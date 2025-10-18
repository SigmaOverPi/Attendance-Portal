<?php
session_start();

//* Redirect to login page if user isn't logged in
if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

//* Include the database connection
require 'includes/db_connect.php';

//* Get the logged-in user's ID
$student_id = $_SESSION['user_id'];

//* Get the logged-in user's full name(for email generation)
$user_sql = "SELECT full_name FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $student_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$student_name = $user['full_name'];

//* Prepare SQL to fetch attendance records for this student
//* We use joins to get data from multiple tables at once
$sql = 'SELECT
            c.course_name,
            s.session_date,
            s.session_type,
            s.notes,
            a.status
        FROM attendance a
        JOIN sessions s ON a.session_id = s.session_id
        JOIN courses c ON s.course_id = c.course_id
        WHERE a.student_id = ?
        ORDER BY s.session_date DESC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cabin:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dash.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="login-container" id="dsah-container">
        <div class="left-side" id="navbar">
            <div>
                <a href="index.php"><img id="navbar-logo" src="resources/ashesilogo.JPG" alt="ashesi-logo"></a>
                <h4 id="navbar-text">ATTENDANCE</h4>
                <h4 id="navbar-text-below">MANAGER</h4>
            </div>

            <nav>
                <a href="dashboard.php"><option value="green">Home</option></a>
                <hr>
            </nav>

            <div style="height: 7%;">
                <a style="color: whitesmoke;" href="#">Help</a>
            </div>
        </div>

        <div style="gap: 10px;" class="right-side">
            <!--* Search bar and icons div -->
            <div class="searchbar-icons">
                <div class="searchbar-div">
                    <form action="#">
                        <input type="search" name="search" id="search-input" placeholder="🔎 Search">
                    </form>
                </div>

                <div class="icons">
                    <img src="resources/notibell.png" alt="notification-bell">
                    <img src="resources/user.png" alt="user-icon">
                </div>
            </div>

            <!--* Selectors div -->
            <div class="students-and-selectors">
                <div class="students">
                    <h2>Attendance</h2>
                </div>
            </div>

            <!--* Main dash div -->
            <div class="main-dash">

                <table id="students-table" cellspacing="3" cellpadding="3" onload="createStudentsTable()">
                    <tr>
                        <th style="text-align: center;">Date</th>
                        <th style="text-align: center;">Course ID</th>
                        <th style="text-align: center;">Session Type</th>
                        <th style="text-align: center;">Attendance</th>
                        <th style="text-align: center;">Notes</th>
                    </tr>
                    <tbody id="students">
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('F j, Y, g:i a', strtotime($row['session_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                                    <td><?php echo ucfirst($row['session_type']); ?></td>
                                    <td class="status-<?php echo strtolower($row['status']); ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['notes']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No attendance records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>

                <div class="right-side" id="right-dash">
                    <div class="right-side-box">
                        <div class="top-side">
                            <p style="display: flex; align-items: center;">Sessions</p>
                            <select name="sessions" id="sessions-selector">
                                <option value="monday">Monday</option>
                            </select>
                        </div>

                        <div class="bottom-side">

                        </div>
                    </div>

                    <div class="right-side-box">
                        <div class="top-side">
                            <p style="display: flex; align-items: center;">Reports</p>
                            <select name="reports" id="reports-selector">
                                <option value="monday">Last 7 Days</option>
                            </select>
                        </div>

                        <div class="bottom-side">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="issue-form">
        <h2>Report an Attendance Issue</h2>
        <p>Use this form to create a template email</p>
        <form id="issueReportForm">
            <label for="issueCourse">Course:</label>
            <input type="text" name="formCourse" id="issueCourse" placeholder="eg. Web Technologies">

            <label for="issueDate">Session Date:</label>
            <input type="date" name="formDate" id="issueDate" required>

            <label for="issueDescription">Description of Issue</label>
            <textarea name="formDescription" id="issueDescription" rows="4" placeholder="Enter description of issue" required></textarea>

            <button type="submit">Create Email Template</button>
        </form>

        <div id="emailTemplate">
            <h3>Your Email Template:</h3>
            <textarea name="formOutput" id="emailOutput" rows="10" readonly></textarea>
        </div>
    </div>

    <script src="./script.js"></script>
    <script>
        document.getElementById('issueReportForm').addEventListener('submit', function(event){
            event.preventDefault();

            const course = document.getElementById('issueCourse').value;
            const sessionDate = document.getElementById('issueDate').value;
            const description = document.getElementById('issueDescription').value;


            const studentName = "<?php echo json_encode($student_name); ?>";
            const studentId = "<?php echo $_SESSION['user_id']; ?>";

            const template = `
            Subject: Attendance Record Inquiry - ${studentName}

            Dear Registrar,

            I hope this email finds you well.

            I am writing to request a review of an attendance record for the following session:
            
            Student Name: ${studentName}
            Student ID: ${studentId}
            Course: ${course}
            Session Date: ${sessionDate}

            Issue: ${description}

            Could you please look into this matter for me? Thank you for your time and assistance.

            Sincerely,
            ${studentName}
            `;

            document.getElementById('emailTemplate').style.display = 'block';
            document.getElementById('emailOutput').value = template.trim();
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
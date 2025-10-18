<?php

//* This is for local development. The live server details are found on my personal sever provided by the school
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'attendance_manager';

//* Create a new connection to db
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

//* Check if connection failed
if ($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
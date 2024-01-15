<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'admin');
$class_id = $_GET['class_id'];
$teacher_id = $_GET['teacher_id'];
$student_id = $_GET['student_id'];
$type = $_GET['level'][0];
$level = $_GET['level'][1];
$details = $_GET['reason'];

$hostname = "localhost";
$username = "root";
$password = "321567@Op";
$database = "datahub";

// Establish connection to the database
$conn = new mysqli($hostname, $username, $password, $database);

$sql = "INSERT INTO consequences (account_id, class_id, teacher_id, type, level, details) VALUE ('$student_id', '$class_id', '$teacher_id', '$type', '$level', '$details');";

$conn->query($sql);

$conn->close();
$s = 'Location: consequences.php';
header($s);
exit();
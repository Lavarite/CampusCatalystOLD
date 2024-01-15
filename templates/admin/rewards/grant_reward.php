<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'admin');
$class_id = $_GET['class_id'];
$teacher_id = $_GET['teacher_id'];
$student_id = $_GET['student_id'];
$type = $_GET['type'];
$volume = $_GET['volume'];
$details = $_GET['reason'];

$hostname = "localhost";
$username = "root";
$password = "321567@Op";
$database = "datahub";

// Establish connection to the database
$conn = new mysqli($hostname, $username, $password, $database);

$sql = "INSERT INTO rewards (account_id, teacher_id, class_id, type, volume, details) VALUE ('$student_id','$teacher_id','$class_id','$type','$volume','$details');";

$conn->query($sql);

$conn->close();
$s = 'Location: rewards.php';
header($s);
exit();
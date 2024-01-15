<?php
include('../../login/session_auth.php');
auth('../../login/login.php', 'admin');
$class_id = (int) $_GET['class_id'];

$hostname = "localhost";
$username = "root";
$password = "321567@Op";
$database = "datahub";

$conn = new mysqli($hostname, $username, $password, $database);

$conn->query("DELETE FROM class_students WHERE class_id = '$class_id'");
$conn->query("DELETE FROM class_teachers WHERE class_id = '$class_id'");
$conn->query("DELETE FROM class_schedule WHERE class_id = $class_id");
$conn->query("DELETE FROM rewards WHERE class_id = '$class_id'");
$conn->query("DELETE FROM consequences WHERE class_id = '$class_id'");
$conn->query("DELETE FROM attendance WHERE class_id = '$class_id'");
$conn->query("DELETE FROM classes WHERE id = '$class_id'");
$conn->close();
header('Location: ../classes.php');
exit();

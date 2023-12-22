<?php
// Database connection
$hostname = "localhost";
$username = "root";
$password = "321567@Op";
$database = "datahub";

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchTerm = isset($_GET['term']) ? $_GET['term'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';

if ($role === 'student' || $role === 'teacher') {
    $sql = "SELECT id, name, surname FROM accounts WHERE (name LIKE '%$searchTerm%' OR surname LIKE '%$searchTerm%') AND role = '$role'";
    $result = $conn->query($sql);

    $people = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $people[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($people);
}

$conn->close();
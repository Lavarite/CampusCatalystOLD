<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'teacher');
$class_id = $_GET['class_id'];
$fields = $_GET['fields'];
$attendanceFields = [];
if (!empty($fields)){
    $entries = explode(';', rtrim($fields, ';'));

    $hostname = "localhost";
    $username = "root";
    $password = "321567@Op";
    $database = "datahub";

// Establish connection to the database
    $conn = new mysqli($hostname, $username, $password, $database);

    foreach ($entries as $entry) {
        $components = explode(',', $entry);

        $date = $components[0];
        $accountId = intval($components[1]);
        $classId = intval($components[2]);
        $status = $components[3];
        $lateMinutes = intval($components[4]);
        $session = intval($components[5]);

        // Check if the record already exists (you might need to adjust this query)
        $checkQuery = "SELECT * FROM attendance WHERE account_id = '$accountId' AND class_id = '$classId' AND date = '$date' AND session = '$session'";
        $checkResult = $conn->query($checkQuery);

        // Update or insert data
        if ($checkResult->num_rows > 0) {
            // Update existing record
            $updateQuery = "UPDATE attendance SET status = '$status', late_minutes = $lateMinutes WHERE account_id = $accountId AND class_id = $classId AND date = '$date' AND session = $session";
            $conn->query($updateQuery);
        } else {
            // Insert new record
            $insertQuery = "INSERT INTO attendance (account_id, class_id, date, status, late_minutes, session) VALUES ('$accountId', '$classId', '$date', '$status', '$lateMinutes', '$session')";
            $conn->query($insertQuery);
        }
    }
    $conn->close();
}
$s = 'Location: class.php?class_id=' . $class_id;
header($s);
exit();
?>
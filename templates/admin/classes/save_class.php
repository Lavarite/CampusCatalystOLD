<?php
include('../../login/session_auth.php');
auth('../../login/login.php', 'admin');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_id = (int) $_POST['class_id'];
    $subject = $_POST['subject'];
    $year = (int) $_POST['year'];
    $set = $_POST['set'];
    $half = $_POST['half'];
    $studentIds = isset($_POST['selectedStudentIds']) ? explode(',', $_POST['selectedStudentIds']) : [];
    $teacherIds = isset($_POST['selectedTeacherIds']) ? explode(',', $_POST['selectedTeacherIds']) : [];

    $code_subject = preg_match('/\s/',$subject) ? strtoupper(implode('', array_map(function($item) { return $item[0]; }, explode(' ', $subject)))) : strtoupper(substr($subject, 0, 2));
    $class_code = $year . $half . '-' . $code_subject . $set;

    $hostname = "localhost";
    $username = "root";
    $password = "321567@Op";
    $database = "datahub";

    $conn = new mysqli($hostname, $username, $password, $database);

    $sql = "UPDATE classes SET
            name = '$subject',
            code = '$class_code',
            year = '$year',
            half = '$half',
            `set` = '$set'
            WHERE id = '$class_id'";

    $conn->query($sql);

    // Delete existing student and teacher associations
    $conn->query("DELETE FROM class_students WHERE class_id = '$class_id'");
    $conn->query("DELETE FROM class_teachers WHERE class_id = '$class_id'");

    foreach ($studentIds as $studentId) {
        $sql = "INSERT INTO class_students (class_id, account_id) VALUES ('$class_id', '$studentId')";
        $conn->query($sql);
    }

    foreach ($teacherIds as $teacherId) {
        $sql = "INSERT INTO class_teachers (class_id, account_id) VALUES ('$class_id', '$teacherId')";
        $conn->query($sql);
    }

    $conn->query("DELETE FROM class_schedule WHERE class_id = $class_id");

    if(!empty($_POST['weekAScheduleData']) and !empty($_POST['weekBScheduleData'])){
        $weekType = 'A';
    }else {
        $weekType = 'Both';
    }

    if (isset($_POST['weekAScheduleData']) and $_POST['weekAScheduleData'] !== '') {
        $weekAScheduleEntries = explode(';', $_POST['weekAScheduleData']);
        foreach ($weekAScheduleEntries as $entry) {
            list($day, $classroom, $startTime, $endTime) = explode(',', $entry);
            $day = (int)$day;
            $sql = "INSERT INTO class_schedule (class_id, day_of_week, session_start, session_end, week, classroom) VALUES ('$class_id', '$day', '$startTime', '$endTime', '$weekType', '$classroom')";
            $conn->query($sql);
        }
    }

    if (isset($_POST['weekBScheduleData']) and $_POST['weekBScheduleData'] !== '') {
        $weekBScheduleEntries = explode(';', $_POST['weekBScheduleData']);
        foreach ($weekBScheduleEntries as $entry) {
            list($day, $classroom, $startTime, $endTime) = explode(',', $entry);
            $day = (int)$day;
            $sql = "INSERT INTO class_schedule (class_id, day_of_week, session_start, session_end, week, classroom) VALUES ('$class_id', '$day', '$startTime', '$endTime', 'B', '$classroom')";
            $conn->query($sql);
        }
    }
    $conn->close();

    header('Location: classes.php');
    exit();
}

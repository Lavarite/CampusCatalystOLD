<?php
include('../../login/session_auth.php');
auth('../../login/login.php', 'admin');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $sql = "INSERT INTO classes (name, code, year) VALUES ('$subject', '$class_code', '$year');";

    $conn->query($sql);

    $lastClassId = $conn->insert_id;

    foreach ($studentIds as $studentId) {
        $sql = "INSERT INTO class_students (class_id, account_id) VALUES ('$lastClassId', '$studentId')";
        $conn->query($sql);
    }

    foreach ($teacherIds as $teacherId) {
        $sql = "INSERT INTO class_teachers (class_id, account_id) VALUES ('$lastClassId', '$teacherId')";
        $conn->query($sql);
    }

    if(isset($_POST['weekAScheduleData']) and isset($_POST['weekBScheduleData'])){
        $weekType = 'A';
    }else {
        $weekType = 'Both';
    }

    if (isset($_POST['weekAScheduleData']) and $_POST['weekAScheduleData'] !== '') {
        $weekAScheduleEntries = explode(';', $_POST['weekAScheduleData']);
        foreach ($weekAScheduleEntries as $entry) {
            list($day, $classroom, $startTime, $endTime) = explode(',', $entry);
            $day = (int)$day;
            $sql = "INSERT INTO class_schedule (class_id, day_of_week, session_start, session_end, week, classroom) VALUES ('$lastClassId', '$day', '$startTime', '$endTime', '$weekType', '$classroom')";
            $conn->query($sql);
        }
    }

    if (isset($_POST['weekBScheduleData']) and $_POST['weekBScheduleData'] !== '') {
        $weekBScheduleEntries = explode(';', $_POST['weekBScheduleData']);
        foreach ($weekBScheduleEntries as $entry) {
            list($day, $classroom, $startTime, $endTime) = explode(',', $entry);
            $day = (int)$day;
            $sql = "INSERT INTO class_schedule (class_id, day_of_week, session_start, session_end, week, classroom) VALUES ('$lastClassId', '$day', '$startTime', '$endTime', 'B', '$classroom')";
            $conn->query($sql);
        }
    }
    $conn->close();

    header('Location: classes.php');
    exit();
}

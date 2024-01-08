<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'teacher');
$class_id = $_GET['class_id'];
$class = getClassFromId($class_id);
$students = getStudents($class_id);
$teachers = getTeachers($class_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="icon" href="../../../presets/favicon.png" type="image/png">
    <link href="class.css" rel="stylesheet" type="text/css">
    <link href="attendance.css" rel="stylesheet" type="text/css">
    <link href="../header/header.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>$(function(){$(".header").load("../header/header.html")});</script>
</head>

<!-- Header -->
<div class="header"></div>

<body>
<div class="lists-container">
    <div id="student-list">
        <h3>Students</h3>
        <button class="regular-btn" onclick="showAttendance()" style="align-self: ">Check Attendance</button>
        <?php if (!empty($students)):?>
            <?php foreach ($students as $student): ?>
                <div class="student-entry" data-id="<?= $student['id']?>" onclick="">
                    <span><?= $student['name'] . ' ' . $student['surname']?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="class-detail-banner">
        <div class="class-details">
            <h1 id="class-name"><?= $class['subject']?></h1>
            <p id="class-code"><?= $class['code']?></p>
        </div>
    </div>

    <div id="teacher-list">
        <h3>Teachers</h3>
        <?php if (!empty($teachers)):?>
            <?php foreach ($teachers as $teacher): ?>
                <div class="teacher-entry" data-id="<?= $teacher['id']?>" onclick="">
                    <span><?= $teacher['name'] . ' ' . $teacher['surname']?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div id="attendanceForm" class="popup-form"></div>
</body>

<script>
    function showAttendance() {
        var class_id = <?= $class_id?>;
        $.ajax({
            url: "attendance.php",
            type: "GET",
            data: { class_id: class_id },
            success: function(data) {
                // Show the edit form with the data received
                $("#attendanceForm").html(data).show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error loading form: " + textStatus, errorThrown);
            }
        });
    }
</script>
</html>
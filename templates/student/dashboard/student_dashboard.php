<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'student');
$classes = getLessons('student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="icon" href="../../../presets/favicon.png" type="image/png">
    <link href="student_dashboard.css" rel="stylesheet" type="text/css">
    <link href="../header/header.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>$(function(){$(".header").load("../header/header.html")});</script>
</head>

<!-- Header -->
<div class="header"></div>

<body>

<H2>Schedule for <?= htmlspecialchars(date('D jS'))?></H2>

<div class="schedule-graph">
    <!-- Time Labels -->
    <div class="time-labels">
        <?php for ($hour = 8; $hour <= 17; $hour++): ?>
            <div class="time-label" style="top: <?= ($hour - 8) * 60 ?>px;">
                <span class="hour"><?= str_pad($hour, 2, '0', STR_PAD_LEFT) ?>:00</span>
            </div>
        <?php endfor; ?>
    </div>
    <!-- Classes -->
    <div class="classes-container">
        <?php foreach ($classes as $class): ?>
            <?php
            // Calculate the position and height of each class block
            $startTime = strtotime($class['session_start']);
            $endTime = strtotime($class['session_end']);
            $duration = ($endTime - $startTime) / 60; // Duration in minutes
            list($hours, $minutes) = explode(':', date('H:i', $startTime));
            $topPosition = ($hours * 60 + $minutes - 8 * 60); // Position from 8:00AM
            $height = $duration; // Height proportional to duration
            ?>
            <div class="class-block" style="top: <?= $topPosition ?>px; height: <?= $height ?>px;" onclick="window.location.href = '../class/class.php?class_id=<?=$class['id']?>'">
                <span class="class-name"><?= htmlspecialchars($class['name']) ?></span>
                <span class="class-time"><?= htmlspecialchars(date('H:i', $startTime)) ?> - <?= htmlspecialchars(date('H:i', $endTime)) ?></span>
                <span class="class-name"><?= htmlspecialchars($class['classroom']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</div>
</body>
</html>

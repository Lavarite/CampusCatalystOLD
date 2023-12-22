<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'student');
$todaysClasses = getLessons('student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="student_dashboard.css" rel="stylesheet" type="text/css">
</head>
<body>

<!-- Header -->
<div class="header">
    <!-- Main Dropdown Menu -->
    <div class="dropdown">
        <button class="dropbtn">Menu</button>
        <ul class="dropdown-content">
            <li>Option 1</li>
            <li>Option 2</li>
            <li>Option 3</li>
        </ul>
    </div>

    <!-- Spacer -->
    <div class="spacer"></div>

    <!-- Profile Picture Button -->
    <div class="dropdown">
        <button class="profilebtn">
            <img src="../../../presets/profile_picture.png" class="profile-pic" alt="Profile">
        </button>
        <ul class="dropdown-content-user">
            <li onclick="window.location.href = 'student_dashboard.php'">Dashboard</li>
            <li onclick="window.location.href = '../../login/login.php'">Log Out</li>
        </ul>
    </div>
</div>

<div class="date-label">Schedule for <?php echo date('j') . 'th ' . date('F')?></div>
<div class="schedule-graph">
    <div class="time-labels">
        <!-- Generate time labels for 8:00 to 17:00 -->
        <?php for($hour = 8; $hour <= 17; $hour++): ?>
            <div class="time-label" style="top: <?= ($hour - 8) * 60 ?>px;">
                <span class="hour"><?= $hour ?>:00</span>
                <span class="hour-tick" style="translate: 200% -32px"></span>
            </div>
        <?php endfor; ?>

    </div>
    <div class="classes-container">
        <?php foreach ($todaysClasses as $class): ?>
            <?php
            $startTime = strtotime($class['session_start']);
            $endTime = strtotime($class['session_end']);
            $duration = ($endTime - $startTime) / 3600; // Duration in hours
            list($hours, $minutes) = explode(':', $class['session_start']);
            $startTimeInMinutes = $hours * 60 + $minutes - 8 * 60; // Subtract 8 hours worth of minutes
            $topPosition = $startTimeInMinutes; // Convert hours to 'top' position
            $height = $duration * 60; // Convert duration to height
            ?>
            <div class="class-block" style="top: <?= $topPosition ?>px; height: <?= $height ?>px;">
                <span class="class-name"><?= htmlspecialchars($class['name']) ?></span>
                <span class="class-time"><?= htmlspecialchars(date('H:i', strtotime($class['session_start']))) ?> - <?= htmlspecialchars(date('H:i', strtotime($class['session_end']))) ?></span>
                <span class="class-room"><?= htmlspecialchars($class['classroom'])?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <?php
    $height = 1;
    // Get the current time
    $currentTime = new DateTime();
    $graphStartTime = clone $currentTime;
    $graphEndTime = clone $currentTime;
    $graphStartTime->setTime(9, 0);
    $graphEndTime->setTime(18, 0);
    $minutesSinceStartOfDay = 0;
    if ($currentTime >= $graphStartTime and $currentTime <= $graphEndTime) {
        $interval = $currentTime->diff($graphStartTime);
        $minutesSinceStartOfDay = ($interval->h * 60) + $interval->i;
    }else {
        $height = 0;
    }
    $topPosition = $minutesSinceStartOfDay;
    ?>
    <div class="current-time-marker" style="height: <?= $height ?>px;top: <?= $topPosition ?>px;"></div>

</div>
</body>
</html>

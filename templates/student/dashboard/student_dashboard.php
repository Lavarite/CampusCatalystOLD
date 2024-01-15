<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'student');
$classes = getLessons('student');
$date = date('Y-m-d');
$id = getId();
getRewardsStudent($id, false, 1);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../rewards/hp-chart.js"></script>
    <script>
        $(function(){$(".header").load("../header/header.html")});
    </script>
</head>

<!-- Header -->
<header class="header"></header>

<body>

<div class="schedule-container">
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
        <div class="day-bar" onclick="toggleScheduleDetails('<?= $date ?>')">
            <div class="date-info"><?= date('D jS', strtotime($date)) ?></div>
            <div class="expand-icon">+</div>
        </div>

        <!-- Compact View -->
        <div class="compact-view">
            <?php foreach ($classes as $class): ?>
                <div class="class-item" onclick="window.location.href = '../class/class.php?class_id=<?=$class['id']?>'">
                    <div class="class-subject"><?= htmlspecialchars($class['name']) ?></div>
                    <div class="class-room-time">
                        <span><?= htmlspecialchars($class['classroom']) ?></span>
                        <span><?= htmlspecialchars(date('H:i', strtotime($class['session_start'])))?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Detailed View (initially hidden) -->
        <!-- Detailed View (initially hidden) -->
        <div id="details-<?= $date ?>" class="detailed-view" style="display: none;">
            <div class="schedule-graph">
                <!-- Time Labels -->
                <div class="time-labels">
                    <?php for ($hour = 8; $hour <= 16; $hour++): ?>
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
    </div>


</div>

<div class="house-points-container" onclick="window.location.href = '../rewards/rewards.php'">
    <div class="house-points" id="house-points">
        <label for="house-points-chart-values" style="font: bold 20px sans-serif;">House Points given for</label>
        <canvas id="house-points-chart-values" style="margin-top: 10px"></canvas>

        <label for="house-points-chart-subject" style="font: bold 20px sans-serif;">House Points given in</label>
        <canvas id="house-points-chart-subject" style="margin-top: 10px"></canvas>
    </div>
</div>

<script>
    function toggleScheduleDetails(date) {
        var detailedView = $('#details-' + date);
        var compactView = detailedView.siblings('.compact-view');
        var allDetailedViews = $('.detailed-view').not(detailedView);
        var allCompactViews = $('.compact-view').not(compactView);

        // Slide up any other detailed views that are open
        allDetailedViews.slideUp(function() {
            allCompactViews.slideDown(); // Ensure compact views are shown after other details are hidden
        });

        // Toggle the clicked one
        detailedView.slideToggle();
        compactView.slideToggle();
    }
</script>

</body>
</html>

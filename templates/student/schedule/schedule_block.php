<?php
include('../../../presets/getset.php');
$schedules = getLessonsInterval(14, 'student');
?>

<div class="schedule-container">
    <?php foreach ($schedules as $date => $classes): ?>
        <div class="day-schedule">
            <!-- Day Bar -->
            <div class="day-bar" onclick="toggleScheduleDetails('<?= $date ?>')">
                <div class="date-info"><?= date('D jS', strtotime($date)) ?></div>
                <div class="expand-icon">+</div>
            </div>

            <!-- Compact View -->
            <div class="compact-view">
                <?php foreach ($classes as $class): ?>
                    <div class="class-item">
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
                            <div class="class-block" style="top: <?= $topPosition ?>px; height: <?= $height ?>px;">
                                <span class="class-name"><?= htmlspecialchars($class['name']) ?></span>
                                <span class="class-time"><?= htmlspecialchars(date('H:i', $startTime)) ?> - <?= htmlspecialchars(date('H:i', $endTime)) ?></span>
                                <span class="class-name"><?= htmlspecialchars($class['classroom']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    <?php endforeach; ?>
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


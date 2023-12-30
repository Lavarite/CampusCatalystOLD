<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="schedule.css" rel="stylesheet" type="text/css">
    <link href="schedule_block.css" rel="stylesheet" type="text/css">
    <link href="../header/header.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function(){$(".header").load("../header/header.html")});
        $(function(){$("#schedule-list").load("schedule_block.php")});
    </script>
</head>
<body>

<!-- Header -->
<div class="header"></div>

<div id="schedule-list"></div>
</body>
</html>

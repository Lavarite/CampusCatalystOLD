<?php
include('../../login/session_auth.php');
auth('../../login/login.php', 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="admin_dashboard.css" rel="stylesheet" type="text/css">
    <link href="../header/header.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>$(function(){$(".header").load("../header/header.html")});</script>
</head>
<body>

<!-- Header -->
<div class="header"></div>
</body>
</html>
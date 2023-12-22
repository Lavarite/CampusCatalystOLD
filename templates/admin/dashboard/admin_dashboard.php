<?php
include('../../login/session_auth.php');
auth('../../login/login.php', 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="admin_dashboard.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="header">
    <!-- Main Dropdown Menu -->
    <div class="dropdown">
        <button class="dropbtn">Menu</button>
        <ul class="dropdown-content">
            <li onclick="window.location.href = '../classes/classes.php'">Classes</li>
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
            <li onclick="window.location.href = 'admin_dashboard.php'">Dashboard</li>
            <li onclick="window.location.href = '../../login/login.php'">Log Out</li>
        </ul>
    </div>
</div>
</body>
</html>
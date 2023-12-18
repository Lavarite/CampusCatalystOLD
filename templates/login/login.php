<?php
setcookie("updateToken", "", time() - 3600, "/");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <form action="login_auth.php" method="post">
        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="input-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div id="error-message"></div>
        <button type="submit" name="login">Login</button>
        <button type="button" onclick="window.location = 'google_login.php';" class="google-btn">
            Log in with Google
        </button>
    </form>
</div>
<script>
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        if (error) {
            document.getElementById('error-message').textContent = 'Incorrect email or password.';
        }
    };
</script>
</body>
</html>

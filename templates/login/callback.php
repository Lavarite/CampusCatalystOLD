<?php
session_start();
require_once '../../google-api-php-client-2.4.0/vendor/autoload.php';

// Configuration - Replace with your details
$clientId = '650088888366-62bjkkspfv141i8rk0auhk2ocigm5kul.apps.googleusercontent.com'; // Replace with your client ID
$clientSecret = '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'; // Replace with your client secret
$redirectUri = 'http://localhost/projects/CampusCatalyst/templates/login/callback.php';

// Create Client
$client = new Google_Client();
$client->setClientId($clientId);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("https://www.googleapis.com/auth/userinfo.email");

// Disable SSL verification
$client->setHttpClient(new GuzzleHttp\Client(['verify' => false]));

$host = 'localhost'; // Host name
$username = 'root'; // MySQL username
$password = '321567@Op'; // MySQL password
$db_name = 'accounts'; // Database name

// Connect to server and select database.
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Authenticate and Exchange Code for Token
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
}

// Check if we have an access token
if ($client->getAccessToken()) {
    // Retrieve user information
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email =  $google_account_info->email;
    $name =  $google_account_info->name;

    $sql = "SELECT * FROM accounts WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $role = $row['role'];
        $id = $row['id'];

        $temp = sha1(rand());
        $tokenValue = substr($temp, 0, 16);
        $cookieResult = $conn->query("UPDATE accounts SET CookiePass='$tokenValue' WHERE id='$id'");
        setcookie('updateToken', $tokenValue, time() + (86400 * 30), "/");

        if ($role == 'student') {
            header('Location: ../student/dashboard/student_dashboard.php');
            exit();
        } elseif ($role == 'teacher') {
            header('Location: ../teacher/dashboard/teacher_dashboard.php');
            exit();
        } elseif ($role == 'admin') {
            header('Location: ../admin/dashboard/admin_dashboard.php');
            exit();
        }
    } else {
        // Redirect back to the login page with an error flag
        header("Location: login.php?error=1");
        exit();
    }
} else {
    // No valid token available, redirect to login
    header('Location: login.php');
    exit();
}
?>

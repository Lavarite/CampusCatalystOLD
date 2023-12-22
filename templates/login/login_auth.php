<?php
session_start();

$host = 'localhost'; // Host name
$username = 'root'; // MySQL username
$password = '321567@Op'; // MySQL password
$db_name = 'DataHub'; // Database name

// Connect to server and select database.
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if email and password are set from the form data
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // This should be hashed

    // To protect from MySQL injection
    $email = stripslashes($email);
    $password = stripslashes($password);
    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);

    $sql = "SELECT * FROM accounts WHERE email='$email' AND password='$password'"; // Password should be hashed
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $role = $row['role'];
        $id = $row['id'];

        $temp = sha1(rand());
        $tokenValue = substr($temp, 0, 16);
        $cookieResult = $conn->query("UPDATE accounts SET token='$tokenValue' WHERE id='$id'");
        setcookie('token', $tokenValue, time() + (86400 * 30), "/");

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
    // Redirect back to the login page without processing
    header("Location: login.php");
    exit();
}

$conn->close();
?>


<?php
session_start();

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

        // Output role to the command line
        echo "Logged in as: " . $role . "\n";
    } else {
        // Redirect back to the login page with an error flag
        header("Location: login.html?error=1");
        exit();
    }
} else {
    // Redirect back to the login page without processing
    header("Location: login.html");
    exit();
}

$conn->close();
?>


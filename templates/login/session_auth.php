<?php
function auth($path, $role)
{
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
    if (isset($_COOKIE['token'])) {

        $tokenValue = $_COOKIE['token'];
        $result = $conn->query("SELECT * FROM accounts WHERE token='$tokenValue'");

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['role']!=$role){
                header('Location: ' . $path);
                return 1;
            }
            return 0;
        } else {
            header('Location: ' . $path);
            return 1;
        }
    }else {
        header('Location: ' . $path);
        return 1;
    }
}

?>
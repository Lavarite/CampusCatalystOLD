<?php
function getId()
{
    $token = $_COOKIE['token'];
    $host = 'localhost'; // Host name
    $username = 'root'; // MySQL username
    $password = '321567@Op'; // MySQL password
    $db_name = 'DataHub'; // Database name

    $conn = new mysqli($host, $username, $password, $db_name);

    $query = "SELECT id FROM accounts WHERE token = '$token'";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $studentId = $row['id'];
        return $studentId;
    }
    return -1;
}

function getAccount()
{
    $token = $_COOKIE['token'];
    $host = 'localhost'; // Host name
    $username = 'root'; // MySQL username
    $password = '321567@Op'; // MySQL password
    $db_name = 'DataHub'; // Database name

    $conn = new mysqli($host, $username, $password, $db_name);

    $query = "SELECT * FROM accounts WHERE token = '$token'";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return '';
}

//get the lessons for a given day
function getLessons($role, $day='', $month='', $year='', $id='')
{
    $host = 'localhost'; // Host name
    $username = 'root'; // MySQL username
    $password = '321567@Op'; // MySQL password
    $db_name = 'DataHub'; // Database name
    $conn = new mysqli($host, $username, $password, $db_name);

    $day = empty($day) ? date('j') : $day;
    $month = empty($month) ? date('m') : $month;
    $year = empty($year) ? date('Y') : $year;

    $dateString = sprintf('%04d-%02d-%02d', $year, $month, $day);
    $timestamp = strtotime($dateString);
    $set_day = date('N', $timestamp);
    $weekOfYear = date('W', $timestamp);
    $weekType = ($weekOfYear % 2 === 1) ? 'B' : 'A';

    $accountIdWhere = empty($id) ? '' : "cls.account_id = '$id' AND";
    $class_role = ($role == 'student') ?  'class_students' : 'class_teachers';
    $query = "SELECT c.id, c.name, cs.day_of_week, cs.session_start, cs.session_end, cs.classroom 
          FROM class_schedule cs
          INNER JOIN classes c ON cs.class_id = c.id
          INNER JOIN $class_role cls ON c.id = cls.class_id
          WHERE " . $accountIdWhere . " cs.day_of_week = '$set_day' AND (cs.week = '$weekType' OR cs.week = 'Both')
          ORDER BY cs.session_start";

    $result = $conn->query($query);

    if (!$result) {
        die("Error: " . $conn->error);
    }

    $todaysClasses = [];
    while ($row = $result->fetch_assoc()) {
        $todaysClasses[] = $row;
    }

    $conn->close();
    return $todaysClasses;
}

function getLessonsInterval($interval, $role, $date='', $id='')
{
    $id = empty($id) ? getId() : $id;
    $dates = [];
    $currentDate = empty($date) ? new DateTime() : $date; // start from today
    for ($i = 0; $i < $interval; $i++) {
        $dates[] = clone $currentDate;
        $currentDate->modify('+1 day');
    }

    // Fetch schedules for each day
    $schedules = [];
    foreach ($dates as $date) {
        $schedules[$date->format('Y-m-d')] = getLessons($role, $date->format('j'), $date->format('n'), $date->format('Y'), $id);
    }
    return $schedules;
}

function getClassLessons($day='', $month='', $year='', $id='')
{
    $host = 'localhost'; // Host name
    $username = 'root'; // MySQL username
    $password = '321567@Op'; // MySQL password
    $db_name = 'DataHub'; // Database name
    $conn = new mysqli($host, $username, $password, $db_name);

    $day = empty($day) ? date('j') : $day;
    $month = empty($month) ? date('m') : $month;
    $year = empty($year) ? date('Y') : $year;

    $dateString = sprintf('%04d-%02d-%02d', $year, $month, $day);
    $timestamp = strtotime($dateString);
    $set_day = date('N', $timestamp);
    $weekOfYear = date('W', $timestamp);
    $weekType = ($weekOfYear % 2 === 1) ? 'B' : 'A';

    $classIdWhere = empty($id) ? '' : "c.id = '$id' AND";
    $query = "SELECT c.id, c.name, cs.day_of_week, cs.session_start, cs.session_end, cs.classroom 
          FROM class_schedule cs
          INNER JOIN classes c ON cs.class_id = c.id
          WHERE " . $classIdWhere . " cs.day_of_week = '$set_day' AND (cs.week = '$weekType' OR cs.week = 'Both')
          ORDER BY cs.session_start";

    $result = $conn->query($query);

    if (!$result) {
        die("Error: " . $conn->error);
    }

    $todaysClasses = [];
    while ($row = $result->fetch_assoc()) {
        $todaysClasses[] = $row;
    }

    $conn->close();
    return $todaysClasses;
}


function getClassLessonsInterval($interval, $id, $date='')
{
    $dates = [];
    $currentDate = empty($date) ? new DateTime() : $date; // start from today
    for ($i = 0; $i < $interval; $i++) {
        $dates[] = clone $currentDate;
        $currentDate->modify('+1 day');
    }

    // Fetch schedules for each day
    $schedules = [];
    foreach ($dates as $date) {
        $schedules[$date->format('Y-m-d')] = getClassLessons($date->format('j'), $date->format('n'), $date->format('Y'), $id);
    }
    return $schedules;
}

function getFilteredClasses()
{
    // Database connection variables
    $hostname = "localhost";
    $username = "root";
    $password = "321567@Op";
    $database = "datahub";

// Establish connection to the database
    $conn = new mysqli($hostname, $username, $password, $database);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

// Initialize the WHERE clause array
    $whereClauses = [];

// Retrieve filter values from GET parameters
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $year = isset($_GET['year']) ? $_GET['year'] : '';
    $teacher = isset($_GET['teacher']) ? $_GET['teacher'] : '';
    $student = isset($_GET['student']) ? $_GET['student'] : '';
    $code = isset($_GET['code']) ? $_GET['code'] : '';

// Add search condition for subject
    if (!empty($search)) {
        $whereClauses[] = "c.name LIKE '%" . $conn->real_escape_string($search) . "%'";
    }

// Add year condition for study
    if (!empty($year)) {
        $whereClauses[] = "c.year = " . (int)$year;
    }

// Add teacher condition for name/surname
    if (!empty($teacher)) {
        $whereClauses[] = "(a.name LIKE '%" . $conn->real_escape_string($teacher) . "%' OR a.surname LIKE '%" . $conn->real_escape_string($teacher) . "%')";
    }

    if (!empty($student)) {
        $whereClauses[] = "(b.name LIKE '%" . $conn->real_escape_string($student) . "%' OR b.surname LIKE '%" . $conn->real_escape_string($student) . "%')";
    }

// Add code condition for class code
    if (!empty($code)) {
        $whereClauses[] = "c.code LIKE '%" . $conn->real_escape_string($code) . "%'";
    }

// Combine all WHERE clauses into a single string
    $where = !empty($whereClauses) ? ' WHERE ' . implode(' AND ', $whereClauses) : '';

// Build the SQL query with optional WHERE clause
    $sql = "SELECT c.id, c.name, c.code, 
       (SELECT COUNT(*) FROM class_students WHERE class_id = c.id) as student_count, 
       c.year
        FROM classes c
        LEFT JOIN class_teachers ct ON c.id = ct.class_id
        LEFT JOIN accounts a ON ct.account_id = a.id
        LEFT JOIN class_students cs ON c.id = cs.class_id
        LEFT JOIN accounts b ON cs.account_id = b.id" . $where .
        " GROUP BY c.id";;

// Execute the SQL query
    $result = $conn->query($sql);

// Initialize an array to store the class information
    $classes = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Since we need teacher names per class, we have to query inside the loop
            $teacher_sql = "SELECT a.name, a.surname
                        FROM accounts a
                        JOIN class_teachers ct ON a.id = ct.account_id
                        WHERE ct.class_id = " . $row['id'] . " AND a.role = 'teacher'";

            $teacher_result = $conn->query($teacher_sql);

            $teachers = [];
            while ($teacher = $teacher_result->fetch_assoc()) {
                $teachers[] = substr($teacher['name'], 0, 1) . ' ' . $teacher['surname'];
            }
            $classes[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'year' => $row['year'],
                'code' => $row['code'],
                'teachers' => implode(', ', $teachers), // Join teacher names with comma
                'student_count' => $row['student_count']
            ];
        }
    }

// Close the database connection
    $conn->close();
    return $classes;
}

function getClassFromId($id)
{
    // Database connection variables
    $hostname = "localhost";
    $username = "root";
    $password = "321567@Op";
    $database = "datahub";

// Establish connection to the database
    $conn = new mysqli($hostname, $username, $password, $database);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $classDetails = [];
    $studentIds = [];
    $teacherIds = [];
    $scheduleData = [];

// 1. Retrieve class details
    $sql = "SELECT * FROM classes WHERE id = '$id';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $classDetails = $result->fetch_assoc();
    }

// 2. Fetch associated students
    $sql = "SELECT account_id FROM class_students WHERE class_id = '$id';";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $studentIds[] = $row['account_id'];
    }

// 3. Fetch associated teachers
    $sql = "SELECT account_id FROM class_teachers WHERE class_id = '$id';";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $teacherIds[] = $row['account_id'];
    }

// 4. Fetch class schedule
    $sql = "SELECT * FROM class_schedule WHERE class_id = '$id';";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $scheduleData[] = $row;
    }

    $return_class = [
        'subject' => $classDetails['name'],
        'code' => $classDetails['code'],
        'year' => $classDetails['year'],
        'half' => $classDetails['half'],
        'set' => $classDetails['set'],
        'teacherIds' => $teacherIds,
        'studentIds' => $studentIds,
        'scheduleData' => $scheduleData
    ];

// Close the connection
    $conn->close();
    return $return_class;
}

function getStudents($class_id)
{
    $students = [];
    $host = 'localhost'; // Host name
    $username = 'root'; // MySQL username
    $password = '321567@Op'; // MySQL password
    $db_name = 'DataHub'; // Database name

    $conn = new mysqli($host, $username, $password, $db_name);

    $query = "SELECT a.id, a.name, a.surname, a.email FROM accounts a INNER JOIN class_students cs ON a.id = cs.account_id WHERE cs.class_id = '$class_id'";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        return $students;
    }
    return 0;
}

function getTeachers($class_id)
{
    $teachers = [];
    $host = 'localhost'; // Host name
    $username = 'root'; // MySQL username
    $password = '321567@Op'; // MySQL password
    $db_name = 'DataHub'; // Database name

    $conn = new mysqli($host, $username, $password, $db_name);

    $query = "SELECT a.id, a.name, a.surname, a.email FROM accounts a INNER JOIN class_teachers cs ON a.id = cs.account_id WHERE cs.class_id = '$class_id'";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $teachers[] = $row;
        }
        return $teachers;
    }
    return 0;
}

function getAccountNames()
{
    $names = [];
    $host = 'localhost'; // Host name
    $username = 'root'; // MySQL username
    $password = '321567@Op'; // MySQL password
    $db_name = 'DataHub'; // Database name

    $conn = new mysqli($host, $username, $password, $db_name);

    $query = "SELECT id, name, surname FROM accounts";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $names[$row['id']] = $row['name'] . ' ' . $row['surname'];
        }
        return $names;
    }
    return [];
}

function getAccountName($id)
{
    $host = 'localhost'; // Host name
    $username = 'root'; // MySQL username
    $password = '321567@Op'; // MySQL password
    $db_name = 'DataHub'; // Database name

    $conn = new mysqli($host, $username, $password, $db_name);

    $query = "SELECT name, surname FROM accounts WHERE id = '$id'";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'] . ' ' . $row['surname'];
    }
    return '';
}

function getAttendance($date, $session, $class_id, $account_id)
{
    $host = 'localhost'; // Host name
    $username = 'root'; // MySQL username
    $password = '321567@Op'; // MySQL password
    $db_name = 'DataHub'; // Database name

    $conn = new mysqli($host, $username, $password, $db_name);

    $query = "SELECT status, attendance_id FROM attendance WHERE date = '$date' AND session = '$session' AND class_id = '$class_id' AND account_id = '$account_id'";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return '';
}

function getRewardsStudent($student_id, $return, $greater = false, $type = '', $name = '')
{
    $type_str = '';
    if (!empty($type)) {
        if ($greater) {
            $type_str = " type >= '$type' AND ";
        }else {
            $type_str = " type = '$type' AND ";
        }
    }
    $hostname = "localhost";
    $username = "root";
    $password = "321567@Op";
    $database = "datahub";

    $conn = new mysqli($hostname, $username, $password, $database);

// Fetch rewards data of a specific type and student
    $sql = "SELECT details, volume, class_id, date, teacher_id, type FROM rewards WHERE" . $type_str . " account_id = '$student_id'";
    $result = $conn->query($sql);

    $rewards_data = [];
    while($row = $result->fetch_assoc()) {
        $rewards_data[] = ['details' => $row['details'], 'volume' => $row['volume'], 'subject' => getClassFromId($row['class_id'])['subject'], 'date' => $row['date'], 'teacher_id' => $row['teacher_id'], 'type' => $row['type']];
    }

// Encode rewards data to JSON format
    $conn->close();
    if ($return){
        return $rewards_data;
    } else{
        $data = json_encode($rewards_data);
        if (!empty($name)){
            echo "<script>var $name = $data;</script>";
        }else {
            echo "<script>var data = $data;</script>";
        }
    }
}

function getConsequencesStudent($student_id, $return, $name = '', $type = '' )
{
    $type_str = '';
    if (!empty($type)) {
        $type_str = " type = '$type' AND ";
    }
    $hostname = "localhost";
    $username = "root";
    $password = "321567@Op";
    $database = "datahub";

    $conn = new mysqli($hostname, $username, $password, $database);

// Fetch rewards data of a specific type and student
    $sql = "SELECT teacher_id, class_id, date, details, type, level FROM consequences WHERE" . $type_str . " account_id = '$student_id'";
    $result = $conn->query($sql);

    $rewards_data = [];
    while($row = $result->fetch_assoc()) {
        $rewards_data[] = ['details' => $row['details'], 'level' => $row['level'], 'subject' => getClassFromId($row['class_id'])['subject'], 'date' => $row['date'], 'teacher_id' => $row['teacher_id'], 'type' => $row['type']];
    }

// Encode rewards data to JSON format
    $conn->close();
    if ($return){
        return $rewards_data;
    } else{
        $data = json_encode($rewards_data);
        if (!empty($name)){
            echo "<script>var $name = $data;</script>";
        }else {
            echo "<script>var data = $data;</script>";
        }
    }
}

function getStudentAccounts()
{
    $hostname = "localhost";
    $username = "root";
    $password = "321567@Op";
    $database = "datahub";

    $conn = new mysqli($hostname, $username, $password, $database);

// Fetch rewards data of a specific type and student
    $sql = "SELECT id, name, surname FROM accounts WHERE role = 'student'";
    $result = $conn->query($sql);

    $students = [];
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    return $students;
}

function getFilteredStudentAccounts()
{
    $hostname = "localhost";
    $username = "root";
    $password = "321567@Op";
    $database = "datahub";

    $conn = new mysqli($hostname, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $whereClauses = [];

    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $surname = isset($_GET['surname']) ? $_GET['surname'] : '';
    $email = isset($_GET['email']) ? $_GET['email'] : '';

    if (!empty($email)) {
        $whereClauses[] = "email LIKE '%" . $conn->real_escape_string($email) . "%'";
    }

    if (!empty($name)) {
        $whereClauses[] = "name LIKE '%" . $conn->real_escape_string($name) . "%'";
    }

    if (!empty($surname)) {
        $whereClauses[] = "surname LIKE '%" . $conn->real_escape_string($surname) . "%'";
    }

    $where = !empty($whereClauses) ? 'WHERE ' . implode(' AND ', $whereClauses) . " AND role = 'student'": "WHERE role = 'student'";

    $sql = "SELECT id, name, surname FROM accounts " . $where;
    $result = $conn->query($sql);

    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $conn->close();
    return $data;
}
?>

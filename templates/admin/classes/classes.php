<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'admin');

$search = isset($_GET['search']) ? $_GET['search'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$teacher = isset($_GET['teacher']) ? $_GET['teacher'] : '';
$student = isset($_GET['student']) ? $_GET['student'] : '';
$code = isset($_GET['code']) ? $_GET['code'] : '';

// Call the function with filter values
$classes = getFilteredClasses();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="icon" href="../../../presets/favicon.png" type="image/png">
    <link href="classes.css" rel="stylesheet" type="text/css">
    <link href="create_class_form.css" rel="stylesheet" type="text/css">
    <link href="../header/header.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>$(function(){$(".header").load("../header/header.html")});</script>
</head>
<body>

<!-- Header -->
<div class="header"></div>

<div class="filtered-classes">
    <div class="filter-bar">
        <form method="get">
            <div>
                <label for="search">Subject:</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div>
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($year); ?>">
            </div>
            <div>
                <label for="teacher">Teacher:</label>
                <input type="text" id="teacher" name="teacher" value="<?php echo htmlspecialchars($teacher); ?>">
            </div>
            <div>
                <label for="teacher">Student:</label>
                <input type="text" id="student" name="student" value="<?php echo htmlspecialchars($student); ?>">
            </div>
            <div>
                <label for="code">Code:</label>
                <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($code); ?>">
            </div>
            <div>
                <button type="submit">Filter</button>
                <button type="button" onclick="window.location.href = 'classes.php';">Reset Filters</button>
            </div>
        </form>
    </div>

    <div class="class-table">
        <div class="scroll">
            <table>
                <tr>
                    <th>Subject</th>
                    <th>Year</th>
                    <th>Class Code</th>
                    <th>Teacher(s)</th>
                    <th>Student Count</th>
                </tr>
                <?php foreach ($classes as $class): ?>
                    <tr class="table-row" data-id="<?php echo htmlspecialchars($class['id']); ?>">
                        <td><?php echo htmlspecialchars($class['name']); ?></td>
                        <td><?php echo htmlspecialchars($class['year']); ?></td>
                        <td><?php echo htmlspecialchars($class['code']); ?></td>
                        <td><?php echo htmlspecialchars($class['teachers']); ?></td>
                        <td><?php echo htmlspecialchars($class['student_count']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <!-- Create Button -->
    <button id="createButton" class="create-btn">Create Class</button>

    <!-- Pop-up Form (initially hidden) -->
    <div id="createForm" class="popup-form"></div>
    <div id="editForm" class="popup-form"></div>

<script>
    function showCreateForm() {
        $("#createForm").load("create_class_form.html", function() {
            $("#createForm").show();
        });
    }

    function showEditForm(event) {
        // Find the closest row to the event target
        var row = event.target.closest('.table-row');
        if (!row) return; // Exit if no row was clicked

        var class_id = row.getAttribute('data-id');
        $.ajax({
            url: "edit_class_form.php",
            type: "GET",
            data: { class_id: class_id },
            success: function(data) {
                // Show the edit form with the data received
                $("#editForm").html(data).show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error loading form: " + textStatus, errorThrown);
            }
        });
    }

    document.getElementById('createButton').addEventListener('click', showCreateForm);
    document.querySelector('.class-table table').addEventListener('click', showEditForm);
</script>
</body>
</html>
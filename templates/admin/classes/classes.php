<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'admin');

$search = isset($_GET['search']) ? $_GET['search'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$teacher = isset($_GET['teacher']) ? $_GET['teacher'] : '';
$code = isset($_GET['code']) ? $_GET['code'] : '';

// Call the function with filter values
$classes = getFilteredClasses();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="classes.css?v=1" rel="stylesheet" type="text/css">
</head>
<body>
<div class="header">
    <!-- Main Dropdown Menu -->
    <div class="dropdown">
        <button class="dropbtn">Menu</button>
        <ul class="dropdown-content">
            <li onclick="window.location.href = 'classes.php'">Classes</li>
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
                    <tr>
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
    <div id="popupForm" class="popup-form">
        <div class="form-flex-container">
            <div class="teacher-container">
                <h3>Add Teachers</h3>
                <input type="text" id="teacherName" name="teacherName" onkeyup="searchPeople('teacher')">
                <div id="teacherSearchResults"></div>
                <div id="selectedTeachers"></div>
            </div>
            <div class="form-container">
                <form method="post" action="create_class.php">

                    <h2>Create New Class</h2>

                    <input type="hidden" id="selectedStudentIds" name="selectedStudentIds">
                    <input type="hidden" id="selectedTeacherIds" name="selectedTeacherIds">
                    <input type="hidden" id="weekAScheduleData" name="weekAScheduleData">
                    <input type="hidden" id="weekBScheduleData" name="weekBScheduleData">

                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" required>

                    <label for="year">Year of Study:</label>
                    <input type="number" id="year" name="year" min="1" max="13" required>

                    <label for="set">Set:</label>
                    <select id="set" name="set" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="0">Not Applicable</option>
                    </select>

                    <label for="half">Half:</label>
                    <select id="half" name="half" required>
                        <option value="A">A Half</option>
                        <option value="B">B Half</option>
                    </select>

                    <div class="form-actions">
                        <input type="button" value="Submit" class="submit" onclick="prepareAndSubmitForm()">
                        <button type="button" class="cancel-btn" onclick="closeForm()">Cancel</button>
                    </div>
                </form>
            </div>
            <div class="student-container">
                <h3>Add Students</h3>
                <input type="text" id="studentName" name="studentName" onkeyup="searchPeople('student')">
                <div id="studentSearchResults"></div>
                <div id="selectedStudents"></div>
            </div>
            <div class="schedule-container">
                <label>
                    <input type="checkbox" id="scheduleCheckbox" onchange="toggleWeeksSchedule()"> Schedule for both Week A and B
                </label>
                <div id="weekASchedule" class="week-schedule">
                    <h3>Week A Schedule</h3>
                    <div id="weekAEntries"></div>
                    <button type="button" onclick="addScheduleEntry('weekAEntries')" class="add-schedule-btn">Add</button>
                </div>

                <div id="weekBSchedule" class="week-schedule">
                    <h3>Week B Schedule</h3>
                    <div id="weekBEntries"></div>
                    <button type="button" onclick="addScheduleEntry('weekBEntries')" class="add-schedule-btn">Add</button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    var selectedStudentIds = [];
    var selectedTeacherIds = [];

    function prepareAndSubmitForm() {
        // Populate hidden inputs for students and teachers
        updateHiddenInput('student');
        updateHiddenInput('teacher');

        // Populate hidden inputs for schedule data
        document.getElementById('weekAScheduleData').value = getScheduleData('weekAEntries');
        document.getElementById('weekBScheduleData').value = getScheduleData('weekBEntries');

        // Submit the form
        document.querySelector('.form-container form').submit();
    }

    function getScheduleData(weekId) {
        var scheduleEntries = document.getElementById(weekId).getElementsByClassName('schedule-entry');
        var scheduleData = [];
        var dayName = weekId.substring(0,5) + "Day";
        var className = weekId.substring(0,5) + "Classroom";
        var sessionName = weekId.substring(0,5) + "Session";

        for (var i = 0; i < scheduleEntries.length; i++) {
            var day = scheduleEntries[i].querySelector(`select[name^=${dayName}]`).value;
            var classroom = scheduleEntries[i].querySelector(`input[name^=${className}]`).value;
            var session = scheduleEntries[i].querySelector(`select[name^=${sessionName}]`).value;
            scheduleData.push(day + ',' + classroom + ',' + session); // Include classroom data
        }

        return scheduleData.join(';');
    }


    function toggleWeeksSchedule() {
        var isChecked = document.getElementById('scheduleCheckbox').checked;
        document.getElementById('weekBSchedule').style.display = isChecked ? 'none' : 'block';
        if (isChecked) {
            document.getElementById('weekBEntries').innerHTML = ''; // Clear Week B entries
        }
    }

    function addScheduleEntry(weekId) {
        var weekPrefix = weekId === 'weekAEntries' ? 'weekADay' : 'weekBDay';
        var sessionPrefix = weekId === 'weekAEntries' ? 'weekASession' : 'weekBSession';
        var classroomPrefix = weekId === 'weekAEntries' ? 'weekAClassroom' : 'weekBClassroom';

        var entryDiv = document.createElement('div');
        entryDiv.className = 'schedule-entry';
        entryDiv.innerHTML = `
        <select name="${weekPrefix}[]">
            <option value="1">Monday</option>
            <option value="2">Tuesday</option>
            <option value="3">Wednesday</option>
            <option value="4">Thursday</option>
            <option value="5">Friday</option>
        </select>
        <select name="${sessionPrefix}[]">
            <option value="08:50:00,09:50:00">Session 1: 08:50 - 9:50</option>
            <option value="09:55:00,10:55:00">Session 2: 09:55 - 10:55</option>
            <option value="11:15:00,12:15:00">Session 3: 11:15 - 12:15</option>
            <option value="12:20:00,13:20:00">Session 4: 12:20 - 13:20</option>
            <option value="13:25:00,14:00:00">Lunchtime: 13:25 - 14:00</option>
            <option value="14:05:00,15:05:00">Session 5: 14:05 - 15:05</option>
            <option value="15:10:00,16:10:00">After school: 15:10 - 16:10</option>
        </select>
        <input type="text" name="${classroomPrefix}[]" placeholder="Room" maxlength="4">
        <span class="remove" onclick="removeScheduleEntry(this)">&times;</span>
    `;
        document.getElementById(weekId).appendChild(entryDiv);
    }

    function removeScheduleEntry(element) {
        element.parentNode.remove();
    }

    function searchPeople(role) {
        var inputField = document.getElementById(role === 'student' ? 'studentName' : 'teacherName');
        var searchTerm = inputField.value;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var results = JSON.parse(this.responseText);
                displaySearchResults(results, role);
            }
        };
        xhr.open("GET", "../../../presets/search_people.php?term=" + searchTerm + "&role=" + role, true);
        xhr.send();
    }

    function displaySearchResults(results, role) {
        var resultsContainer = document.getElementById(role === 'student' ? 'studentSearchResults' : 'teacherSearchResults');
        resultsContainer.innerHTML = '';

        if (results.length > 0) {
            results.forEach(function (person) {
                if ((role === 'student' && !selectedStudentIds.includes(person.id.toString())) ||
                    (role === 'teacher' && !selectedTeacherIds.includes(person.id.toString()))) {
                    var div = document.createElement('div');
                    div.innerHTML = person.name + ' ' + person.surname;
                    div.setAttribute('data-id', person.id);
                    div.setAttribute('onclick', 'selectPerson(this, "' + role + '")');
                    resultsContainer.appendChild(div);
                }
            });
            resultsContainer.style.display = 'block';
        }else{
            resultsContainer.style.display = 'none';
        }
    }

    function updateHiddenInput(role) {
        var hiddenInput = document.getElementById(role === 'student' ? 'selectedStudentIds' : 'selectedTeacherIds');
        var selectedIds = role === 'student' ? selectedStudentIds : selectedTeacherIds;
        hiddenInput.value = selectedIds.join(','); // Store as a comma-separated string
    }

    function selectPerson(element, role) {
        var id = element.getAttribute('data-id');
        var name = element.innerHTML;

        if (role === 'student' && !selectedStudentIds.includes(id)) {
            selectedStudentIds.push(id);
        } else if (role === 'teacher' && !selectedTeacherIds.includes(id)) {
            selectedTeacherIds.push(id);
        } else {
            return;
        }

        // Clear the search results and input field
        var resultsContainer = document.getElementById(role === 'student' ? 'studentSearchResults' : 'teacherSearchResults');
        resultsContainer.innerHTML = '';
        var searchInput = document.getElementById(role === 'student' ? 'studentName' : 'teacherName');
        searchInput.value = '';

        // Add the selected person to the 'selected' container
        var selectedContainer = document.getElementById(role === 'student' ? 'selectedStudents' : 'selectedTeachers'); // Correct the ID if necessary
        var selectedDiv = document.createElement('div');
        selectedDiv.innerHTML = name + ' <span class="remove" onclick="unselectPerson(this, \'' + role + '\')">&times;</span>';
        selectedDiv.setAttribute('data-id', id);
        selectedDiv.classList.add('selected-person');
        selectedContainer.appendChild(selectedDiv);

        var resultsContainer = document.getElementById(role === 'student' ? 'studentSearchResults' : 'teacherSearchResults');
        resultsContainer.innerHTML = '';
        updateHiddenInput(role);
    }

    function unselectPerson(element, role) {
        var id = element.parentNode.getAttribute('data-id');
        if (role === 'student') {
            selectedStudentIds = selectedStudentIds.filter(studentId => studentId !== id);
        } else if (role === 'teacher') {
            selectedTeacherIds = selectedTeacherIds.filter(teacherId => teacherId !== id);
        }

        element.parentNode.remove();
        searchPeople(role);
    }

    function hideTeachers() {
        var teacherResults = document.getElementById('teacherSearchResults');
        teacherResults.style.display = 'none';
    }

    function hideStudents() {
        var studentResults = document.getElementById('studentSearchResults');
        studentResults.style.display = 'none';
    }

    document.addEventListener('click', function(event) {
        var teacherInput = document.getElementById('teacherName');
        var studentInput = document.getElementById('studentName');
        var teacherResults = document.getElementById('teacherSearchResults');
        var studentResults = document.getElementById('studentSearchResults');

        var clickedInsideTeacher = teacherInput.contains(event.target) || teacherResults.contains(event.target);
        var clickedInsideStudent = studentInput.contains(event.target) || studentResults.contains(event.target);

        if (!clickedInsideStudent){
            hideStudents();
        }
        if (!clickedInsideTeacher){
            hideTeachers();
        }
    });


    function showForm() {
        document.getElementById('popupForm').style.display = 'block';
    }

    function closeForm() {
        document.getElementById('popupForm').style.display = 'none';
        var form = document.querySelector('.form-container form');
        form.reset();

        // Clear selected students and teachers
        document.getElementById('selectedStudents').innerHTML = '';
        document.getElementById('selectedTeachers').innerHTML = '';

        // Clear schedule entries for Week A and Week B
        document.getElementById('weekAEntries').innerHTML = '';
        document.getElementById('weekBEntries').innerHTML = '';
    }

    document.getElementById('createButton').addEventListener('click', showForm);
</script>
</body>
</html>
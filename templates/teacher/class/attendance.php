<?php
include('../../../presets/getset.php');
$class_id = $_GET['class_id'];
$students = getStudents($class_id);
$date = new DateTime();
if ($date->format('w') != 1) {
    $date->modify('last Monday');
}
$schedules = getClassLessonsInterval(7, $class_id, $date);
$updateFields = [];
?>
<div id="attendance-sheet">
    <div class="attendance-data">
        <table class="attendance-sheet">
            <thead>
            <tr>
                <th></th>
                <!-- Create a header cell for each date with 7 sub-cells for each day's sessions -->
                <?php foreach ($schedules as $date => $lessons): ?>
                    <th colspan="7"><?php echo htmlspecialchars($date); ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <!-- Session row for the headers above -->
            <tr>
                <th class="student-name">Student Name</th>
                <?php foreach ($schedules as $date => $lessons): ?>
                    <!-- Create cells for 4 lessons, lunch, 1 lesson, and after school -->
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <td class="lesson-cell morning-lesson">L<?php echo $i; ?></td>
                    <?php endfor; ?>
                    <td class="lesson-cell lunch">Lu</td>
                    <td class="lesson-cell afternoon-lesson">L5</td>
                    <td class="lesson-cell after-school">A/S</td>
                <?php endforeach; ?>
            </tr>

            <!-- Create a row for each student -->
            <?php foreach ($students as $student): ?>
                <tr data-id="<?=$student['id']?>">
                    <!-- Student name and surname cell -->
                    <td class="student-name">
                        <?php echo htmlspecialchars($student['name'] . ' ' . $student['surname']); ?>
                    </td>
                    <!-- Fill the rest of the student's row with empty cells -->
                    <?php foreach ($schedules as $date => $lessons): ?>
                        <!-- Create empty cells for separation -->
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                            <?php
                            $attendance = getAttendance($date, $i, $class_id, $student['id']);
                            if (is_array($attendance)){
                                switch ($attendance['status']){
                                    case 'Absent':
                                        $symbol = '/';
                                        break;
                                    case 'Late':
                                        $symbol = 'L';
                                        break;
                                    case 'Present':
                                        $symbol = 'P';
                                        break;
                                    case 'Illness':
                                        $symbol = 'I';
                                        break;
                                    case 'Other':
                                        $symbol = 'O';
                                        break;
                                    default:
                                        $symbol = '?';
                                }
                            }else{
                                $symbol = '?';
                            }
                            ?>
                            <td data-date="<?= $date?>" data-session="<?= $i?>" class="lesson-cell"><?=$symbol?></td>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <button class="cancel" onclick="closeForm()">Cancel</button>
    <button class="submit" onclick="saveAttendance()">Save</button>
</div>

<script>
    var fieldsEdited = [];

    function closeForm() {
        document.getElementById('attendance-sheet').parentElement.style.display = 'none';
    }

    function saveAttendance() {
        var fields = '';
        fieldsEdited.forEach(function(field) {
            fields += field.date + ',' + field.account_id + ',' + <?=$class_id?> + ',' + field.status + ',' + field.late_minutes + ',' + field.session + ';';
        });
        window.location.href = "save_attendance.php?class_id=<?=$class_id?>&fields=" + fields;
    }

    var attendanceTable = document.querySelector('.attendance-sheet');
    attendanceTable.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.lesson-cell')) {
            e.target.setAttribute('contenteditable', 'true');
            e.target.focus(); // Focus the cell to start editing
        }
    });

    // Attach keydown event listener to the table
    attendanceTable.addEventListener('keydown', function(e) {
        if (e.target && e.target.matches('.lesson-cell')) {
            var newValue;
            var symbol;
            var minutesLate = 0;
            switch (e.key.toLowerCase()) {
                case 'o':
                    newValue = 'Other';
                    symbol = 'O'
                    break;
                case 'i':
                    newValue = 'Illness';
                    symbol = 'I'
                    break;
                case 'p':
                    newValue = 'Present';
                    symbol = 'P'
                    break;
                case '/':
                    newValue = 'Absent';
                    symbol = '/'
                    break;
                case 'l':
                    // Prompt the user to enter the number of minutes late, with "1" as the default value
                    minutesLate = prompt("Enter the number of minutes late:", "1");
                    if (minutesLate !== null) {
                        newValue = 'Late';
                        symbol = 'L'
                    }else {
                        newValue = 'Present';
                        symbol = 'P'
                    }
                    break;
                default:
                    e.preventDefault();
                    return; // Exit the function and allow default keydown behavior
            }

            // If newValue was set, update the cell content and remove contenteditable attribute
            if (newValue) {
                e.target.textContent = symbol;
                e.target.removeAttribute('contenteditable');
                e.preventDefault();

                const matchingField = fieldsEdited.find(
                    field =>
                        field.date === e.target.getAttribute('data-date') &&
                        field.account_id === e.target.parentElement.getAttribute('data-id') &&
                        field.session === e.target.getAttribute('data-session')
                );
                if (matchingField) {
                    Object.assign(matchingField, {
                        status: newValue,
                        late_minutes: minutesLate,
                    });
                } else {
                    fieldsEdited.push({
                        date: e.target.getAttribute('data-date'),
                        account_id: e.target.parentElement.getAttribute('data-id'),
                        status: newValue,
                        late_minutes: minutesLate,
                        session: e.target.getAttribute('data-session'),
                    });
                }
                var currentCellIndex = Array.from(e.target.parentNode.children).indexOf(e.target);
                var nextRow = e.target.parentNode.nextElementSibling;
                if (nextRow) {
                    var nextCell = nextRow.children[currentCellIndex];
                    if (nextCell && nextCell.matches('.lesson-cell')) {
                        nextCell.setAttribute('contenteditable', 'true');
                        nextCell.focus();
                    }
                }
            }
        }
    });
</script>

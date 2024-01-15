<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'student');
$id = getId();
$table_data = getConsequencesStudent($id, true);
getConsequencesStudent($id, false);
$consequencesData = getConsequencesStudent($id, true);
$monthPoints = array_reduce($consequencesData, function($carry, $item) {
    if (new DateTime($item['date']) >= new DateTime(date('Y-m-01'))) {
        $carry += 1;
    }
    return $carry;
}, 0);
$weekPoints = array_reduce($consequencesData, function($carry, $item) {
    if (new DateTime($item['date']) >= new DateTime("last Sunday")) {
        $carry += 1;
    }
    return $carry;
}, 0);
$startWeek = date_format(new DateTime('last Monday'),'jS F Y');
$startMonth = '1st ' . date('F');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="icon" href="../../../presets/favicon.png" type="image/png">
    <link href="consequences.css" rel="stylesheet" type="text/css">
    <link href="../header/header.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="consequence-chart.js"></script>
    <script>
        $(function(){$(".header").load("../header/header.html")});
    </script>
</head>

<!-- Header -->
<header class="header"></header>

<body>
    <div id="charts">
        <div class="chart">
            <label for="consequence-chart-values" style="font: bold 20px sans-serif;">Consequences given for</label>
            <canvas class="chart" id="consequence-chart-values" style="margin-top: 10px;"></canvas>
        </div>

        <div class="chart">
            <label for="consequence-chart-subject" style="font: bold 20px sans-serif;">Consequences given in</label>
            <canvas class="chart" id="consequence-chart-subject" style="margin-top: 10px;"></canvas>
        </div>
    </div>

    <div id="consequence-details" class="consequence-details">
        <div class="consequence-block">
            <label for="this-month-consequences" class="consequence-label">This month</label>
            <div id="this-month-consequences" class="consequences-details">
                <p style="margin-bottom: 10px"><?= $monthPoints ?></p>
                <p>Since <?= $startMonth ?></p>
            </div>
        </div>

        <div class="consequence-block">
            <label for="this-week-consequences" class="consequence-label">This week</label>
            <div id="this-week-consequences" class="consequences-details">
                <p style="margin-bottom: 10px"><?= $weekPoints ?></p>
                <p>Since <?= $startWeek ?></p>
            </div>
        </div>
    </div>

    <input oninput="filterTable()" id="search-input">
    <div id="table-wrapper">
        <table class="consequences-table">
            <thead>
            <tr>
                <th>Given by</th>
                <th>Subject</th>
                <th>Type</th>
                <th>Date</th>
                <th>Reason</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($table_data as $data): ?>
                <tr>
                    <td><?= getAccountName($data['teacher_id']) ?></td>
                    <td><?= $data['subject'] ?></td>
                    <td><?= $data['type'].$data['level'] ?></td>
                    <td><?= date_format(new DateTime($data['date']), 'jS F Y') ?></td>
                    <td><?= $data['details'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        function filterTable() {
            var input, filter, table, tr, td, i, j, txtValue, found;
            input = document.getElementById('search-input');
            filter = input.value.toUpperCase();
            table = document.querySelector(".consequences-table");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td");
                found = false;
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }
    </script>
</body>
</html>
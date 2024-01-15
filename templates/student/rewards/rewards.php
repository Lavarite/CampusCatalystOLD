<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'student');
$id = getId();
$table_data = getRewardsStudent($id, true);
getRewardsStudent($id, false, false, 1);
$rewardsData = getRewardsStudent($id, true, false, 1);
$monthPoints = array_reduce($rewardsData, function($carry, $item) {
    if (new DateTime($item['date']) >= new DateTime(date('Y-m-01'))) {
        $carry += $item['volume'];
    }
    return $carry;
}, 0);
$weekPoints = array_reduce($rewardsData, function($carry, $item) {
    if (new DateTime($item['date']) >= new DateTime("last Sunday")) {
        $carry += $item['volume'];
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
    <link href="rewards.css" rel="stylesheet" type="text/css">
    <link href="../header/header.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="hp-chart.js"></script>
    <script>
        $(function(){$(".header").load("../header/header.html")});
    </script>
</head>

<!-- Header -->
<header class="header"></header>

<body>

<div id="charts">
    <div class="chart">
        <label for="house-points-chart-values" style="font: bold 20px sans-serif;">House Points given for</label>
        <canvas class="chart" id="house-points-chart-values" style="margin-top: 10px;"></canvas>
    </div>

    <div class="chart">
        <label for="house-points-chart-subject" style="font: bold 20px sans-serif;">House Points given in</label>
        <canvas class="chart" id="house-points-chart-subject" style="margin-top: 10px;"></canvas>
    </div>
</div>

<div id="rewards-details" class="rewards-details">
    <div class="rewards-block">
        <label for="this-month-points" class="rewards-label">This month</label>
        <div id="this-month-points" class="points-details">
            <p style="margin-bottom: 10px"><?= $monthPoints ?></p>
            <p>Since <?= $startMonth ?></p>
        </div>
    </div>

    <div class="rewards-block">
        <label for="this-week-points" class="rewards-label">This week</label>
        <div id="this-week-points" class="points-details">
            <p style="margin-bottom: 10px"><?= $weekPoints ?></p>
            <p>Since <?= $startWeek ?></p>
        </div>
    </div>
</div>
<input oninput="filterTable()" id="search-input">
<div id="table-wrapper">
    <table class="rewards-table">
        <thead>
        <tr>
            <th>Awarded by</th>
            <th>Quantity</th>
            <th>Subject</th>
            <th>Category</th>
            <th>Date</th>
            <th>Type</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($table_data as $data): ?>
            <tr>
                <td><?= getAccountName($data['teacher_id']) ?></td>
                <td><?= $data['volume'] ?></td>
                <td><?= $data['subject'] ?></td>
                <td><?= $data['details'] ?></td>
                <td><?= date_format(new DateTime($data['date']), 'jS F Y') ?></td>
                <td>
                    <?php
                        switch ($data['type']){
                            case 1:
                                echo 'House Point';
                                break;
                            case 2:
                                echo 'Postcard Home';
                                break;
                            case 3:
                                echo 'Headteachers Commendation';
                                break;
                            case 4:
                                echo 'Roll of Honour';
                                break;
                            case 5:
                                echo 'Star Student';
                                break;
                        }
                    ?>
                </td>
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
        table = document.querySelector(".rewards-table");
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
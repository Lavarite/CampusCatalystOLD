<?php
include('../../login/session_auth.php');
include('../../../presets/getset.php');
auth('../../login/login.php', 'admin');
$name = isset($_GET['name']) ? $_GET['name'] : '';
$surname = isset($_GET['surname']) ? $_GET['surname'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';

$students = getFilteredStudentAccounts();

$consequence_list = [];
$consequences = [];
$severe_consequences = [];
foreach ($students as $student){
    $consequence_list[$student['id']] = getConsequencesStudent($student['id'], true);
    $consequences[$student['id']] = count($consequence_list[$student['id']]);
    $severe_consequences[$student['id']] = count(array_filter($consequence_list[$student['id']], function ($item) {
        return ($item['level'] >= 3);
    }));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="icon" href="../../../presets/favicon.png" type="image/png">
    <link href="consequences.css" rel="stylesheet" type="text/css">
    <link href="consequence_form.css" rel="stylesheet" type="text/css">
    <link href="../header/header.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(function(){$(".header").load("../header/header.html")});
    </script>
</head>

<!-- Header -->
<header class="header"></header>

<body>
<div id="filtered-consequences">
    <div class="filter-bar">
        <form method="get">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div>
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($surname); ?>">
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div>
                <button type="submit">Filter</button>
                <button type="button" onclick="window.location.href = 'consequences.php';">Reset Filters</button>
            </div>
        </form>
    </div>
    <div id="table-wrapper">
        <table class="consequence-table">
            <thead>
            <tr>
                <th>Given to</th>
                <th>Consequences</th>
                <th>Severe Consequences</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $data): ?>
                <tr data-id="<?= $data['id']?>" data-name="<?= $data['name'] . ' ' . $data['surname']?>" class="table-row">
                    <td><?= $data['name'] . ' ' . $data['surname'] ?></td>
                    <td><?= $consequences[$data['id']] ?></td>
                    <td><?= $severe_consequences[$data['id']] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div id="details-wrapper"></div>
    <div id="grantForm"></div>
</div>
<script>
    var id = 0;

    function updateDetails(event){
        var row = event.target.closest('.table-row');
        if (!row) return;
        id = row.getAttribute('data-id');
        name = row.getAttribute('data-name');
        $.ajax({
            url: "consequence-chart.php",
            type: "GET",
            data: {id: id, name: name},
            success: function (data) {
                $('#details-wrapper').html(data).show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error loading form: " + textStatus, errorThrown);
            }
        });
    }
    document.querySelector('table').addEventListener('click', updateDetails);
</script>
</html>
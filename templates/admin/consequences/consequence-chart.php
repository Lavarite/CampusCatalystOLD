<?php
include('../../../presets/getset.php');
$id = $_GET['id'];
$name = $_GET['name'];
$consequencesData = getConsequencesStudent($id, true);
getConsequencesStudent($id, false, 'consequencesData');
$monthSubject = count(array_filter($consequencesData, function ($item) {
    return (new DateTime($item['date']) >= new DateTime(date('Y-m-01')));
}));
$weekSubject = count(array_filter($consequencesData, function ($item) {
    return (new DateTime($item['date']) >= new DateTime("last Sunday"));
}));
$startWeek = date_format(new DateTime('last Monday'),'jS F Y');
$startMonth = '1st ' . date('F');
?>
<script>
    var detailsColors = {
        'H1': '#FF6384',
        'H2': '#36A2EB',
        'H3': '#FFCE56',
        'C1': '#4BC0C0',
        'C2': '#F7464A',
        'C3': '#54b22d',
        'C4': '#ff7240',
        'C5': '#7246bf',

        'Mathematics': '#7C83FD',
        'English': '#00ffe9',
        'Science': '#FF9671',
        'Chemistry': '#8FD9A8', // Mint Leaf Green
        'Physics': '#FF9F1C', // Orange Peel
        'Biology': '#97ff00', // Baby Blue
        'History': '#5231e7',
        'Geography': '#6DDCCF',
        'French': '#b600ff',
        'Spanish': '#00ff77',
        'German': '#C8AD7F',
        'Art': '#ffdc00',
        'Music': '#7EB5A6',
        'Physical Education': '#3cb65f',
        'Drama': '#ff6a00',
        'Design & Technology': '#e00c0c',
        'Computer Science': '#0080ff',
        'Religious Studies': '#FFADAD',
        'Business Studies': '#00b01f',
        'Tutor': '#0004ff'
    };
    var totalVolume = 0;

    $(document).ready(function() {

        if (consequencesData.length !== 0){
            const SubjectData = consequencesData.reduce((acc, item) => {
                acc[item.subject] = (acc[item.subject] || 0) + 1;
                totalVolume += 1;
                return acc;
            }, {});
            createPieChart(SubjectData, 'chart-Subject', totalVolume);
        }else{
            var ctx = document.getElementById('chart-Subject').getContext('2d');
            var canvas = document.getElementById('chart-Subject');
            ctx.font = '20px Arial';
            ctx.fillStyle = 'black';
            ctx.textAlign = 'center';
            ctx.fillText('No consequences found :)', canvas.width / 2, canvas.height / 2);
        }

        if (consequencesData.length !== 0){
            totalVolume = 0;
            const TypeData = consequencesData.reduce((acc, item) => {
                acc[item.type + item.level] = (acc[item.type + item.level] || 0) + 1;
                totalVolume += 1;
                return acc;
            }, {});
            createPieChart(TypeData, 'chart-Type', totalVolume);
        }else {
            ctx = document.getElementById('chart-Type').getContext('2d');
            canvas = document.getElementById('chart-Type');
            ctx.font = '20px Arial';
            ctx.fillStyle = 'black';
            ctx.textAlign = 'center';
            ctx.fillText('No consequences found :)', canvas.width / 2, canvas.height / 2);
        }
    });

    function createPieChart(data, id, volume) {
        const ctx = document.getElementById(id).getContext('2d');
        const centerText = {
            id: 'centerText',
            afterDatasetsDraw(chart, args, pluginOptions) {
                const {ctx} = chart;
                ctx.textAlign = 'centre'
                ctx.textBaseline = 'middle';
                ctx.font = 'bold 35px sans-serif';
                const text = volume;
                const textWidth = ctx.measureText(text).width;
                const x = chart.getDatasetMeta(0).data[0].x-textWidth/2;
                const y = chart.getDatasetMeta(0).data[0].y;
                ctx.fillText(text, x, y);
            }
        }
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    data: Object.values(data),
                    backgroundColor: Object.keys(data).map(detail => detailsColors[detail])
                }],
            },
            plugins: [centerText],
            options: {
                plugins: {
                    legend: false,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.parsed || '';
                                const currentValue = context.parsed;
                                const percentage = ((currentValue / volume) * 100).toFixed(2) + '%';
                                label += ' ('+percentage+')';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
<div class="chart-container">
    <h2><?= $name ?></h2>
    <div class="chart-section">
        <div class="chart">
            <label for="chart-Subject" class="chart-label">Consequences given in</label>
            <canvas id="chart-Subject" class="chart-canvas"></canvas>
        </div>
        <div class="details-section">
            <div class="consequences-block">
                <label for="this-month-Subject" class="consequences-label">This month</label>
                <div id="this-month-Subject" class="consequences-details">
                    <p style="margin-bottom: 10px"><?= $monthSubject ?></p>
                    <p>Since <?= $startMonth ?></p>
                </div>
            </div>
            <div class="consequences-block">
                <label for="this-week-Subject" class="consequences-label">This week</label>
                <div id="this-week-Subject" class="consequences-details">
                    <p style="margin-bottom: 10px"><?= $weekSubject ?></p>
                    <p>Since <?= $startWeek ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="chart-section">
        <div class="chart">
            <label for="chart-Type" class="chart-label">Type of consequences given</label>
            <canvas id="chart-Type" class="chart-canvas"></canvas>
        </div>
    </div>
    <button class="create-btn" onclick="openGrant(<?= $id ?>)">Grant</button>
    <script>
        function openGrant(id){
            id = parseInt(id);
            $.ajax({
                url: "consequence_form.php",
                type: "GET",
                data: {id: id},
                success: function(data) {
                    // Show the edit form with the data received
                    $("#grantForm").html(data).show();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error loading form: " + textStatus, errorThrown);
                }
            });
        }
    </script>
</div>


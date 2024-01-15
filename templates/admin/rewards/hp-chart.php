<?php
include('../../../presets/getset.php');
$id = $_GET['id'];
$name = $_GET['name'];
$rewardsData = getRewardsStudent($id, true);
$accoladesData = array_filter($rewardsData, function ($item) {
    return ($item['type'] >= 2);
});
$pointsData = array_filter($rewardsData, function ($item) {
    return ($item['type'] == 1);
});
$monthPoints = array_reduce($pointsData, function($carry, $item) {
    if (new DateTime($item['date']) >= new DateTime(date('Y-m-01'))) {
        $carry += $item['volume'];
    }
    return $carry;
}, 0);
$weekPoints = array_reduce($pointsData, function($carry, $item) {
    if (new DateTime($item['date']) >= new DateTime("last Monday")) {
        $carry += $item['volume'];
    }
    return $carry;
}, 0);
$monthAccolades = count(array_filter($accoladesData, function ($item) {
    return (new DateTime($item['date']) >= new DateTime(date('Y-m-01')));
}));
$weekAccolades = count(array_filter($accoladesData, function ($item) {
    return (new DateTime($item['date']) >= new DateTime("last Sunday"));
}));
$startWeek = date_format(new DateTime('last Monday'),'jS F Y');
$startMonth = '1st ' . date('F');
?>

<?php getRewardsStudent($id, false, false,1, 'housepoints');?>
<?php getRewardsStudent($id, false, true,2, 'accolades');?>
<script>
    var detailsColors = {
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

        if (housepoints.length !== 0){
            const hpData = housepoints.reduce((acc, item) => {
                acc[item.subject] = (acc[item.subject] || 0) + parseInt(item.volume);
                totalVolume += parseInt(item.volume);
                return acc;
            }, {});
            createPieChart(hpData, 'chart-hp', totalVolume);
        }else{
            var ctx = document.getElementById('chart-hp').getContext('2d');
            var canvas = document.getElementById('chart-hp');
            ctx.font = '20px Arial';
            ctx.fillStyle = 'black';
            ctx.textAlign = 'center';
            ctx.fillText('No rewards found :(', canvas.width / 2, canvas.height / 2);
        }

        if (accolades.length !== 0){
            totalVolume = 0;
            const accoladeData = accolades.reduce((acc, item) => {
                acc[item.subject] = (acc[item.subject] || 0) + 1;
                totalVolume += 1;
                return acc;
            }, {});
            createPieChart(accoladeData, 'chart-accolades', totalVolume);
        }else {
            ctx = document.getElementById('chart-accolades').getContext('2d');
            canvas = document.getElementById('chart-accolades');
            ctx.font = '20px Arial';
            ctx.fillStyle = 'black';
            ctx.textAlign = 'center';
            ctx.fillText('No rewards found :(', canvas.width / 2, canvas.height / 2);
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
            <label for="chart-hp" class="chart-label">House Points given in</label>
            <canvas id="chart-hp" class="chart-canvas"></canvas>
        </div>
        <div class="details-section">
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
    </div>

    <div class="chart-section">
        <div class="chart">
            <label for="chart-accolades" class="chart-label">Accolades given in</label>
            <canvas id="chart-accolades" class="chart-canvas"></canvas>
        </div>
        <div class="details-section">
            <div class="rewards-block">
                <label for="this-month-accolades" class="rewards-label">This month</label>
                <div id="this-month-accolades" class="accolades-details">
                    <p style="margin-bottom: 10px"><?= $monthAccolades ?></p>
                    <p>Since <?= $startMonth ?></p>
                </div>
            </div>
            <div class="rewards-block">
                <label for="this-week-accolades" class="rewards-label">This week</label>
                <div id="this-week-accolades" class="accolades-details">
                    <p style="margin-bottom: 10px"><?= $weekAccolades ?></p>
                    <p>Since <?= $startWeek ?></p>
                </div>
            </div>
        </div>
    </div>
    <button class="create-btn" onclick="openGrant(<?= $id ?>)">Grant</button>
    <script>
        function openGrant(id){
            id = parseInt(id);
            $.ajax({
                url: "rewards_form.php",
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


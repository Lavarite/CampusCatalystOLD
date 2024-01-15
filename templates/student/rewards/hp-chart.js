var detailsColors = {
    'Aspiration': '#FF6384',
    'Confidence': '#36A2EB',
    'Integrity': '#FFCE56',
    'Initiative': '#4BC0C0',
    'Resilience': '#F7464A',
    'Tolerance': '#7246bf',

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
    'Business Studies': '#00b01f'
};
var totalVolume = 0;

$(document).ready(function() {
    if (data.length !== 0){
        const valuesData = data.reduce((acc, item) => {
            acc[item.details] = (acc[item.details] || 0) + parseInt(item.volume);
            totalVolume += parseInt(item.volume);
            return acc;
        }, {});
        const subjectsData = data.reduce((acc, item) => {
            acc[item.subject] = (acc[item.subject] || 0) + parseInt(item.volume);
            return acc;
        }, {});
        createPieChart(valuesData, 'house-points-chart-values');
        createPieChart(subjectsData, 'house-points-chart-subject');
    }else{
    var ctx = document.getElementById('house-points-chart-values').getContext('2d');
    var canvas = document.getElementById('house-points-chart-values');
    ctx.font = '20px Arial';
    ctx.fillStyle = 'black';
    ctx.textAlign = 'center';
    ctx.fillText('No rewards found :(', canvas.width / 2, canvas.height / 2);

    ctx = document.getElementById('house-points-chart-subject').getContext('2d');
    canvas = document.getElementById('house-points-chart-subject');
    ctx.font = '20px Arial';
    ctx.fillStyle = 'black';
    ctx.textAlign = 'center';
    ctx.fillText('No rewards found :(', canvas.width / 2, canvas.height / 2);
}

});

function createPieChart(data, id) {
    const ctx = document.getElementById(id).getContext('2d');
    const centerText = {
        id: 'centerText',
        afterDatasetsDraw(chart, args, pluginOptions) {
            const {ctx} = chart;
            ctx.textAlign = 'centre'
            ctx.textBaseline = 'middle';
            ctx.font = 'bold 35px sans-serif';
            const text = totalVolume;
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
                            const percentage = ((currentValue / totalVolume) * 100).toFixed(2) + '%';
                            label += ' ('+percentage+')';
                            return label;
                        }
                    }
                }
            }
        }
    });
}
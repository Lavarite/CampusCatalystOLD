var detailsColors = {
    'H1: Home learning not complete or not to a satisfactory standard': '#FF6384',
    'H2: Home learning not completed by 2nd deadline': '#36A2EB',
    'H3: Student fails to attend the faculty detention': '#FFCE56',

    'C1: Disrupting the learning of others': '#4BC0C0',
    'C1: Out of seat': '#4BC0C0',
    'C1: Uniform/Appearance below expected standard': '#4BC0C0',
    'C1: Lack of equipment/device': '#4BC0C0',
    'C1: Verbal warning': '#4BC0C0',

    'C2: Repetition of any C1 offence': '#F7464A',
    'C2: 2nd Verbal warning': '#F7464A',

    'C3: Repetition of any C2 offence': '#7246bf',
    'C3: Inappropriate conduct': '#7246bf',
    'C3: Rudeness to a member of staff/arguing': '#7246bf',
    'C3: Refusal to follow instructions': '#7246bf',
    'C3: Use of device/mobile phone without permission': '#7246bf',
    'C3: Repeated failure to bring appropriate equipment': '#7246bf',
    'C3: Inappropriate language inc. swearing in conversation': '#7246bf',
    'C3: Provoking another student/situation': '#7246bf',
    'C3: Failure to attend homework detention': '#7246bf',
    'C3: Lateness to lessons (>5mins)': '#7246bf',
    'C3: Late for school without a valid reason': '#7246bf',
    'C3: Abuse of Open Access, including being in the vicinity of the bicycle shed between 8.45am-3pm': '#7246bf',

    'C4: Repetition of any C3 offence': '#2f9b3a',
    'C4: 2 C3s in one day': '#2f9b3a',
    'C4: Dishonesty/lying to a member of staff': '#2f9b3a',
    'C4: Walking away from a member of staff': '#2f9b3a',
    'C4: Deliberate defiance, including appearance': '#2f9b3a',
    'C4: Swearing across a room/at another student': '#2f9b3a',
    'C4: Bullying incident - Cyber/Verbal/Physical': '#2f9b3a',
    'C4: Fighting': '#2f9b3a',
    'C4: Chewing gum': '#2f9b3a',
    'C4: Off site at lunch': '#2f9b3a',

    'C5: Poor behaviour during C4': '#e18324',
    'C5: Cultural intolerance': '#e18324',
    'C5: Gross disobedience': '#e18324',
    'C5: Physical assault': '#e18324',
    'C5: Discriminatory language': '#e18324',
    'C5: Persistent bullying': '#e18324',
    'C5: Graffiti or Damage': '#e18324',
    'C5: Inappropriate use of mobile phone/computer or equipment': '#e18324',
    'C5: Persistent C4 behaviour': '#e18324',
    'C5: Smoking/e-cigarettes and/or the possession of cigarettes, lighters and/or alcohol': '#e18324',
    'C5: Being in the vicinity of smokers': '#e18324',
    'C5: Swearing at or about a member of staff': '#e18324',
    'C5: Threatening behaviour against staff or student': '#e18324',

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
            const key = item.type + item.level + ': ' + item.details;
            acc[key] = (acc[item.details] || 0) + 1;
            totalVolume += 1;
            return acc;
        }, {});
        const subjectsData = data.reduce((acc, item) => {
            acc[item.subject] = (acc[item.subject] || 0) + 1;
            return acc;
        }, {});
        createPieChart(valuesData, 'consequence-chart-values');
        createPieChart(subjectsData, 'consequence-chart-subject');
    }else {
        var ctx = document.getElementById('consequence-chart-values').getContext('2d');
        var canvas = document.getElementById('consequence-chart-values');
        ctx.font = '20px Arial';
        ctx.fillStyle = 'black';
        ctx.textAlign = 'center';
        ctx.fillText('No consequences found :)', canvas.width / 2, canvas.height / 2);

        ctx = document.getElementById('consequence-chart-subject').getContext('2d');
        canvas = document.getElementById('consequence-chart-subject');
        ctx.font = '20px Arial';
        ctx.fillStyle = 'black';
        ctx.textAlign = 'center';
        ctx.fillText('No consequences found :)', canvas.width / 2, canvas.height / 2);
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
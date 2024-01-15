<?php
include('../../../presets/getset.php');
$id = $_GET['id'];
?>

<div id="rewards-form">
    <form action="grant_reward.php">
        <label>Student: <?= getAccountName($id)?></label>

        <label for="type">Select the type of reward: </label>
        <select id="type" name="type" onchange="updateType()">
            <option value="1">House Point</option>
            <option value="2">Postcard Home</option>
            <option value="3">Headteacher Commendation</option>
            <option value="4">Roll of Honour</option>
            <option value="5">Start Student</option>
        </select>

        <label for="reason">Select the reason for the reward: </label>
        <select id="reason" name="reason">
            <option value="Aspiration">Aspiration</option>
            <option value="Confidence">Confidence</option>
            <option value="Integrity">Integrity</option>
            <option value="Initiative">Initiative</option>
            <option value="Resilience">Resilience</option>
            <option value="Tolerance">Tolerance</option>
        </select>

        <label for="volume">Select the volume: </label>
        <select id="volume" name="volume"></select>

        <input type="hidden" name="class_id" value="0">
        <input type="hidden" name="student_id" value="<?= $id?>">
        <input type="hidden" name="teacher_id" value="<?= getId()?>">
        <input type="submit">
    </form>
    <button class="cancel" onclick="closeForm()">Cancel</button>
</div>
<script>
    function closeForm() {
        document.getElementById('rewards-form').parentElement.style.display = 'none';
    }

    function updateType() {
        var level = document.getElementById("type").value;
        var volumeSelect = document.getElementById("volume");

        // Clear existing options
        volumeSelect.innerHTML = '';

        // Define reasons based on level
        var volumes = {
            '1': [1,2,3,4,5,6,7,8,9,10],
            '2': [1],
            '3': [1],
            '4': [1],
            '5': [1]
        };

        // Populate reasons based on selected level
        if (volumes[level]) {
            volumes[level].forEach(function(reason) {
                var option = document.createElement("option");
                option.value = reason;
                option.text = reason;
                volumeSelect.appendChild(option);
            });
        }
    }

    updateType();
</script>

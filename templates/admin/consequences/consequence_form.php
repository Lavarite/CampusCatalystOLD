<?php
include('../../../presets/getset.php');
$id = $_GET['id'];
?>

<div id="consequence-form">
    <form action="grant_consequence.php">
        <label for="student_id">Student: </label>
        <label>Student: <?= getAccountName($id)?></label>

        <label for="level">Select the consequence level: </label>
        <select id="level" name="level" onchange="updateReasons()">
            <option value="H1">H1</option>
            <option value="H2">H2</option>
            <option value="H3">H3</option>
            <option value="C1">C1</option>
            <option value="C2">C2</option>
            <option value="C3">C3</option>
            <option value="C4">C4</option>
            <option value="C5">C5</option>
            <option value="C6">C6</option>
        </select>

        <label for="reason">Select the reason for the consequence: </label>
        <select id="reason" name="reason"></select>

        <input type="hidden" name="class_id" value="0">
        <input type="hidden" name="student_id" value="<?= $id?>">
        <input type="hidden" name="teacher_id" value="<?= getId()?>">
        <input type="submit">
    </form>
    <button class="cancel" onclick="closeForm()">Cancel</button>
</div>
<script>
    function closeForm() {
        document.getElementById('consequence-form').parentElement.style.display = 'none';
    }

    function updateReasons() {
        var level = document.getElementById("level").value;
        var reasonSelect = document.getElementById("reason");

        // Clear existing options
        reasonSelect.innerHTML = '';

        // Define reasons based on level
        var reasons = {
        'H1': ['Home learning not complete or not to a satisfactory standard'],
        'H2': ['Home learning not completed by 2nd deadline'],
        'H3': ['Student fails to attend the faculty detention'],
        'C1': ['Disrupting the learning of others', 'Out of seat', 'Uniform/Appearance below expected standard', 'Lack of equipment/device', 'Verbal warning'],
        'C2': ['Repetition of any C1 offence', '2nd Verbal warning'],
        'C3': ['Repetition of any C2 offence', 'Inappropriate conduct', 'Rudeness to a member of staff/arguing', 'Refusal to follow instructions', 'Use of device/mobile phone without permission', 'Repeated failure to bring appropriate equipment', 'Inappropriate language inc. swearing in conversation', 'Provoking another student/situation', 'Failure to attend homework detention', 'Lateness to lessons (>5mins)', 'Late for school without a valid reason', 'Abuse of Open Access, including being in the vicinity of the bicycle shed between 8.45am-3pm'],
        'C4': ['Repetition of any C3 offence', '2 C3s in one day', 'Dishonesty/lying to a member of staff', 'Walking away from a member of staff', 'Deliberate defiance, including appearance', 'Swearing across a room/at another student', 'Bullying incident – Cyber/Verbal/Physical', 'Fighting', 'Chewing gum', 'Off site at lunch'],
        'C5': ['Poor behaviour during C4', 'Cultural intolerance', 'Gross disobedience', 'Physical assault', 'Discriminatory language', 'Persistent bullying', 'Graffiti or Damage', 'Inappropriate use of mobile phone/computer or equipment', 'Persistent C4 behaviour', 'Smoking/e-cigarettes and/or the possession of cigarettes, lighters and/or alcohol', 'Being in the vicinity of smokers', 'Swearing at or about a member of staff', 'Threatening behaviour against staff or student'],
        'C6': ['Persistent C5 behaviour', 'Possession of offensive weapon', 'Theft or vandalism', 'Sexual misconduct/harassment', 'Possession of illegal drugs', 'Violence towards a member of staff']
        };

        // Populate reasons based on selected level
        if (reasons[level]) {
            reasons[level].forEach(function(reason) {
            var option = document.createElement("option");
            option.value = reason;
            option.text = reason;
            reasonSelect.appendChild(option);
            });
        }
    }

    updateReasons();
</script>

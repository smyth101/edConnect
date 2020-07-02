<?php
$update = false;
$sql = 'SELECT * FROM detention LEFT JOIN students ON students.student_id = detention.student_id WHERE date = "' . $detDate . '" AND staff_id="' . $_SESSION['userId'] . '" AND detention_type ="' . $detentionList . '"';
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    if($row['status'] != ''){
        $update = true;
    }
    echo '<tr>';
    echo '<td data-toggle="modal" data-target="#add-action-modal" onclick="showActionModal(event.target)" class="hover">' . $row['name'] . '</td><td><input type="checkbox" name="detAttendList[]" value="' . $row['student_id'] . '" onclick="detAttendCheck(event.target)" ';
    echo ($row['status'] == 'present' || $row['status'] == 'late')?'checked':'';
    echo ($row['status'] == 'late')?' disabled':'';
    echo '><input type="hidden" name="detAbsentList[]" value="' . $row['student_id'] . '" ';
    echo ($row['status'] == 'present' || $row['status'] == 'late')?'disabled':'';
    echo '></td><td><input type="checkbox" onclick = "detLateCheck(event.target)" name="detLateList[]" value="' . $row['student_id'] . '" ';
    echo ($row['status'] == 'late')?'checked':'';
    echo '></td>';
    echo '</tr>';
}
echo '</table></div><div class="row justify-content-center mt-3 ml-0 mr-0"><button class="btn btn-primary">';
echo ($update == true)?'Update':'Submit';
echo '</button></div>
    <input type="hidden" name="detType" value="' . $detentionList . '"></form>
    <script>
    function detAttendCheck(e){
        if(e.checked ==true){
            e.parentNode.childNodes[1].disabled = true
        }
        else{
            e.parentNode.childNodes[1].disabled = false
        }
    }
    function detLateCheck(e){
        attend = e.parentNode.parentNode.childNodes[1].childNodes[0]
        absent = e.parentNode.parentNode.childNodes[1].childNodes[1]
        if(e.checked == true){
            attend.checked = true
            attend.disabled = true
            absent.disabled = true
        }
        else{
            attend.checked = false
            attend.disabled = false
            absent.disabled = false
        }
    }
    </script>';
?>
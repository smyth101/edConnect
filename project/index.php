<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    require('class-functions.php');
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }

    if(isset($_POST['accountChange'])){
        $_SESSION['userId'] = $_POST['accountChange'];
    }
    if(isset($_SESSION['lastRefresh'])){
        $refreshTime = (30 * 60) + $_SESSION['lastRefresh'];
        if($refreshTime < time()){
            unset($_SESSION['period']);
            unset($_SESSION['subjectNow']);
        }
    }
    $_SESSION['lastRefresh'] = time();

    principalViewCheck($conn);

    if(isset($_POST['sidebarValue'])){
        if($_POST['sidebarValue'] == 'classList'){
            unset($_SESSION['classHistory']);
            unset($_SESSION['classAnalytics']);
        }
        else if($_POST['sidebarValue'] == 'classHistory'){
            unset($_SESSION['classAnalytics']);
            $_SESSION[$_POST['sidebarValue']] = true;
        }
        else if($_POST['sidebarValue'] == 'classAnalytics'){
            unset($_SESSION['classHistory']);
            $_SESSION[$_POST['sidebarValue']] = true;
        }
    }
    
    if(isset($_POST['home'])){
        unset($_SESSION['classHistory']);
        unset($_SESSION['classAnalytics']);
    }

    if(isset($_SESSION['classHistory'])){
        if(isset($_POST['dateTime'])){
            $_SESSION['historyPeriod'] = $_POST['dateTime'];
            $_SESSION['historyDate'] = $_POST['historyDate'];
        }
    }
    else{
        if(isset($_POST['dateTime'])){
            $_SESSION['period'] = $_POST['dateTime'];
        }
    }


    if(isset($_POST['timetableSubjectCode'])){
        $_SESSION['subjectNow'] = $_POST['timetableSubjectCode'];
        unset($_SESSION['classHistory']);
        unset($_SESSION['classAnalytics']);
        if(isset($_POST['timetableToHistoryDate'])){
            $_SESSION['classHistory'] = true;
            $_SESSION['historyDate'] = $_POST['timetableToHistoryDate'];
            $_SESSION['historyPeriod'] = $_POST['timetableToHistoryPeriod'];
        }
    }

    if(isset($_POST['attendList']) || isset($_POST['lateList'])){
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, date, " . $_SESSION['period'] . ") VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE " . $_SESSION['period'] . "= VALUES(" . $_SESSION['period'] . ")");
        $stmt->bind_param("sss", $student_id, $date, $status);
        attendanceSet($conn,sizeof($_POST['attendList']));
        foreach($_POST['attendList'] as $student){
            $studentStatus = explode('-',$student);
            $student_id = $studentStatus[1];
            $status = $studentStatus[0] . '-' . $_SESSION['subjectNow'];
            $date = date('Y-m-d');
            $stmt->execute();
            echo mysqli_error($conn);
        }
    }

    if(isset($_POST['detType'])){
            $stmt = $conn->prepare("UPDATE detention SET status = ? WHERE student_id = ? AND date = ? AND detention_type = ?");
            $stmt->bind_param("ssss",$detentionStatus,$sid,$detentionDate,$detentionType);
            $detentionType = $_POST['detType'];
            $detentionDate = date('Y-m-d');
            if(isset($_POST['detAttendList'])){
                foreach($_POST['detAttendList'] as $att){
                    $detentionStatus = 'present';
                    $sid = $att;
                    $stmt->execute();
                }
            }
            if(isset($_POST['detAbsentList'])){
                foreach($_POST['detAbsentList'] as $abs){
                    $detentionStatus = 'absent';
                    $sid = $abs;
                    $stmt->execute();
                }
            }
            if(isset($_POST['detLateList'])){
                foreach($_POST['detLateList'] as $late){
                    $detentionStatus = 'late';
                    $sid = $late;
                    $stmt->execute();
                }
            }
    }

?>
<!DOCTYPE html>
<head>
    <title>
        edConnect | Classes
    </title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
<script src='scripts.js?<?php echo time(); ?>'></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<?php
require('navbar.php');
if($_SESSION['accountType'] != 'staff'){
    require('attendanceParentStudent.php');
    exit();
}
?>
<div class='row ml-0 mr-0 mb-4 col-12 third-nav'>
    <form id='class-sidebar' method='post' action='index.php'>
        <ul class='nav'>
            <li onclick='setSidebarValue("classList")' class="third-nav-item">Attendance List</li>
            <li onclick='setSidebarValue("classHistory")' class="third-nav-item"><span class='d-none d-lg-inline'>Attendance </span>History</li>
            <li onclick='setSidebarValue("classAnalytics")' class="third-nav-item"><span class='d-none d-lg-inline'>Attendance </span>Analytics</li>
            <input type='hidden' id='class-sidebar-value' name='sidebarValue'>
        </ul>
    </form>
</div>
<?php
    if($_SESSION['accountType'] == 'staff' && $_SESSION['staffPrivileges'] == 'higher'){
        echo '
        <div class="row mr-0 ml-0">
            <form method="post" action="index.php" class="col-1 offset-9">
                <input type="hidden" value="';
                echo (isset($_SESSION['principalView']) && $_SESSION['principalView'] == "true")?'false':'true';""; echo '" name="principal-view">';
                echo '<label class="switch">
                    <input type="checkbox" onchange="form.submit()"';
                    echo (isset($_SESSION['principalView']) && $_SESSION['principalView'] == "true")?'checked':''; echo '>';
                    echo '<span class="slider round"></span>
                </label>
            </form>
        </div>';
    }
    ?>
<div class='row justify-content-center mr-0 ml-0'>
<?php
 if(isset($_SESSION['classAnalytics']) || isset($_GET['student'])){
     require('attendance-stats.php');
 }
?>
<div class='col-12 col-lg-2 justify-content-left'>
<form action='index.php' method='post' id='classCode'>
    Subject : <select name='classCode' onchange='submitter("classCode")'>
<?php
    if(isset($_POST['classCode'])){
        if($_POST['classCode'] == 'Lunch Time' || $_POST['classCode'] == 'After School'){
            $detentionList = $_POST['classCode'];
        }
        else{
            $_SESSION['subjectNow'] = $_POST['classCode'];
            $classDetails = getClass($conn,$_SESSION['subjectCodes']);
            $_SESSION['period'] = $classDetails[1];
        }
        unset($_SESSION['historyPeriod']);
        unset($_SESSION['historyDate']);
    }
    else{
        $classDetails = getClass($conn,$_SESSION['subjectCodes']);
        if(!isset($_SESSION['subjectNow'])){
            $_SESSION['subjectNow'] = $classDetails[0];
           if($classDetails[1] != ""){
                $_SESSION['period'] = $classDetails[1]; 
           }
        }
    }
    foreach($_SESSION['subjectCodes'] as $subjectCode){
        if(trim($_SESSION['subjectNow']) == trim($subjectCode)){
            echo '<option selected="selected">' . trim($subjectCode) . '</option>';
        }
        else{
            echo '<option>' . trim($subjectCode) . '</option>';
        }
    }
    $detDate = date('Y-m-d');
    $sql = 'SELECT * FROM supervision WHERE staff_id="' . $_SESSION['userId'] . '" AND date ="' . $detDate . '"';
    $result = $conn->query($sql);
    if($result->num_rows >= 1){
        echo '<option disabled>------------</option>'; 
        while($row = $result->fetch_assoc()){
            if($row['type'] != 'Break Corridor' && $row['type'] != 'Lunch Corridor'){
                if(isset($detentionList) && $detentionList == trim(str_replace('Detention','',$row['type']))){
                    echo '<option selected>' . trim(str_replace('Detention','',$row['type'])) . '</option>';  
                }
                else{
                    echo '<option>' . str_replace('Detention','',$row['type']) . '</option>';  
                }
            }
        }
    }


?>
    </select>
    </form>
    </div>
    <div class='col-xs-8 col-lg-2 justify-content-left mt-3 mt-lg-0'>
    <form method='post' action='index.php' id='dateTime'>
        Date: 
        <select name='dateTime' onchange='<?php echo (isset($_SESSION['classHistory']))?'classHistorySubmitter()':'submitter("dateTime")';?>'>
            <?php
                if(isset($_SESSION['classHistory'])){
                    dateDropdown($conn,$_SESSION['subjectNow']);
                }
                else if(isset($detentionList)){
                    echo '<option>' . date('Y-m-d') . '</option>';
                    $isClassToday = false;
                }
                else{
                    if(isset($_POST['timetableToTodayPeriod'])){
                        $_SESSION['period'] = $_POST['timetableToTodayPeriod'];
                    }
                    $isClassToday = todayDropdown($conn, $_SESSION['subjectNow'],$_SESSION['period']);
                }
            ?>
        </select>
        <input type='hidden' name='historyDate' id='historyDate'>
    </form>
    </div>

    <input type='button' onclick='editAttendance()' value='edit' id='editAttendBtn' class='btn btn-primary mt-2 mt-lg-0'>
    <input type='button' value='cancel' id='cancelAttendBtn' style='display:none' onclick='cancelBtn()' class='btn btn-secondary'>
</div>
<form method='post' action='index.php' id='attendanceTable'>
<div class ='row justify-content-center mt-4 mr-0'>
<table border=1 class='col-10 col-lg-3'>
    <tr>
        <th>Name</th>
        <th>Present</th>
        
        <?php echo (isset($_SESSION['classHistory']))?'<th>Reason</th><th>Description</th>':'<th>Late</th>';?>
    </tr>
<?php
    $sql = 'SELECT name,student_id FROM students WHERE subjectCode LIKE "%' . $_SESSION['subjectNow'] . '%"';
    $attendanceList = $conn->query($sql);
    echo mysqli_error($conn);
    if(isset($_SESSION['classHistory'])){
        if(!isset($_SESSION['historyPeriod'])){
            echo '<tr></tr>';
        }
        else{
            $sql = 'SELECT students.name, attendance.' . $_SESSION['historyPeriod'] . ',attendance.reason,attendance.description,attendance.missedPeriods FROM attendance INNER JOIN students ON attendance.date = "' . $_SESSION['historyDate'] . '" AND ' . $_SESSION['historyPeriod'] . ' LIKE "%-' . $_SESSION['subjectNow'] . '" AND students.student_id = attendance.student_id';
            $historyList = $conn->query($sql);
            if(mysqli_num_rows($historyList) == 0){
                unset($_SESSION['historyDate']);
                unset($_SESSION['historyPeriod']);
                // header('location:index.php');
            }
            while($row = $historyList->fetch_assoc()){
                if(substr($row[$_SESSION['historyPeriod']],0,6) == 'absent'){
                    $reason = $row['reason'];
                    $description = $row['description'];
                }
                else{
                    $reason='';
                    $description='';
                }
                $status = explode('-',$row[$_SESSION['historyPeriod']])[0];
                $status = ($status == 'Aabsent')?'School Activity':$status;
                echo '<tr><td>' . $row['name'] . '</td><td>' . $status . '</td><td>' . $reason . '</td><td>' . $description . '</td></tr>';
            }
        }
    }
    else{
        if($isClassToday){
            $submittedList = attendanceSet($conn,mysqli_num_rows($attendanceList));
            if($submittedList != False){
                while($row = $submittedList->fetch_assoc()) {
                    echo '<script>document.getElementsByTagName("th")[2].style.display="none"</script>';
                    $attStatus = (explode('-',$row[$_SESSION['period']])[0]) == "Aabsent"?"<span data-activStudentId='"  . $row['student_id'] . "'></span>School Activity":explode('-',$row[$_SESSION['period']])[0];
                    echo '<tr class="studentAttendance"><td>' . $row['name'] . '</td><td>' . $attStatus . '<input type="hidden" name="attendList[]" value="' . $row['student_id'] . '"></td></tr>';
                }
                $editable = true;
            }
            else{
                $index = 0;
                while($row = $attendanceList->fetch_assoc()) {
                    $studentsOnActivity = onActivity($conn);
                    if(in_array($row['student_id'],$studentsOnActivity)){
                        echo '<tr class="studentAttendance"><td>' . $row['name'] . '</td><td>School Activity</td><td><input type="button" value="alter" onclick="alterActivityAttendance(event, \'' . $index . '\',\'' . $row['student_id']  . '\')"></td></tr>';
                    }
                    else{
                        echo '<tr class="studentAttendance"><td data-toggle="modal" data-target="#add-action-modal" onclick="showActionModal(event.target)" class="hover">' . $row['name'] . '</td><td><input id="attendCheck'  . $index . '" type="checkbox"/><input type="hidden" name="attendList[]" value="' . $row['student_id'] . '"></td><td><input type="checkbox" id="lateCheck'  . $index . '" onclick="setLate(\'' . $index . '\')"></td></tr>';
                    }
                    $index++;
                }
            }
        }
        else if(isset($detentionList)){
            require('detentionAttendance.php');
        }
        else{
            echo '<tr class="studentAttendance"><td colspan="3"><b>No Scheduled classes for today</b></td></tr>';
            while($row = $attendanceList->fetch_assoc()) {
                echo '<tr class="studentAttendance"><td colspan="3"s>' . $row['name'] . '</td></tr>';
            }
        }
    }
    
?>
</table>
</div>
<div class='row justify-content-center ml-0 mr-0'>
<?php
    if(isset($isClassToday) && $isClassToday && $submittedList == False){
        echo '<input type="button" onclick="getAttendance()" class="btn btn-primary mt-3" value="submit">';
    }
?>
</form>
</div>
<div class="modal fade" id="add-action-modal" tabindex="-1" role="dialog" aria-labelledby="addActionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Actions</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='closeActionModal()'>
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id='actionForm'>
            <div class="modal-body row">
            <?php echo "
                <h5 id='action-student-name' class='col-12'></h5>
                    <span class='col-12 mt-4'>Detention Date:
                        <input name='detention-date' type='date' value='" . (new DateTime('tomorrow'))->format('Y-m-d') . "'><br>
                    </span>
                    <span class='mt-4 col-12'>
                        Detention type: 
                        <select name='detention-type'>
                            <option selected='selected'>Lunch Time</option>
                            <option>After School</option>
                        </select><br>
                    </span>
                    <span class='col-12 mt-4 mb-3'>
                        reason:
                        <input type='text' name='detention-reason'><br>
                        <input type='hidden' name='detention-assigned' value='" . date('Y-m-d') . "'>
                        <input type='hidden' name='action-user' id='action-user'>
                    </span>
                    <div class='modal-footer'>
                    <button class='btn btn-primary' onclick='submitActionForm()'>Add detention</button><input type='button'class='btn btn-secondary' value='cancel' onclick='closeActionModal()'  data-dismiss='modal' aria-label='Close'>
                    </div>"
                    ;?>
            </div>
            </form>
        </div>
    </div>     
</div>
<?php 
    if(isset($editable)){
        echo '<style type="text/css">
        #editAttendBtn {
            display: block;
        }
        </style>';
    }
    else{
        echo '<style type="text/css">
        #editAttendBtn {
            display: none;
        }
        </style>';    
    }
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    function submitActionForm(){
        $.ajax({
            url:'submitAction.php',
            type:'post',
            data:$('#actionForm').serialize(),
            // success:function(){
            // }
        });
    }
</script>
</body>
</html>
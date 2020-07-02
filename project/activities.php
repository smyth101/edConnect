<?php
    session_start();
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }
    require('connection.php');
    require('common-functions.php');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST['activityName'])){
            $stmt = $conn->prepare("INSERT INTO activities (staff_id,student_id,name,description,start_date,end_date,permission_required,type,other_supervisors) VALUES (?, ?, ?, ?, ?, ?, ? ,?, ?)");
            $stmt->bind_param("ssssssiss", $_SESSION['userId'],$student_ids,$activityName,$activityDescription,$s_date,$e_date,$perm,$type,$supervisors);
            $s_date = $_POST['s_date'];
            $e_date = $_POST['e_date'];
            $activityName = $_POST['activityName'];
            $activityDescription = $_POST['activityDesc'];
            $type = $_POST['activityType'];
            if(isset($_POST['activitySupervisorId'])){
                $supervisors = implode(",",$_POST['activitySupervisorId']);
            }
            else{
                $supervisors = '';
            }
            $student_ids = implode(',',$_POST['activityMemberId']);
            $perm = $_POST['perm'];
            if($perm == 'Yes'){
                $perm = 1;
            }
            else{
                $perm = 0;
            }
            
            $stmt->execute();
        }

        if(isset($_POST['activity_id'])){
            $permList = '<table class="col-8 offset-2 col-lg-4 offset-lg-4 mb-4"><tr><th>Name</th><th>status</th></tr>';
            $sql = 'SELECT * FROM activities WHERE activity_id = ' . $_POST['activity_id'];
            $result=$conn->query($sql);
            $row=$result->fetch_assoc();
            $studentList = explode(',',$row['student_id']);
            $permittedList =explode(',',$row['permission_list']);
            foreach($studentList as $student){
                if(in_array($student, $permittedList)){
                    $status = 'permitted';
                }
                else{
                    $status = 'not permitted';
                }
                $sqlName = 'SELECT name FROM students WHERE student_id = "' . $student . '"';
                $resultName = $conn->query($sqlName);
                $rowName = $resultName->fetch_assoc();
                $permList .= '<tr><td>' . $rowName['name'] . '</td><td>' . $status . '</td></tr>';
            }
        }

        if(isset($_POST['permissionGranted'])){
            $sql = 'SELECT * FROM activities WHERE activity_id = ' . $_POST['permissionGranted'];
            $result = $conn->query($sql);
            $row=$result->fetch_assoc();
            if($row['permission_list'] ==''){
                $oldList = [];
            }
            else{
                $oldList = explode(',',$row['permission_list']);
            }
            array_push($oldList,$_SESSION['userId']);
            $updatedPerm = implode(',',$oldList);
            $sql = 'UPDATE activities SET permission_list = "' . $updatedPerm . '" WHERE activity_id = ' . $_POST['permissionGranted'];
            $conn->query($sql);
        }

        if(isset($_POST['activId'])){
            if(!isset($_POST['activityAttendStudents'])){
                $studentList = '';
            }
            else{
                $studentList = implode(',',$_POST['activityAttendStudents']);
            }
            $stmt = $conn->prepare('UPDATE  activities SET attendance = ? WHERE activity_id = ?');
            $stmt->bind_param('si',$studentList,$_POST['activId']);
            $stmt->execute();

            $sql = 'SELECT * FROM activities where activity_id = "' . $_POST['activId'] . '"';
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $students = explode(',',$row['attendance']);
            foreach($students as $student){
                $sql = 'SELECT subjectCode FROM students WHERE student_id = "' . $student . '"';
                $studentResults = $conn->query($sql);
                $studentDetails = $studentResults->fetch_assoc();
                $subjCode = $studentDetails['subjectCode'];
                $subjCode = explode(",",$subjCode);
                $subjCode = implode('","',$subjCode);
                $startDate = $row['start_date'];
                $endDate = $row['end_date'];
                $activityDays = [];
                $activityPeriod = new DatePeriod(
                    new DateTime($startDate),
                    new DateInterval('P1D'),
                    new DateTime($endDate)
                );
                if(iterator_count($activityPeriod) < 3){
                    $activityPeriod = [];
                }
                foreach ($activityPeriod as $key => $value) {
                    if($key != 0 && $key !=  (iterator_count($activityPeriod)-1)){
                        $midDate = $value->format('Y-m-d');
                        $dayOfWeek = strtolower(date("l", strtotime($midDate)));
                        $activityDays[$midDate]=$dayOfWeek;
                    }
                }
                $startDay = strtolower(date("l", strtotime($startDate)));
                $endDay = strtolower(date("l", strtotime($endDate)));
                $startTime = date("H:i:s",strtotime($startDate));
                $endTime = date("H:i:s",strtotime($endDate));
                if($startDay == $endDay){
                    $sql = 'SELECT * FROM timetable WHERE (day = "'. $startDay . '" AND start_time >= "' . $startTime . '") AND (day = "'. $endDay . '" AND start_time <= "' . $endTime . '") AND subjectCode IN ("' . $subjCode . '")';
                }
                else{
                    $sql = 'SELECT * FROM timetable WHERE ((day = "'. $startDay . '" AND start_time >= "' . $startTime . '") OR (day = "'. $endDay . '" AND start_time <= "' . $endTime . '")) AND subjectCode IN ("' . $subjCode . '")';
                }
                $timetableResults = $conn->query($sql);
                while($ttRow = $timetableResults->fetch_assoc()){
                    $stmt = $conn->prepare('INSERT INTO attendance (student_id,date,period' . $ttRow['period'] . ') VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE period' . $ttRow['period'] . ' = IF(period' . $ttRow['period'] . ' NOT LIKE "present-%",VALUES(period' . $ttRow['period'] . '),period' . $ttRow['period'] . ')');
                    $stmt->bind_param('sss',$student,$aDate,$p);
                    if($ttRow['day'] == $startDay){
                        $aDate = $startDate;
                    }
                    else{
                        $aDate = $endDate;
                    }
                    $p = 'Aabsent-' . $ttRow['subjectCode'];
                    $stmt->execute();
                }
                foreach($activityDays as $midDay){
                    $sql = $sql = 'SELECT * FROM timetable WHERE (day = "'. $midDay . '" AND subjectCode IN ("' . $subjCode . '")';
                    $timetableResults = $conn->query($sql);
                    while($ttRow = $timetableResults->fetch_assoc()){
                        $stmt = $conn->prepare('INSERT INTO attendance (student_id,date,period' . $ttRow['period'] . ') VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE period' . $ttRow['period'] . ' = IF(period' . $ttRow['period'] . ' NOT LIKE "present-%",VALUES(period' . $ttRow['period'] . '),period' . $ttRow['period'] . ')');
                        $stmt->bind_param('sss',$student,$aDate,$p);
                        if($ttRow['day'] == $startDay){
                            $aDate = $startDate;
                        }
                        else{
                            $aDate = $endDate;
                        }
                        $p = 'Aabsent-' . $ttRow['subjectCode'];
                        $stmt->execute();
                    }
                }                

            }
       
        }
    }
    
?>

<!DOCTYPE html>
<head>
    <title>
        edConnect | Activities
    </title>
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src='scripts.js?<?php echo time(); ?>'></script>
</head>
<body>

<?php
require('navbar.php');
if($_SESSION['accountType'] == 'staff'){
    echo 
    '<form action="activities.php" method="post">
        <div class="row justify-content-center mt-3 ml-0 mr-0">
            <h3>Add Activity</h3>
        </div>
        <div class="row justify-content-center mt-2 ml-0 mr-0">
            <div class="col-12 col-lg-3 text-center">
                Type: 
                <select name="activityType">
                    <option>Sporting</option>
                    <option>Subject</option><option>
                    extra-curricular</option>
                </select>
            </div>';

   echo '
        <div class="col-12 col-lg-3 text-center mt-3 mt-lg-0">other supervisors:
        <input type="text"  onkeyup="showResult(this.value,\'supervisorsearch\')" placeholder=\'search\'>
        <div id="supervisorsearch"></div>
        <span id="supervisorHeader" style="display:none">supervisors 
        <input type=\'button\' value=\'clear\' onclick=\'clearActivitySupervisors()\' class="btn btn-secondary"></span>
        <div id=\'supervisors\'>
            <div id=\'creator\'>
            </div>
        </div></div>
        <div class="col-12 col-lg-3 text-center mt-3 mt-lg-0">
            <span class="col-12 col-lg-6">Search by name or subject:</span>
            <input type="text"  onkeyup="showResult(this.value,\'membersearch\')" placeholder=\'search\'>
            <div id="membersearch"></div>
            <span id="memberHeader" style="display:none">
                members
                <input type=\'button\' value=\'clear\' onclick=\'clearActivityMembers()\' class="btn btn-secondary">
                <div id=\'members\'>
                    <div id=\'creator\'>
                    </div>
                </div>
            </span>
        </div>
    </div>
    <div class="row justify-content-center text-center mt-3 ml-0 mr-0">
    <div class="col-12 offset-0 col-lg-2 offset-lg-1 mt-3">Activity name: <input type=\'text\' name=\'activityName\' required></div>
    <div class="col-12 col-lg-2 mt-3">Description: <textarea required name=\'activityDesc\'></textarea></div>
    <div class="col-12 col-lg-2 mt-3">Start date:<input type=\'datetime-local\' name=\'s_date\' required></div>
    <div class="col-12 col-lg-2 mt-3">End Date:<input type=\'datetime-local\' name=\'e_date\' required></div>
    <div class="col-12 col-lg-2 mt-3">Permission Required: <select name=\'perm\'><option>No</option><option>Yes</option></select></div>
    <div class="col-1 mt-3 text-lg-left"><button class="btn btn-primary">Create</button></div>
    </form>
    </div>';
}

    if($_SESSION['accountType'] == 'parent'){
        echo '
        <div class="row justify-content-center mt-4 ml-0 mr-0">
            <h3>Permission Required</h3>
        </div>
        <div class="row justify-content-center mt-3 mr-0 ml-0">
        <form action="activities.php" method="post">
            <table>';
        $sql = 'SELECT * FROM activities WHERE end_date >= CURDATE() AND permission_required = 1 ORDER BY start_date ASC';
        $result = $conn->query($sql);
        if($result->num_rows==0){
            echo '<tr><td>No scheduled activities requiring permission</td></tr>';
        }
        else{
            while($row=$result->fetch_assoc()){
                if(!in_array($_SESSION['userId'],explode(',',$row['permission_list']))){
                    echo '<tr><td>' . $row['name'] . '</td><td><button>Grant Permission</button><input name="permissionGranted" type="hidden" value="' . $row['activity_id'] . '"></td></tr>';
                }
            }
        }
        echo '</table></form></div>';
    }

?>
<div class="row justify-content-center mt-5 mr-0 ml-o">
    <h3>Upcoming Activities</h3>
</div>
<div class="row justify-content-center text-center mr-0 ml-0">
<div id='permList' class='col-12'>
<?php if(isset($permList)){
    echo $permList;
}
?>
</div>
<div class='table-responsive col-10 col-lg-6'>
    <table class='col-12'></tr><th>Name</th><th>Description</th><th>start time</th><th>end time</th><th></th></tr>
    <?php 
    $sql = 'SELECT * FROM activities WHERE end_date >= CURDATE() AND (staff_id = "' . $_SESSION['userId'] . '" OR student_id LIKE "%,' . $_SESSION['userId'] . ',%" OR student_id LIKE "%,' . $_SESSION['userId'] . '" OR student_id LIKE "' . $_SESSION['userId'] . ',%" OR student_id LIKE "' . $_SESSION['userId'] . '" OR other_supervisors LIKE "%,' . $_SESSION['userId'] . ',%" OR other_supervisors LIKE "%,' . $_SESSION['userId'] . '" OR other_supervisors LIKE "' . $_SESSION['userId'] . ',%" OR other_supervisors LIKE "' . $_SESSION['userId'] . '") ORDER BY start_date ASC';
    $result = $conn->query($sql);
    while($row=$result->fetch_assoc()){
        if($_SESSION['accountType'] == 'staff' && ($row['staff_id'] == $_SESSION['userId'] || in_array($_SESSION['userId'],explode(',',$row['other_supervisors'])))){
            if(substr($row['start_date'],0,-9) == date("Y-m-d")){
                if($row['attendance'] == ''){
                    $btnTitle = 'take attendance';
                }
                else{
                    $btnTitle = 'edit attendance';
                }
                $attBtn = '<form method="post" action="activities.php"><input type="submit" value="'  . $btnTitle .'" class="btn btn-primary"><input type="hidden" name="activityAttend" value='  . $row['activity_id'] . '></form>';
            }
            else{
                $attBtn = '';
            }
            if( $row['permission_required'] == 1){
                $permBtn = '<span onclick="viewActivityPerm(\'' . $row['activity_id'] . '\')"><u style="cursor:pointer">view permission stasuses</u></span>';
            }
            else{
                $permBtn = '';
            }
            $permCell = $attBtn . $permBtn;
        }
        else if($_SESSION['accountType'] == 'student' || $_SESSION['accountType'] == 'parent' ){
            if(in_array($_SESSION['userId'],explode(',',$row['permission_list']))){
                $permCell = 'permission granted';
            }
            else if($row['permission_required'] == 1){
                $permCell = 'permission required';
            }
            else{
                $permCell = '';
            }
        }
        else{
            $permCell = '';
        }
        echo '<tr><td>' . $row['name'] . '</td><td>' . $row['description'] . '</td><td>' . $row['start_date'] . '</td><td>' . $row['end_date'] . '</td><td>' . $permCell . '</td></tr>';
    }
    echo '</table>';
    if(isset($_POST['activityAttend'])){
        echo '<form action="activities.php" method="post" class="col-8 col-lg-4 offset-2 offset-lg-4 mt-3"><table class="col-12"><tr><th>Name</th><th>Present</th><th></th></tr>';
        $sql = 'SELECT * FROM activities WHERE activity_id=' . $_POST['activityAttend'];
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $studentIds = explode(",",$row['student_id']);
        $perms = explode(',',$row['permission_list']);
        $permReq = $row['permission_required']; 
        if($row['attendance'] != ''){
            $att = explode(',',$row['attendance']);
        }
        else{
            $att = [];
        }
        foreach($studentIds as $student){
            if(in_array($student,$att)){
                $selected = 'checked';
            }
            else{
                $selected = '';
            }
            $sqlName = 'SELECT name FROM students WHERE student_id = "' . $student . '"';
            $resultName = $conn->query($sqlName);
            $rowName = $resultName->fetch_assoc();
            if($permReq == 0 || in_array($student,$perms)){
                echo '<tr><td>' . $rowName['name'] . '</td><td><input type="checkbox" value="' . $student . '" name="activityAttendStudents[]" ' . $selected . '></td></tr>';
            }
            else{
                echo '<tr><td>' . $rowName['name'] . '</td><td><input type="checkbox" value="' . $student . '" name="activityAttendStudents[]" disabled></td><td>Permission was not given</td></tr>';
            }
            
        }
    echo '</table><input type="hidden" name="activId" value=' . $_POST['activityAttend'] . '><button class="mt-2 btn btn-primary">Submit</button><button type="button" class="btn btn-secondary mt-2 ml-4" onclick="event.target.parentNode.parentNode.removeChild(event.target.parentNode.parentNode.lastChild)">Cancel</button></form></div>';
}
?>
</body>
</html>
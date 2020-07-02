<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    require('info.php');
    
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }

    
   principalViewCheck($conn);

    if(isset($_POST['gradeDate'])){
        $gradeDate = $_POST['gradeDate'];
    }
    if(isset($_POST['classCode'])){
        $_SESSION['subjectNow'] = $_POST['classCode'];
    }

    if(isset($_GET['gradePage'])){
        $_SESSION['gradePage'] = $_GET['gradePage'];
    }

    if(!isset($_SESSION['gradePage'])){
        $_SESSION['gradePage'] = 'addGrade';
    }

    if($_SESSION['accountType'] != 'staff' && !isset($_GET['student']) && $_SESSION['gradePage'] == 'analytics'){
        header('location:grades.php?gradePage=analytics&student="' . $_SESSION['userId'] . '"');
    }


    if(isset($_POST['addGradeId'])){
        $stmt = $conn->prepare("INSERT INTO grades (student_id,mark, date, subjectCode) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $addGradeId,$addGradeMark,$addDate,$_SESSION['subjectNow']);
        for($i = 0; $i < count($_POST['addGradeId']); $i++)
        {
            $addGradeId = $_POST['addGradeId'][$i];
            $addGradeMark = $_POST['addGradeMark'][$i];
            $addDate = $_POST['dateSelect'];
            echo $conn->error;
            $x = 1;
            echo $stmt->error;
            $stmt->execute();
        }
        $sql = 'UPDATE classactivities SET marked = 1 WHERE date = "' . $addDate . '"';
        $conn->query($sql); 
        $_SESSION['gradePage'] = 'pastGrade';
    }

    if(isset($_POST['changeMarkReason'])){
        $stmt = $conn->prepare("UPDATE grades SET mark = ?, reason = concat(reason ,  ?) WHERE student_id = ? AND subjectCode = ? AND date = ?");
        $stmt->bind_param("issss", $grade, $changeReason, $studentId, $_SESSION['subjectNow'],$_POST['gradeDate']);
        echo $stmt->error;
        foreach($_POST['changeMarkReason'] as $key=>$reason){
            if($reason != ""){
                $studentId = $_POST['changeMarkStudent'][$key];
                $grade = $_POST['changeMark'][$key];
                $changeReason = 'grade changed from ' . $_POST['originalMark'][$key] . '. ' . $reason;
                $stmt->execute();
            }
        }
    }



    if(isset($_POST['testDate'])){
        $stmt = $conn->prepare("INSERT INTO classactivities (staff_id,subjectCode,date,description,marked,testType,SchoolTestType) VALUES (?, ?, ?, ?, ?, ? ,?)");
        $stmt->bind_param("ssssiss",$_SESSION['subjectNow'], $_POST['classCode'], $_POST['testDate'], $_POST['testDescription'],$marked ,$_POST['testType'],$schoolTestType);
        $marked = 0;
        if(isset($_POST['schoolTestType'])){
            $schoolTestType = $_POST['schoolTestType'];
        }
        else{
            $schoolTestType = '';
        }
        $stmt->execute();
        $_SESSION['gradePage'] = 'addGrade';
    }
?>
<!DOCTYPE html>
<head>
    <title>edConnect | grades</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <script src="../node_modules/chart.js/dist/Chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
</head>
<body>
<?php
require('navbar.php');
echo "<div class='row ml-0 col-12 third-nav mr-0 mb-4'>";
if($_SESSION['accountType'] == 'staff'){
    echo "
        <form action='grades.php' method='get' id='gradePageForm1'>
        <li onclick=\"document.getElementById('gradePageForm1').submit();\" class='third-nav-item'>Add Test</li>
        <input type='hidden' value='addTest' name='gradePage'>
        </form>
        <form action='grades.php' method='get' id='gradePageForm2'>
        <li onclick=\"document.getElementById('gradePageForm2').submit();\" class='third-nav-item'>Add Grades</li> 
        <input type='hidden' value='addGrade' name='gradePage'>
        </form>";
}
else{
    if($_SESSION['gradePage'] != 'analytics' && $_SESSION['gradePage'] != 'pastGrade'){
        $_SESSION['gradePage'] = 'pastGrade';
    }
}
?>
    <form action='grades.php' method='get' id='gradePageForm3'>
    <li onclick="document.getElementById('gradePageForm3').submit();" class='third-nav-item'><span class='d-none d-lg-inline'>Past Grades</span><span class='d-inline d-lg-none'>History</span></li> 
    <input type='hidden' value='pastGrade' name='gradePage'>
    </form>
    <form action='grades.php' method='get' id='gradePageForm4' class='third-nav-item'>
    <li onclick="document.getElementById('gradePageForm4').submit();"><span class='d-none d-lg-inline'>Grade </span>Analytics</li> 
    <input type='hidden' value='analytics' name='gradePage'>
    <?php 
        if($_SESSION['accountType'] != 'staff'){
            echo "<input type='hidden' name='student' value='\"" . $_SESSION['userId'] . "\"'>";
    }
    ?>
    </form>
</div>
<?php
if($_SESSION['accountType'] == 'staff' && $_SESSION['staffPrivileges'] == 'higher'){
    
    echo '<div class="row mr-0 ml-0">';
    if($_SESSION['gradePage'] == 'pastGrade' && $_SESSION['accountType'] == 'staff'){
        echo '
        <span class="offset-lg-5 col-lg-2 offset-3 col-3">
            <input type="button"value="show distribution" onclick="showGradeDist()" class="btn btn-primary ml-2" id="distBtn" data-toggle="modal" data-target="#distModal">
        </span>
        <form method="post" action="index.php"  class="col-1 offset-3 offset-lg-2">';
    }
    else if($_SESSION['gradePage'] == 'analytics' && $_SESSION['accountType'] == 'staff' && !isset($_GET['student'])){
        if($previousStartDate != null && !isset($_GET['student'])){
            echo '<form action="grades.php" method="get" class="col-7 offset-2 col-lg-2 offset-lg-5"><button class="btn btn-primary">';
            if(!isset($_GET['compare']) || $_GET['compare'] == "false"){
                echo 'Compare Previous Year</button><input type="hidden" name="compare" value=true>';
            }
            else{
                echo 'Hide Previous Year</button><input type="hidden" name="compare" value=false>';
            }
            echo '</form>
            <form method="post" action="grades.php"  class="col-1 offset-lg-2">';
        }
    }
    else{
        echo '<form method="post" action="grades.php" class="col-1 offset-9">';
    }
    echo '
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
 <?php
    if($_SESSION['gradePage'] == 'analytics'){
        require('grade-stats.php');
    }
    echo "<div class='row justify-content-center text-center ml-0 mr-0'>";
    if($_SESSION['accountType'] == 'staff'){
        echo "<form action='grades.php' method='post' id='classCode'>
           <span class='col-12'> Subject: <select name='classCode' onchange='submitter(\"classCode\")' id='classCodeDrpdwn'>";
     
        foreach($_SESSION['subjectCodes'] as $subjectCode){
            if(isset($_POST['classCode']) && $_POST['classCode'] == 'All'){
                echo '<option>' . trim($subjectCode) . '</option>';
            }
            else{
                if(trim($_SESSION['subjectNow']) == trim($subjectCode)){
                    echo '<option selected="selected">' . trim($subjectCode) . '</option>';
                }
                else{
                    echo '<option>' . trim($subjectCode) . '</option>';
                }
            }
        }
    }
    else{
        $sql = 'SELECT year FROM students WHERE student_id="' . $_SESSION['userId'] . '"';
        $result=$conn->query($sql);
        $row = $result->fetch_assoc();
        $year = $row['year'];
        if(isset($_POST['testYear'])){
            $selectedYear = $_POST['testYear'];
        }
        else{
            $selectedYear = $year;
        }
        if(isset($_POST['testSubject'])){
            $testSubject = $_POST['testSubject'];
            $_SESSION['subjectNow'] = $_POST['testSubject'];
        }
        echo '<form action="grades.php" method="post"><span class="col-3"> Subject: <select name="testSubject" onchange="form.submit()">';
        $sql = 'SELECT subjectCode FROM grades WHERE subjectCode LIKE "___' . $selectedYear . '%" GROUP BY subjectCode';
        $result = $conn->query($sql);
        while($row=$result->fetch_assoc()){
            if(!isset($testSubject)){
                $testSubject = $_SESSION['subjectNow'];
            }
            if($testSubject == $row['subjectCode']){
                echo '<option selected>' . $row['subjectCode'] . '</option>';
            }
            else{
                echo '<option>' . $row['subjectCode'] . '</option>';
            }
        }
        echo $sql;
    }
    
    echo '</select></span>';
    if($_SESSION['gradePage'] == 'addTest'){
        echo '<span class="mt-5 col-12">Date of test: <input type="date" name="testDate" required></span>
        </div>
        <div class="row justify-content-center text-center">
            <span class="col-12 col-lg-3">
                Description: <textarea class="mt-4" name="testDescription" required></textarea>
            </span>
            <span class="col-12 col-lg-2 mt-4">
                type: <select onchange="changedTestType(event.target)" name="testType">
                    <option>Class test</option>
                    <option>School test</option>
                </select>
            </span>
            <span class="col-12 col-lg-2 mt-4" id="classTestdrpdwn"  style="display:none">
                <select name="schoolTestType" disabled>
                    <option>Christmas Exam</option>
                    <option>Summer Exam</option>
                    <option>Mock Exam</option>
                    <option>Mock Oral Exam</option>
                </select>
            </span>
            <button class="btn btn-primary mt-4" style="height:40px">Add</button>
        </div>';
    }
    echo '</form>'; 
    if($_SESSION['gradePage'] == 'pastGrade'){
        if($_SESSION['accountType'] == 'staff'){
            $sql = 'SELECT date FROM grades where subjectCode = "' . $_SESSION['subjectNow'] . '" AND date BETWEEN "' . $schoolStartDate . '" AND "' . $schoolEndDate .'" GROUP BY date ORDER BY date DESC';
            $result = $conn->query($sql);
            echo '<form action="grades.php" method="post">test date: <select name="gradeDate" onchange="this.form.submit()">';
            while($row = $result->fetch_assoc()){
                if(!isset($gradeDate)){
                    $gradeDate = $row['date']; 
                    echo '<option selected>' . $row['date'] . '</option>';
                }
                else{
                    if($gradeDate == $row['date']){
                        echo '<option selected>' . $row['date'] . '</option>';
                    }
                    else{
                        echo '<option>' . $row['date'] . '</option>';
                    }
                }
            }
            echo '</select>';
            echo '</div>';
            echo '<div class="modal fade" id="distModal" tabindex="-1" role="dialog" aria-labelledby="distModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="distModalLabel">Grade Distribution</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="document.getElementById(\'chartContainer\').remove()">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="document.getElementById(\'chartContainer\').remove()">Close</button>
                </div>
              </div>
            </div>
          </div>';
            echo '</form>';
            if(isset($gradeDate)){
                echo '<div class="row"><input type="button" value="edit" id="editBtn" onclick="editGrades()" class="btn btn-secondary offset-3"><input type="button" value="cancel" onclick="location.reload()" class="btn btn-secondary offset-3" id="cancelBtn" style="display:none"></div>';
                $sql = 'SELECT description,testType,schoolTestType FROM classactivities WHERE subjectCode="' . $_SESSION['subjectNow'] . '" AND date = "' . $gradeDate .  '"';
                $result = $conn->query($sql);
                $row=$result->fetch_assoc();
                if($row['description'] != '' || $row['testType'] == 'School test'){
                    echo '<div class="row justify-content-center">';
                    if($row['testType'] == 'School test'){
                        echo $row['schoolTestType'];
                        $sType=$row['schoolTestType'];
                    }
                    echo ' : ' . $row['description'];
                    echo '</div>';
                }
                echo '<div class="row justify-content-center">
                    <table class="table table-striped col-lg-6 col-10 text-center">
                        <tr>
                            <th>Name</th>
                            <th>Grade</th>
                            <th>Reason</th>
                        </tr>';
                if(isset($gradeDate)){
                    $sql = 'SELECT grades.*,students.name FROM grades LEFT JOIN students ON grades.student_id = students.student_id WHERE grades.subjectCode = "' . $_SESSION['subjectNow'] . '" and grades.date = "' . $gradeDate .  '"';
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()){
                        echo '<tr><td data-gredit="' . $row['student_id'] . '">' . $row['name'] . '</td><td>' . $row['mark'] . '</td><td>' . $row['reason'] . '</td></tr>';
                    }
                }
                echo '</table></div>';
            }
            else{
                echo '<div class="row justify-content-center"><span>No grades recorded for this subject</span></div>';
            }
        }
        else{
            echo '<form action="grades.php" method="post"><select onchange="form.submit()" name="testYear">';
            foreach(range($year,1) as $y){
                if($y == 1){
                    $yString = '1st year';
                }
                else if($y == 2){
                    $yString = '2nd year';
                }
                else if($y == 3){
                    $yString = '3rd year';
                }
                else{
                    $yString = $y . 'th year';
                }
                if(isset($_POST['testYear']) && $y == $_POST['testYear']){
                    echo '<option value=' . $y . ' selected>' . $yString . '</option>';    
                }
                else{
                    echo '<option value=' . $y . '>' . $yString . '</option>';
                }
            }
            echo '</select>';
            echo '<span class="ml-3">Class tests:<input type="checkbox" value="Class test"  name="classTestCheck" onchange="form.submit()"';
            echo (isset($_POST['classTestCheck']) || !isset($_POST['testYear']) || (isset($_POST['testYear']) && !isset($_POST['schoolTestCheck']) && !isset($_POST['classTestCheck'])))?' checked':'';
            echo '></span>';
            echo '<span class="ml-3">School tests:<input type="checkbox" value="School test"  name="schoolTestCheck" onchange="form.submit()"';
            echo (isset($_POST['schoolTestCheck']) || !isset($_POST['testYear']) || (isset($_POST['testYear']) && !isset($_POST['schoolTestCheck']) && !isset($_POST['classTestCheck'])))?' checked':'';
            echo '></span>';
            echo '</div></form>';
            echo '<div class="row justify-content-center ml-0 mr-0"><div" class="col-11 col-lg-6 table-responsive"><table  class="text-center table table-striped mt-3">
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>mark</th>
                        <th class="reason-col">reason for change</th>
                    </tr>';
            if(isset($_POST['testYear'])){

                if(isset($_POST['classTestCheck']) && !isset($_POST['schoolTestCheck'])){
                    $checkFilter = ' AND testType="Class test"';
                }
                else if(isset($_POST['schoolTestCheck']) && !isset($_POST['classTestCheck'])){
                    $checkFilter = ' AND testType="School test"';
                }
                else{
                    $checkFilter = '';
                }
            }
            else{
                $checkFilter = '';
            }
            if(isset($testSubject)){
                $sql = 'SELECT * FROM grades as g LEFT JOIN classactivities as ca ON g.date = ca.date AND g.subjectCode = ca.subjectCode WHERE student_id = "' . $_SESSION['userId'] . '" AND g.subjectCode = "' . $testSubject . '"' . $checkFilter . ' ORDER BY g.date DESC';
                $result = $conn->query($sql);
                $reasonSet = false;
                while($row = $result->fetch_assoc()){
                    echo '<tr>';
                    echo '<td>' . $row['date'] . '</td>';
                    echo '<td>' . $row['subjectCode'] . '</td>';
                    if($row['testType'] == 'School test'){
                        $description = $row['schoolTestType'] . ' : ' . $row['description'];
                    }
                    else{
                        $description = $row['description'];
                    }
                    echo '<td>' . $description . '</td>';
                    echo '<td>' . $row['mark'] . '</td>';
                    echo '<td class="reason-col col-2">' . $row['reason'] . '</td>';
                    echo '</tr>';
                    if($row['reason'] != ''){
                        $reasonSet = true;
                    }
                }
                echo '</table></div></div>';
                if(!$reasonSet){
                    echo '<script>reasons = document.getElementsByClassName("reason-col");
                    for(i=0;i < reasons.length;i++){
                        reasons[i].style.display = "none";   
                    }</script>';
                }
            }
            else{
                echo '<tr><td colspan=5>No grades recorded for selected year</td></tr></table></div>';
            }
        }

    }
    if($_SESSION['gradePage'] == 'addGrade'){
        $sql = 'SELECT name,student_id FROM students WHERE subjectCode LIKE "%' . $_SESSION['subjectNow'] . '%"';
        $result = $conn->query($sql);
        $sqlDate = 'SELECT date FROM classactivities WHERE subjectCode = "'. $_SESSION["subjectNow"] . '" and marked = 0';
        $resultDate = $conn->query($sqlDate);
        $dateSelect = '<select name="dateSelect">';
        while($row = $resultDate->fetch_assoc()){
            $dateSelect .=  '<option>' . $row['date'] . '</option>';
        }
        $dateSelect .= '</select></div><div class="row justify-content-center mt-3">';
        if($dateSelect == '<select name="dateSelect"></select></div><div class="row justify-content-center mt-3">'){
            echo '</div><div class="row justify-content-center mt-3"><b>No tests scheduled</b></div>';
        } 
        else{
            echo '<form action="grades.php" method="post" class="mt-3 mt-lg-0"><span class="col-3">date of test: ' . $dateSelect . '</span><br><table class="col-6 table table-striped text-center"><tr><th>Name</th><th>Mark (%)</th></tr>';
            while($row = $result->fetch_assoc()){
                echo '<tr><td>' . $row['name'] . '<input type="hidden" value="' . $row['student_id'] . '" name="addGradeId[]"></td><td><input type="number" name="addGradeMark[]"></tr>';
            }
            echo '</table></div><span class="row justify-content-center"><button class="btn btn-primary">Submit</button></form></span>';  
        }
    }
    if(isset($sType)){
        $sql = 'SELECT date FROM classactivities WHERE subjectCode="' . $_SESSION['subjectNow'] . '" AND date BETWEEN "' . $previousStartDate . '" AND "' . $previousEndDate . '" AND testType="School test" AND schoolTestType="' . $sType . '"';
        $result=$conn->query($sql);
        if($result->num_rows != 0){
            $row = $result->fetch_assoc();
            $prevDate = $row['date'];
            $prevResult = [];
            $sql = 'SELECT mark FROM grades WHERE subjectCode="' . $_SESSION['subjectNow'] . '" AND date ="' . $prevDate . '"';
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()){
                array_push($prevResult,$row['mark']);
            }
            echo '<script>var prevResultArray = [' . implode(',',$prevResult) . '];compareBool=false</script>';
        }
    }
?>

</body>
</html>

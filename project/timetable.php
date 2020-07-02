<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }

    principalViewCheck($conn);
?>
<!DOCTYPE html>
<head>
    <title>edConnect | timetable</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<?php
require('navbar.php');
?>
<div class='row ml-0 mr-0 mt-3'>
    <?php 
    if(isset($_SESSION['principalView']) && $_SESSION['principalView'] =='true' && !isset($_SESSION['principalViewStaff'])){

        echo "
        <form action='timetable.php' method='post' class='offset-9 col-1'>
            <span>
                Subject Code:
                <select name='principalSC' onchange='form.submit()'>";
                        foreach($_SESSION['subjectCodes'] as $sc){
                            if(isset($_POST['principalSC']) && $_POST['principalSC'] == $sc){
                                echo '<option selected>' . $sc . '</option>';
                            }
                            else{
                                echo '<option>' . $sc . '</option>';
                            }
                        }
                echo "
                </select>
            </span>
        </form>";
    }
    ?>
    <?php
        if($_SESSION['accountType'] == 'staff' && $_SESSION['staffPrivileges'] == 'higher'){
            echo '
            <div class="col-12">
                <form method="post" action="timetable.php" class="offset-9 col-1">
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
</div>
<div id='timetable-container' class='table-responsive'>
    <!--assumption is max of 9 periods in a day-->
<table border="1" class='table'>
    <tr>
        <th>Period</th>
        <th>Monday</th>
        <th>Tuesday</th>
        <th>Wednesday</th>
        <th>Thursday</th>
        <th>Friday</th>
        <th>Saturday</th>
        <th>Sunday</th>
    </tr>
        
<?php
    if(isset($_SESSION['principalView']) && $_SESSION['principalView'] == 'true' && !isset($_SESSION['principalViewStaff'])){
        if(isset($_POST['principalSC'])){
            $subjectCodes = $_POST['principalSC'];
        }
        else{
            $subjectCodes = $_SESSION['subjectCodes'][0];
        }
    }
    else if(isset($_GET['student'])){
        $sql = 'SELECT subjectCode FROM students WHERE student_id=' . $_GET['student'] . '';
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $subjectCodes = $row['subjectCode'];
    }
    else{
        $subjectCodes = implode('","', $_SESSION['subjectCodes']);
    }
    $sql='SELECT * FROM timetable WHERE subjectCode IN ("' . $subjectCodes . '") ORDER BY start_time ASC;';
    $result = $conn->query($sql);
    echo mysqli_error($conn);
    $daysOfWeek = array(
        'monday'=>[],
        'tuesday'=>[],
        'wednesday'=>[],
        'thursday'=>[],
        'friday'=>[],
        'saturday'=>[],
        'sunday'=>[]
    );
    $colours = ['rgba(242, 38, 38, .4)','rgba(242, 102, 38, 0.4)','rgba(242, 208, 38, 0.4)','rgba(38, 242, 225, 0.2)','rgba(38, 133, 242, 0.2)','rgba(72, 38, 242, 0.2)','rgba(242, 38, 38, .4)'];
    $index = 0;
    $colourMap = [];
    while($row = $result->fetch_assoc()) {
        if(!isset($colourMap[$row['subjectCode']])){
            $colourMap[$row['subjectCode']] = $colours[$index];
            $index++;
        }
        array_push($daysOfWeek[$row['day']],[$row['subjectCode'],$row['period'],$row['start_time'],$row['end_time'],$row['room'],$row['subject']]);
    }

    $currentPeriod = 1;
    // assumption of max class per day
    for($slot=1;$slot<10;$slot++){
        echo '<tr><td>' . $slot . '</td>';
        foreach($daysOfWeek as $day){
            $freeClass = True;
            foreach($day as $key=>$subject){
                if($subject[1] == $slot){
                    $timePeriod = '<span class="timetable-timestamp" onclick="timetableToAttend(this,\'' . $subject[0] . '\',true)">' . $subject[2] . '-<br>' . $subject[3] . '</span>';
                    $titleClass = '<span class="timetable-subject" onclick="timetableToAttend(this,\'' . $subject[0] . '\')">' . $subject[5] . '</span>';
                    $subjectCode = '<span class="timetable-CodeAndRoom"><span onclick="timetableToAttend(this,\'' . $subject[0] . '\')">' . $subject[0] . '</span><br>';
                    $room = $subject[4] . '</span>'; 
                    echo '<td style="background-color:' . $colourMap[$subject[0]] . ';">' . $timePeriod . $titleClass . $subjectCode . $room . '</td>';
                    $freeClass = False;
                break;
                }
            }
            if($freeClass == True){
                echo '<td></td>';
            }
        }
        echo '</tr>';
        if($slot == 3){
            echo '<tr><td colspan="8">Break</td></tr>';
        }
        if($slot == 6){
            echo '<tr><td colspan="8">Lunch</td></tr>';
        }
    }


?>
</table>
<form id='timetableForm' action='index.php' method='post'>
    <input name='timetableSubjectCode' type='hidden' id='timetableAttendInput'>
</form>
</form>
</div>
</body>
</html>
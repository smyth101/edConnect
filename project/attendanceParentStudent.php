<?php
    require('info.php');
    if(isset($_POST['reasonForAbs'])){
        $stmt = $conn->prepare('UPDATE attendance SET reason = ?,description = ? WHERE student_id = ? AND date = ?');
        echo $conn->error;
        $stmt->bind_param('ssss',$_POST['reasonForAbs'],$_POST['descriptionForAbs'],$_SESSION['userId'],$_POST['dateOfAbs']);
        $stmt->execute();
    }

    $sql = 'SELECT date FROM attendance WHERE student_id = "' . $_SESSION['userId'] . '" AND (period1 LIKE "absent%" || period2 LIKE "absent%" || period3 LIKE "absent%" || period4 LIKE "absent%" || period5 LIKE "absent%" || period6 LIKE "absent%" || period7 LIKE "absent%" || period8 LIKE "absent%" ||  period9 LIKE "absent%") AND reason = "" ORDER BY date DESC';
    $result = $conn->query($sql);
    if($result->num_rows != 0 && $_SESSION['accountType'] == 'parent'){
        echo "
        <div class='row justify-content-center mt-3 ml-0 mr-0'>
            <h3>Reason for Absenteeism</h3>
        </div>
        <form action='index.php' method='post'>
            <div class='row justify-content-center mt-2 mr-0 ml-0'>
                <span class='m-3'>
                    Date:
                    <select name='dateOfAbs'>";
                            while($row=$result->fetch_assoc()){
                                echo '<option>' . $row['date'] . '</option>';
                            }
                echo "
                    </select>
                </span>
                <span class='m-3'>
                    Reason:
                    <select name='reasonForAbs'>
                        <option>illness</option>
                        <option>appointment</option>
                        <option>other</other>
                    </select>
                </span>
                <span class='m-3'>
                    Description: <textarea name='descriptionForAbs'></textarea>
                </span>
                <span>
                    <button class='btn btn-primary m-3'>Submit</button>
                </span>
            </div>
        </form>";
    }

    $sql = 'SELECT subjectCode FROM students WHERE student_id="' . $_SESSION['userId'] . '"';
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $subjects = explode(',',$row['subjectCode']);
    $lateCount = [];
    $absentCount = [];
    $activityCount = 0;
    $partialCount = 0;
    $fullCount = 0;
    foreach($subjects as $subject){
        $lateCount[$subject] = 0;
        $absentCount[$subject] = 0;
    }
    $sql = 'SELECT * FROM attendance WHERE (period1 LIKE "late-%" OR period2 LIKE "late-%" OR period3 LIKE "late-%" OR period4 LIKE "late-%" OR period5 LIKE "late-%" OR period6 LIKE "late-%" OR period7 LIKE "late-%" OR period8 LIKE "late-%" OR period9 LIKE "late-%" OR period1 LIKE "absent-%" OR period2 LIKE "absent-%" OR period3 LIKE "absent-%" OR period4 LIKE "absent-%" OR period5 LIKE "absent-%" OR period6 LIKE "absent-%" OR period7 LIKE "absent-%" OR period8 LIKE "absent-%" OR period9 LIKE "absent-%" OR period1 LIKE "Aabsent-%" OR period2 LIKE "Aabsent-%" OR period3 LIKE "Aabsent-%" OR period4 LIKE "Aabsent-%" OR period5 LIKE "Aabsent-%" OR period6 LIKE "Aabsent-%" OR period7 LIKE "Aabsent-%" OR period8 LIKE "Aabsent-%" OR period9 LIKE "Aabsent-%") AND date BETWEEN "' . $schoolStartDate . '" AND "' . $schoolEndDate . '" AND student_id = "' . $_SESSION['userId'] . '"';
    $result = $conn->query($sql);
    while($row=$result->fetch_assoc()){
        $present = false;
        $abs = false;
        for($i=1;$i <= 9;$i++){
            if($row['period' . $i] != ''){
                $period = explode('-',$row['period' . $i]);
                $status = $period[0];
                $sub = $period[1];
                if($status == 'late'){
                    if(!isset( $lateCount[$sub])){
                        $lateCount[$sub] = 0;
                    }
                    if(!isset( $absentCount[$sub])){
                        $absentCount[$sub] = 0;
                    }
                    $lateCount[$sub] += 1;
                }
                else if($status == 'Aabsent'){
                    $activityCount += 1;
                }
                else if($status == 'absent'){
                    if(!isset( $absentCount[$sub])){
                        $absentCount[$sub] = 0;
                    }
                    if(!isset( $lateCount[$sub])){
                        $lateCount[$sub] = 0;
                    }
                    $absentCount[$sub] += 1;
                    $abs = true;
                }
                else{
                    $present = true;
                }
            }
        }
        if($abs == true && $present == true){
            $partialCount += 1;
        }
        else if($abs == true && $present == false){
            $fullCount += 1;
        }
    }
?>
<div class='row justify-content-center mt-5 ml-0 mr-0'>
    <h3>Attendance Information</h3>
</div>
<div class='row justify-content-center ml-0 mr-0 mt-3' style='font-size:120%'>
    <span class='col-2 text-center'>
        <h4><?php echo $fullCount;?></h4>
        full days missed
    </span>
    <span class='col-2 text-center'>
        <h4><?php echo $partialCount;?></h4>
        partial days missed
    </span>
    <span class='col-2 text-center'>
        <h4><?php echo array_sum($lateCount);?></h4>
        late for class
    </span>
    <span class='col-2 text-center'>
        <h4><?php echo $activityCount;?></h4>
        class(es) missed for activities
    </span>
</div>
<div class='row justify-content-center mt-4 ml-0 mr-0'>
    <?php
        if(sizeof($absentCount) > 10){
            $breakpoint = ceil(sizeof($absentCount) / 2);
            echo $breakpoint;
        }
        $subjectCodes = [];
        foreach(array_keys($absentCount) as $subject){
            $subject = '"' . $subject . '"';
            array_push($subjectCodes,$subject);
        }

        $sql = 'SELECT subjectCode,subject FROM timetable WHERE subjectCode IN (' . implode(',',$subjectCodes) . ')';
        $result = $conn->query($sql);
        $subjectMap = [];
        while($row=$result->fetch_assoc()){
            $subjectMap[$row['subjectCode']] = $row['subject'];
        }
        for($i = 0; $i < sizeof($absentCount);$i++){
            if(isset($breakpoint) && $i == $breakpoint){
                echo '</div><div class="row justify-content-center mt-2 ml-0 mr-0"';
            }
            $key = array_keys($absentCount)[$i];
            $absValue = $absentCount[$key];
            $lateValue = $lateCount[$key];
            echo '<span class="col-3 col-lg-1 m-3" style="font-size:110%;">Absent: <b>' . $absValue . '</b><br>';
            echo 'Late: <b>' . $lateValue . '</b><br>';
            echo '<b>' . $subjectMap[$key] . '</b>';
            echo '</span>';
        }
    ?>
</div>
<div class='row justify-content-center ml-0 mr-0 mt-5'>
        <h3>Absenteeism History</h3>
</div>
<form action='index.php' method='post'>
    <div class='row justify-content-center ml-0 mr-0 mt-2'>
        Year: <select name='absHistoryYear' onchange='form.submit()'>
            <?php
                $sql = 'SELECT year FROM students WHERE student_id = "' . $_SESSION['userId'] . '"';
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $year = $row['year'];
                foreach(range($year,1) as $y){
                    if($y == 1){
                        $yearString = $y . 'st year';
                    }
                    else if($y == 2){
                        $yearString = $y . 'nd year';
                    }
                    else if($y == 3){
                        $yearString = $y . 'rd year';
                    }
                    else{
                        $yearString = $y . 'th year';
                    }
                    if($y == $year){
                        $yearString = 'Current - ' . $yearString;
                    }
                    if(isset($_POST['absHistoryYear']) && $_POST['absHistoryYear'] == $y){
                        echo '<option value=' . $y . ' selected>' . $yearString . '</option>';
                    }
                    echo '<option value=' . $y . '>' . $yearString . '</option>';
                }
            ?>
        </select>
    </div>
</form>
<div class='row justify-content-center ml-0 mr-0 mt-3'>
    <table class='text-center col-4'>
        <tr>
            <th>Date</th>
            <th>Reason</th>
            <th>Description</th>
        </tr>
        <?php
            if(isset($_POST['absHistoryYear'])){
                $searchYear = $_POST['absHistoryYear'];
            }
            else{
                $searchYear = $year;
            }
            $sql = 'SELECT date,reason,description FROM attendance WHERE student_id ="' . $_SESSION['userId'] . '" AND (period1 LIKE "absent-___' . $searchYear . '%" OR period2 LIKE "absent-___' . $searchYear . '%" OR  period3 LIKE "absent-___' . $searchYear . '%" OR period4 LIKE "absent-___' . $searchYear . '%" OR period5 LIKE "absent-___' . $searchYear . '%" OR period6 LIKE "absent-___' . $searchYear . '%" OR period7 LIKE "absent-___' . $searchYear . '%" OR period8 LIKE "absent-___' . $searchYear . '%" OR period9 LIKE "absent-___' . $searchYear . '%") ORDER BY DATE DESC';
            $result = $conn->query($sql);
            if($result->num_rows == 0){
                echo '<tr><td colspan=3>No absences</td></tr>';
            }
            else{
                while($row=$result->fetch_assoc()){
                    echo '<tr>';
                    echo '<td>' . $row['date'] . '</td>';
                    echo '<td>' . $row['reason'] . '</td>';
                    echo '<td>'. $row['description'] . '</td>';
                    echo '</tr>';
                }
            }
        ?>
    </table>
</div>
</body>
</htmL>
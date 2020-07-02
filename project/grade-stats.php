<?php
   if(!isset($_SESSION['userId'])){
    header('location:login.php');
    }
require('info.php');

echo '<div class="row justify-content-center ml-0 mr-0">';
$key = 0;

if(isset($_GET['student'])){
    $sql = 'SELECT year FROM students WHERE student_id = ' . $_GET['student'];
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $year = $row['year'];
    if(isset($_GET['overviewYear'])){
        $selectedYear = $_GET['overviewYear'];
    }
    else{
        $selectedYear = $year;
    }
    if($selectedYear == 0){
        $selectedYear = '_';
    }
    $dateCount = [];
    $sql = 'SELECT date,COUNT(*) as count FROM grades WHERE  student_id=' . $_GET['student'] . ' AND subjectCode LIKE "___' . $selectedYear . '%" GROUP BY date ORDER BY date asc';
    $result = $conn->query($sql);
    while($row=$result->fetch_assoc()){
        $dateCount[$row['date']] = intval($row['count']);
    }
    $subjectDict = [];
    $sql = 'SELECT subjectCode FROM grades WHERE  student_id=' . $_GET['student'] . ' AND subjectCode LIKE "___' . $selectedYear . '%" GROUP BY subjectCode';
    $result = $conn->query($sql);
    while($row=$result->fetch_assoc()){
        $subjectDict[$row['subjectCode']] = [];
    }
    echo '<h3 class="pl-5 ml-5">Overview</h3>';
    $student = trim($_GET['student'],'"');
    $allSelected = (isset($_GET['overviewYear']) && $_GET['overviewYear'] == 0)?'selected':'';
    echo '<span class="ml-5">year:<form action="grades.php" method="get"><input type="hidden" value=\'"' .$student . '"\' name="student">
    <select onchange="form.submit()" name="overviewYear">
        <option value=0' . $allSelected . '>All</option>';
        foreach(range($year,1) as $y){
            if($y == 1){
                $yearString = '1st year';
            }
            else if($y == 2){
                $yearString = '2nd year';
            }
            else if($y == 3){
                $yearString = '3rd year';
            }
            else{
                $yearString = $y . 'th year';
            }
            if((isset($_GET['overviewYear']) && $_GET['overviewYear'] == $y) ||(!isset($_GET['overviewYear']) && $year == $y)){
                echo '<option value=' . $y . ' selected>' . $yearString . '</option>';
            }
            else{
                echo '<option value=' . $y . '>' . $yearString . '</option>';
            }
        }
    echo '</select></form></span>';
    $sql = 'SELECT mark,date,subjectCode FROM grades WHERE student_id=' . $_GET['student'] . ' AND subjectCode LIKE "___' . $selectedYear . '%" ORDER BY date asc';
    $result = $conn->query($sql);
    $testCount = 0;
    $dateIndex = 0;
    $data = [];
    $dates = array_keys($dateCount);
    while($row = $result->fetch_assoc()){
        if($testCount == 0){
            $dateIndex++;
        }
        array_push($subjectDict[$row['subjectCode']],$row['mark']);
        $testCount++;
        if($testCount == $dateCount[$row['date']]){
            foreach(array_keys($subjectDict) as $s){
                if(sizeof($subjectDict[$s]) != $dateIndex){
                    array_push($subjectDict[$s],'NaN'); 
                }
            }
            $testCount = 0;
        }
    }
    $colourIndex = 0;
    $colours = ['"rgba(242, 38, 38, .4)"','"rgba(242, 102, 38, 0.4)"','"rgba(242, 208, 38, 0.4)"','"rgba(38, 242, 225, 0.2)"','"rgba(38, 133, 242, 0.2)"','"rgba(72, 38, 242, 0.2)"'];
    foreach($subjectDict as $sIndex=>$s){
        $values = implode(',',$s);
        array_push($data,'{
            label:"' . $sIndex . '",
            fill:false,
            data:[' . $values . '],
            borderColor:[' . $colours[fmod($colourIndex,sizeof($colours))] . '],
            backgroundColor:[' . $colours[$colourIndex] . ']
        }');
        $colourIndex++;
    }
    echo '<canvas class="myChart" height="80px"></canvas>
    <script>
    let ctx' . $key . ' = document.getElementsByClassName("myChart")[' . $key . '];
    let myLineChart' . $key . ' = new Chart(ctx' . $key . ', {
        type: "line",
        data:{
            labels:["' . implode('","',$dates) . '"],
            datasets: [' . implode(',',$data) . ']
        },
        options:{showLines:true,spanGaps:true, scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }}
    });
    </script>';    
    $key++;


    $sql = 'SELECT subjectCode FROM students WHERE student_id = ' . $_GET['student'] . '';
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $subjectCodes = explode(',', $row['subjectCode']);
}
else{
    $subjectCodes = $_SESSION['subjectCodes'];
}
if($_SESSION['accountType'] != 'staff'){
    $subjectMap = [];
    $sqlMap = 'SELECT subject, subjectCode FROM timetable GROUP BY subjectCode';
    $resultMap = $conn->query($sqlMap);
    while($row = $resultMap->fetch_assoc()){
        $subjectMap[$row['subjectCode']] = $row['subject'];
    }

}
if(isset($_GET['overviewYear']) && $_GET['overviewYear'] != $year){
    $subjectCodes = [];
}
forEach($subjectCodes as $subject){
    if(isset($subjectMap)){
        echo '<h2 class="subjectHeader">' . $subjectMap[$subject] . ' </h2>';
    }
    else{
        echo '<h2 class="subjectHeader">' . $subject . ' </h2>';
    }
    $dates = [];
    $sql = 'SELECT *, COUNT(*), MAX(mark) AS max, MIN(mark) AS min, AVG(mark) AS avg FROM grades WHERE subjectCode = "' . $subject . '" AND date >= "' . $schoolStartDate . '" GROUP BY date ORDER BY date ASC';
    $result = $conn->query($sql);
    $avg = [];
    $min = [];
    $max = [];
    $data = [];
    while($row = $result->fetch_assoc()){
        array_push($dates,$row['date']);
        array_push($avg,round($row['avg']));
        array_push($min,round($row['min']));
        array_push($max,round($row['max']));
    }
    $metricMap = [];
    $overviewDates = $dates;
    if(isset($_GET['compare']) && $_GET['compare'] == "true"){
        $sql = 'SELECT *, COUNT(*), MAX(mark) AS max, MIN(mark) AS min, AVG(mark) AS avg FROM grades WHERE subjectCode = "' . $subject . '" AND date BETWEEN "' . $previousStartDate . '" AND "' . $previousEndDate . '" GROUP BY date ORDER BY date ASC';
        $result = $conn->query($sql);
        $prev_avg = [];
        $prev_min = [];
        $prev_max = [];
        $index = 1;
        $inFirstPos = false;
        while($row = $result->fetch_assoc()){
            $prev_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($row['date'])) . " + 1 year"));
            if(!in_array($prev_date,$dates)){
                while(true){
                    if($index != sizeof($overviewDates)){
                        $dateTime = strtotime($overviewDates[$index]);
                        $dateTime1 = strtotime($overviewDates[$index-1]);
                    }
                    $prevTime = intval(strtotime($prev_date));
                    if(($prevTime > $dateTime1 && $prevTime < $dateTime) || ($prevTime < $dateTime1 && $index==1) || $index == sizeof($overviewDates)){
                        if($prevTime < $dateTime1){
                            $index = 0;
                            $inFirstPos = true;
                        }
                        array_splice($overviewDates,$index,0,$prev_date);
                        array_splice($avg,$index,0,'NaN');
                        array_splice($max,$index,0,'NaN');
                        array_splice($min,$index,0,'NaN');
                        $index++;
                        break;
                    }
                    else{
                        if($index==1 && $inFirstPos == false){
                            array_push($prev_avg,'NaN');
                            array_push($prev_min,'NaN');
                            array_push($prev_max,'NaN');    
                        }
                        array_push($prev_avg,'NaN');
                        array_push($prev_min,'NaN');
                        array_push($prev_max,'NaN');
                    }
                    $index++;
                }
            }
            array_push($prev_avg,round($row['avg']));
            array_push($prev_min,round($row['min']));
            array_push($prev_max,round($row['max']));
        }
        $metricMap = $metricMap + ['previous MIN'=>implode(',',$prev_min),'previous MAX'=>implode(',',$prev_max),'previous AVERAGE'=>implode(',',$prev_avg)];
    }
    if($_SESSION['accountType'] != 'staff'){
        $metricMap = ['AVERAGE'=>implode(',',$avg)] + $metricMap;
    
    }
    else{
        $metricMap = ['MIN'=>implode(',',$min),'MAX'=>implode(',',$max),'AVERAGE'=>implode(',',$avg)] + $metricMap;
    }
    
    if(isset($_GET['student'])){
        $sql = 'SELECT mark, date FROM grades WHERE student_id = ' . $_GET['student'] . ' AND subjectCode="' . $subject. '"';
        $result = $conn->query($sql);
        $studentMarks = [];
        $dateIndex = 0;

        $row=$result->fetch_assoc();
        foreach($overviewDates as $d){
            while($d != $row['date']){
                array_push($studentMarks,'NaN');
            }
            array_push($studentMarks,$row['mark']);
            $row = $result->fetch_assoc();
        }
        $metricMap = $metricMap + ['STUDENT'=> implode(',',$studentMarks)];
    }
    $colours = ['"rgba(242, 38, 38, .4)"','"rgba(242, 102, 38, 0.4)"','"rgba(242, 208, 38, 0.4)"','"rgba(38, 242, 225, 0.2)"','"rgba(38, 133, 242, 0.2)"','"rgba(72, 38, 242, 0.2)"'];
    foreach(array_keys($metricMap) as $colourIndex=>$metric){
        $values = $metricMap[$metric];
        array_push($data,'{
            label:"' . $metric . '",
            fill:false,
            data:[' . $values . '],
            borderColor:[' . $colours[$colourIndex] . '],
            backgroundColor:[' . $colours[$colourIndex] . ']
        }');
    }

    echo '<canvas class="myChart" height="80px"></canvas>
    <script>
    let ctx' . $key . ' = document.getElementsByClassName("myChart")[' . $key . '];
    let myLineChart' . $key . ' = new Chart(ctx' . $key . ', {
        type: "line",
        data:{
            labels:["' . implode('","',$overviewDates) . '"],
            datasets: [' . implode(',',$data) . ']
        },
        options:{showLines:true,spanGaps:true, scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }}
    });
    </script>';    
    $key++;
    if(!isset($_GET['student'])){
        $sql = 'SELECT grades.*,students.name FROM grades LEFT JOIN students ON grades.student_id = students.student_id WHERE grades.subjectCode = "' . $subject . '" AND grades.date >= "' . $schoolStartDate . '" ORDER BY grades.student_id,grades.date ASC';
        $result = $conn->query($sql);
        $results = [];
        $data = [];
        $index = 0;
        $colourIndex = 0;
        $colours = ['"rgba(242, 38, 38, .4)"','"rgba(242, 102, 38, 0.4)"','"rgba(242, 208, 38, 0.4)"','"rgba(38, 242, 225, 0.2)"','"rgba(38, 133, 242, 0.2)"','"rgba(72, 38, 242, 0.2)"'];
        while($row = $result->fetch_assoc()){
            if(!isset($currentStudent)){
                $currentStudent = $row['student_id'];
                $currentStudentName = $row['name'];
            }
            if($row['student_id'] != $currentStudent){
                $results = array_pad($results,sizeof($dates),'NaN');
                array_push($data,'{
                    label:"' .$currentStudentName . '",
                    fill:false,
                    data:[' . implode(',',$results) . '],
                    backgroundColor:[' . $colours[fmod($colourIndex,sizeof($colours))] . '],
                    borderColor:[' . $colours[fmod($colourIndex,sizeof($colours))] . ']
                }');
                $currentStudent = $row['student_id'];
                $currentStudentName = $row['name'];
                $results = [];
                $index = 0;
                $colourIndex++;
            }
            while($row['date']!=$dates[$index]){
                $mark = 'NaN';
                array_push($results,$mark);
                $index +=1;
            }
            $mark = $row['mark'];
            $index+=1;
            array_push($results,$mark);
        }
        $results = array_pad($results,sizeof($dates),'NaN');
        if(isset($currentStudent)){
            array_push($data,'{
                label:"' .$currentStudentName . '",
                fill:false,
                data:[' . implode(',',$results) . '],
                borderColor:[' . $colours[fmod($colourIndex,sizeof($colours))] . '],
                backgroundColor:[' . $colours[fmod($colourIndex,sizeof($colours))] . ']
            }');
        }
        unset($currentStudent);
    
        echo '<canvas class="myChart" height="80px"></canvas>
        <script>
        let ctx' . $key . ' = document.getElementsByClassName("myChart")[' . $key . '];
        let myLineChart' . $key . ' = new Chart(ctx' . $key . ', {
            type: "line",
            data:{
                labels:["' . implode('","',$dates) . '"],
                datasets: [' . implode(',',$data) . ']
            },
            options:{showLines:true,spanGaps:true, scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }}
        });
        </script>';    
        $key++;
    }
}
if($_SESSION['accountType'] == 'staff' && !isset($_GET['student']) && (!isset($_GET['compare']) || (isset($_GET['compare']) && $_GET['compare'] != 'true'))){
    echo '<script>gradeInfo()</script>';
}
echo '</body></html>';
exit();
?>
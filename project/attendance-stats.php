<?php
    echo '<script src="../node_modules/chart.js/dist/Chart.js"></script>';
    if(isset($_GET['student'])){
        $sql = 'SELECT subjectCode,year FROM students WHERE student_id = ' . $_GET['student'];
        $result = $conn->query($sql);
        $row =$result->fetch_assoc();
        $subjectList = array_intersect(explode(',',$row['subjectCode']),$_SESSION['subjectCodes']);
        $subjectListString = implode('","', $subjectList);
        $overviewShown = false;
        $currentYear = $row['year'];
        $yearMap= [1=>'1st',2=>'2nd',3=>'3rd',4=>'4th',5=>'5th',6=>'6th'];
        if(isset($_POST['studentAttYear'])){
            $currentSelected = '';
        }
        else{
            $currentSelected = 'selected';
        }
        echo '<div class="col-12"><div class="row justify-content-start"><span class="col-12 col-lg-4 pl-5 align-self-start ">students school year: <form method="post"><select name="studentAttYear" onchange="form.submit()"><option value=' . $currentYear . ' ' . $currentSelected . '>Current year(' . $yearMap[$currentYear] . ')</option>';
        foreach(range($currentYear-1,1) as $year){
            if($_POST['studentAttYear']==$year){
                echo '<option value='  . $year . ' selected>' . $yearMap[$year] . ' year</option>';    
            }
            else{
                echo '<option value='  . $year . '>' . $yearMap[$year] . ' year</option>';
            }
        }
        if(isset($_POST['studentAttYear']) && $_POST{'studentAttYear'}==0){
            echo '<option value=0 selected  >All</option></select></form></span>';
        }
        else{
            echo '<option value=0>All</option></select></form></span>';
        }
        echo '<h2 class="col-4 offset-3 offset-lg-0 text-center">Attendance Overview</h2></div></div>';
    }
    else{
        $subjectList = $_SESSION['subjectCodes'];
        $subjectListString = implode('","', $_SESSION['subjectCodes']);
    }
    $sql = 'SELECT subjectCode,subject FROM timetable WHERE subjectCode IN ("' . $subjectListString . '") GROUP BY subjectCode;'; 
    $result = $conn->query($sql);
    $subjectMapping = array();
    mysqli_error($conn);
    while($row = $result->fetch_assoc()) {
        $subjectMapping[$row['subjectCode']] = $row['subject'];
    }
    $key = 0;
    foreach($subjectList as $subject){
        $graphColours = ['rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'];
        if(isset($_GET['student'])){
            $startDate;
            if($overviewShown == false){
                $overviewShown = true;
                if(!isset($_POST['studentAttYear'])){
                    $studentAttYear = $currentYear;
                }
                else{
                    $studentAttYear = $_POST['studentAttYear'];
                }
                $visualDataStudent = getVisualData($conn,$studentAttYear,$_GET['student'],false,true);
                echo '<canvas class="myChart col-12" height="350px"></canvas>
                <script>
                var late' . $key . ' = ' .json_encode($visualDataStudent[8]) .'
                var activity' . $key . ' = ' . json_encode($visualDataStudent[2]) . '
                var illness' . $key . ' = ' . json_encode($visualDataStudent[3]) . '
                var appointment' . $key . ' = ' . json_encode($visualDataStudent[4]) . '
                var other' . $key . ' = ' . json_encode($visualDataStudent[5]) . '
                var suspension' . $key . ' = ' . json_encode($visualDataStudent[6]) . '
                var unstated' . $key . ' = ' . json_encode($visualDataStudent[7]) . '
                let ctx' . $key . ' = document.getElementsByClassName("myChart")[' . $key . '];
                let myChart' . $key . '= new Chart(ctx' . $key . ', {
                    type: "line",
                    data: {
                        labels: ' . $visualDataStudent[0] . ',
                        datasets: [{
                            label: "Attendance",
                            data: ' . $visualDataStudent[1] . ',
                            backgroundColor: [
                                "' . $graphColours[($key%sizeof($visualDataStudent))] . '",
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        tooltips: {
                            enabled: true,
                            
                            callbacks: {
                                label: function(tooltipItems, data) { 
                                if([tooltipItems.yLabel] == 0){
                                    status="Absent";
                                }
                                else if([tooltipItems.yLabel] == 1){
                                    status="Late";
                                }
                                else{
                                    status="Present";
                                }
                                if(illness' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: illness";
                                }
                                else if(appointment' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: appointment";
                                }
                                else if(activity' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: activity";
                                }
                                else if(other' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: other";
                                }
                                else if(suspension' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: suspended";
                                }
                                else if(unstated' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: unstated";
                                }
                                else{
                                    reason="";
                                }
                                if(reason != ""){
                                    return [status,reason]
                                
                                }
                                else{
                                    return [status]
                                }
                                }
                            }
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    display: false //this will remove only the label
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    stepSize:1,
                                    callback: function(value, index, values) {
                                        if(value==0){
                                            return "absent";
                                        }
                                        else if(value==1){
                                            return "late";
                                        }
                                        else{
                                            return "present";
                                        }
                                    }
                                }
                            }]
                        }
                    }
                });
                </script>';
                $key += 1;
            }
            if(isset($_POST['studentAttYear']) && $currentYear != $_POST['studentAttYear']){
                echo '</body></html>';
                exit();            
            }
            echo '<h2>' . $subjectMapping[$subject] . '</h2>';
            $visualDataStudent = getVisualData($conn,$subject,$_GET['student']);
            echo '<canvas class="myChart col-12" height="200px"></canvas>
            <script>
            var late' . $key . ' = ' .json_encode($visualDataStudent[8]) .'
            var activity' . $key . ' = ' . json_encode($visualDataStudent[2]) . '
            var illness' . $key . ' = ' . json_encode($visualDataStudent[3]) . '
            var appointment' . $key . ' = ' . json_encode($visualDataStudent[4]) . '
            var other' . $key . ' = ' . json_encode($visualDataStudent[5]) . '
            var suspension' . $key . ' = ' . json_encode($visualDataStudent[6]) . '
            var unstated' . $key . ' = ' . json_encode($visualDataStudent[7]) . '
            let ctx' . $key . ' = document.getElementsByClassName("myChart")[' . $key . '];
            let myChart' . $key . '= new Chart(ctx' . $key . ', {
                type: "line",
                data: {
                    labels: ' . $visualDataStudent[0] . ',
                    datasets: [{
                        label: "Attendance",
                        data: ' . $visualDataStudent[1] . ',
                        backgroundColor: [
                            "' . $graphColours[($key%sizeof($visualDataStudent))] . '",
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    tooltips: {
                        enabled: true,
                        
                        callbacks: {
                            label: function(tooltipItems, data) { 
                                if([tooltipItems.yLabel] == 0){
                                    status="Absent";
                                }
                                else if([tooltipItems.yLabel] == 1){
                                    status="Late";
                                }
                                else{
                                    status="Present";
                                }
                                if(illness' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: illness";
                                }
                                else if(appointment' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: appointment";
                                }
                                else if(activity' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: activity";
                                }
                                else if(other' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: other";
                                }
                                else if(suspension' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: suspended";
                                }
                                else if(unstated' . $key . '[tooltipItems.xLabel] > 0){
                                    reason="reason: unstated";
                                }
                                else{
                                    reason="";
                                }
                                if(reason != ""){
                                    return [status,reason]
                                
                                }
                                else{
                                    return [status]
                                }
                                }
                        }
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                display: false //this will remove only the label
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize:1,
                                callback: function(value, index, values) {
                                    if(value==0){
                                        return "absent";
                                    }
                                    else if(value==1){
                                        return "late";
                                    }
                                    else{
                                        return "present";
                                    }
                                }
                            }
                        }]
                    }
                }
            });
            </script>';   
            $key += 1;
        }
        else{
            echo '<h2>' . $subject . '</h2>';
            $startDate = false;
        }

        
        $visualData = getVisualData($conn,$subject,false,$startDate);
        echo '<canvas class="myChart col-12" height="350px"></canvas>
        <script>
        var late' . $key . ' = ' .json_encode($visualData[8]) .'
        var activity' . $key . ' = ' . json_encode($visualData[2]) . '
        var illness' . $key . ' = ' . json_encode($visualData[3]) . '
        var appointment' . $key . ' = ' . json_encode($visualData[4]) . '
        var other' . $key . ' = ' . json_encode($visualData[5]) . '
        var suspension' . $key . ' = ' . json_encode($visualData[6]) . '
        var unstated' . $key . ' = ' . json_encode($visualData[7]) . '
        let ctx' . $key . ' = document.getElementsByClassName("myChart")[' . $key . '];
        let myChart' . $key . '= new Chart(ctx' . $key . ', {
            type: "line",
            data: {
                labels: ' . $visualData[0] . ',
                datasets: [{
                    label: "# of Present Students",
                    data: ' . $visualData[1] . ',
                    backgroundColor: [
                        "' . $graphColours[($key%sizeof($graphColours))] . '",
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                tooltips: {
                    enabled: true,
                    
                    callbacks: {
                        label: function(tooltipItems, data) { 
                           return ["# of Present Students: " + [tooltipItems.yLabel],"# of present students late: " + late' . $key . '[tooltipItems.xLabel] ,"# illness: " + illness' . $key . '[tooltipItems.xLabel] ,"#appointment: " + appointment' . $key . '[tooltipItems.xLabel],"#activity: " + activity' . $key . '[tooltipItems.xLabel],"#other: " + other' . $key . '[tooltipItems.xLabel],"# of suspended: " + suspension' . $key . '[tooltipItems.xLabel],"# of unstated: " + unstated' . $key . '[tooltipItems.xLabel]]
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            display: false //this will remove only the label
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize:1
                        }
                    }]
                }
            }
        });
        </script>';
        $key += 1;
        if(!isset($_GET['student'])){
            $classTotal = array_sum(str_split(str_replace('"','',$visualData[1]))) + array_sum($visualData[2]) + array_sum($visualData[3]) + array_sum($visualData[4]) + array_sum($visualData[5]) + array_sum($visualData[6]); 
            $presentTotal = array_sum(str_split(str_replace('"','',$visualData[1])));
            $lateTotal = array_sum($visualData[8]);
            $absenseTotal = array_sum($visualData[2]) + array_sum($visualData[3]) + array_sum($visualData[4]) + array_sum($visualData[5]) + array_sum($visualData[6]);
            $illnessTotal = array_sum($visualData[3]);
            $activityTotal = array_sum($visualData[2]);
            $appointmentTotal = array_sum($visualData[4]);
            $otherTotal = array_sum($visualData[5]);
            if($classTotal == 0){
                $classTotal = 1;
            }
            if($absenseTotal == 0){
                $absenseTotal = 1;
            }
            if($presentTotal == 0){
                $presentTotal = 1;
            }
            echo '<span class="col-8 offset-4 text-center border-bottom d-none d-lg-block">Reasons for absenteeism</span>';
            echo '<span class="col-6 col-lg-2 text-center"><span class="attendance-stat-figures">' . round(($absenseTotal / $classTotal) * 100) . '%</span><br>Absenteeism</span>';
            echo '<span class="col-6 col-lg-2 text-center"><span class="attendance-stat-figures">' . round(($lateTotal/$presentTotal) * 100) . '%</span><br>Late</span>';
            echo '<span class="col-12 text-center border-bottom d-block d-lg-none">Reasons for absenteeism</span>';
            echo '<span class="col-3 col-lg-2 text-center"><span class="attendance-stat-figures">' . round(($illnessTotal / $absenseTotal) * 100) . '%</span><br>Illness</span>';
            echo '<span class="col-3 col-lg-2 text-center"><span class="attendance-stat-figures">' . round(($appointmentTotal / $absenseTotal) * 100) . '%</span><br>Appointment</span>';
            echo '<span class="col-3 col-lg-2 text-center"><span class="attendance-stat-figures">' . round(($activityTotal/ $absenseTotal) * 100) . '%</span><br>Activity</span>';
            echo '<span class="col-3 col-lg-2 text-center"><span class="attendance-stat-figures">' . round(($otherTotal / $absenseTotal) * 100) . '%</span><br>Other</span>';
        }
    }
    echo '</body></html>';
    exit();

?>

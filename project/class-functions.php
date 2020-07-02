<?php 
    function getClass($conn,$classCodes){
        date_default_timezone_set("Europe/Dublin");
        $classList = implode('","', $classCodes);
        $sql = 'SELECT * FROM timetable WHERE day = "' . strtolower(date("l")) . '" AND subjectCode IN ("' . $classList .'") ORDER BY start_time ASC;';
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $subjectNow = [$row['subjectCode'],substr(str_replace(':','',$row['start_time']),0,4)];
        $period = $row['period'];
        while($row = $result->fetch_assoc()) {
            if(substr(str_replace(':','',$row['start_time']),0,4) > $subjectNow[1] && substr(str_replace(':','',$row['start_time']),0,4) <= date('Hi')){
                $subjectNow = [$row['subjectCode'],substr(str_replace(':','',$row['start_time']),0,4)];
                $period = $row['period'];
            }
        }
        if($subjectNow[0] == ""){
            $subjectNow[0] =  $classCodes[0];
        }
        return [$subjectNow[0],'period' . $period];
    }

    function todayDropdown($conn,$subjectCode,$period){
        $sql = 'SELECT * FROM timetable WHERE day = "' . strtolower(date("l")) . '" AND subjectCode = "'. $subjectCode . '" ORDER BY start_time ASC;';
        $result = $conn->query($sql);
        if(mysqli_num_rows($result)!=0){
            while($row = $result->fetch_assoc()) {
                if('period' . $row['period'] == $period){
                    echo '<option value="period' . $row['period'] . '" selected="selected">Today ' . substr($row['start_time'],0,5) . ' ' . substr($row['end_time'],0,5) . '</option>';
                }
                else{
                    echo '<option value="period' . $row['period'] . '">Today ' . substr($row['start_time'],0,5) . ' ' . substr($row['end_time'],0,5) . '</option>';
                }
            }
            return True;
        }
        else{
            return False;
        }
        
    }

    function dateDropdown($conn,$subjectCode){
        $sql = 'SELECT * FROM attendance where period1 LIKE "%' . $subjectCode . '" OR period2 LIKE "%' . $subjectCode . '" OR period3 LIKE "%' . $subjectCode . '" OR period4 LIKE "%' . $subjectCode . '" OR period5 LIKE "%' . $subjectCode . '" OR period6 LIKE "%' . $subjectCode . '" OR period7 LIKE "%' . $subjectCode . '" OR period8 LIKE "%' . $subjectCode . '" OR period9 LIKE "%' . $subjectCode . '" GROUP BY date ORDER BY date DESC';
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()){
            for($i=1;$i < 10;$i++){
                $status = explode('-',$row['period' . $i]);
                if(sizeof($status) == 2){
                    if($status[1] == $_SESSION['subjectNow']){
                        if(!isset($_SESSION['historyPeriod']) && !isset($_SESSION['historyDate'])){
                            $_SESSION['historyPeriod'] = 'period' . $i;
                            $_SESSION['historyDate'] = $row['date'];
                        }
                        if($_SESSION['historyPeriod'] == 'period' . $i && $_SESSION['historyDate'] == $row['date']){
                            echo '<option value="period' . $i . '" selected="selected">' . $row['date'] . ' period ' . $i . '</option>';
                        }
                        else{
                            echo '<option value="period' . $i . '">' . $row['date'] . ' period ' . $i . '</option>';  
                        }
                    }
                }
            }
        }
    }

    function attendanceSet($conn,$classSize,$date='unset'){
        if($date == 'unset'){
            $date = date('Y-m-d');
        }
        $sql = 'SELECT students.name, attendance.student_id,' . $_SESSION['period'] . ' FROM attendance INNER JOIN students ON date = "' . $date .'" AND ' . $_SESSION['period'] . ' LIKE "%' . $_SESSION['subjectNow'] . '" AND attendance.student_id = students.student_id';
        $result = $conn->query($sql);
        echo mysqli_error($conn);
        if($classSize == mysqli_num_rows($result)){
            return $result;
        }
        else{
            return False;
        }

    }

    function getVisualData($conn,$subject,$studentId=false,$startDate=false,$overview=false){
        if($startDate != false){
            $date = ' AND date >= "' . $startDate . '"';
        }
        else{
            $date = '';
        }
        if($studentId != false){
            $student = ' AND student_id = ' . $studentId;
            if($overview != false){
                if($subject==0){
                    $subject = '_';
                }
                $sql = 'SELECT period1,period2,period3,period4,period5,period6,period7,period8,period9,date, if(period1 LIKE "present-___' . $subject . '%", 2, if(period1 LIKE "late-___' . $subject . '%", 1, 0)) as periodCheck1, if(period2 LIKE "present-___' . $subject . '%" , 2, if(period2 LIKE "late-___' . $subject . '%", 1, 0)) as periodCheck2, if(period3 LIKE "present-___' . $subject . '%", 2, if(period3 LIKE "late-___' . $subject . '%", 1, 0)) as periodCheck3, if(period4 LIKE "present-___' . $subject . '%", 2, if(period4 LIKE "late-___' . $subject . '%", 1, 0)) as periodCheck4, if(period5 LIKE "present-___' . $subject . '%", 2, if(period5 LIKE "late-___' . $subject . '%", 1, 0)) as periodCheck5, if(period6 LIKE "present-___' . $subject . '%", 2, if(period6 LIKE "late-___' . $subject . '%", 1, 0)) as periodCheck6, if(period7 LIKE "present-___' . $subject . '%", 2, if(period7 LIKE "late-___' . $subject . '%", 1, 0)) as periodCheck7, if(period8 LIKE "present-___' . $subject . '%", 2, if(period8 LIKE "late-___' . $subject . '%", 1, 0)) as periodCheck8, if(period9 LIKE "present-___' . $subject . '%", 2, if(period9 LIKE "late-___' . $subject . '%", 1, 0)) as periodCheck9 from attendance  WHERE (period1 LIKE "%%" OR period2 LIKE "%%" OR period3 LIKE "%%" OR period4 LIKE "%%" OR period5 LIKE "%%" OR period6 LIKE "%%" OR period7 LIKE "%%" OR period8 LIKE "%%" OR period9 LIKE "%%")' . $student . ' GROUP BY date ORDER BY date ASC';
            }
            else{
                $sql = 'SELECT period1,period2,period3,period4,period5,period6,period7,period8,period9,date, if(period1 LIKE "present-%", 2, if(period1 LIKE "late-' . $subject . '", 1, 0)) as periodCheck1, if(period2 LIKE "present-' . $subject . '" , 2, if(period2 LIKE "late-' . $subject . '", 1, 0)) as periodCheck2, if(period3 LIKE "present-' . $subject . '", 2, if(period3 LIKE "late-' . $subject . '", 1, 0)) as periodCheck3, if(period4 LIKE "present-' . $subject . '", 2, if(period4 LIKE "late-' . $subject . '", 1, 0)) as periodCheck4, if(period5 LIKE "present-' . $subject . '", 2, if(period5 LIKE "late-' . $subject . '", 1, 0)) as periodCheck5, if(period6 LIKE "present-' . $subject . '", 2, if(period6 LIKE "late-' . $subject . '", 1, 0)) as periodCheck6, if(period7 LIKE "present-' . $subject . '", 2, if(period7 LIKE "late-' . $subject . '", 1, 0)) as periodCheck7, if(period8 LIKE "present-' . $subject . '", 2, if(period8 LIKE "late-' . $subject . '", 1, 0)) as periodCheck8, if(period9 LIKE "present-' . $subject . '", 2, if(period9 LIKE "late-' . $subject . '", 1, 0)) as periodCheck9 from attendance  WHERE (period1 LIKE "%' . $subject . '" OR period2 LIKE "%' . $subject . '" OR period3 LIKE "%' . $subject . '" OR period4 LIKE "%' . $subject . '" OR period5 LIKE "%' . $subject . '" OR period6 LIKE "%' . $subject . '" OR period7 LIKE "%' . $subject . '" OR period8 LIKE "%' . $subject . '" OR period9 LIKE "%' . $subject . '")' . $student . ' GROUP BY date ORDER BY date ASC';
            }
        }
        else{
            $student = '';
            $sql = 'SELECT period1,period2,period3,period4,period5,period6,period7,period8,period9,date, SUM(if(period1 = "present-' . $subject . '" OR period1 = "late-' . $subject . '", 1, 0)) as periodCheck1, SUM(if(period2 = "present-' . $subject . '" OR period2 = "late-' . $subject . '", 1, 0)) as periodCheck2, SUM(if(period3 = "present-' . $subject . '" OR period3 = "late-' . $subject . '", 1, 0)) as periodCheck3, SUM(if(period4 = "present-' . $subject . '" OR period4 = "late-' . $subject . '", 1, 0)) as periodCheck4, SUM(if(period5 = "present-' . $subject . '" OR period5 = "late-' . $subject . '", 1, 0)) as periodCheck5, SUM(if(period6 = "present-' . $subject . '" OR period6 = "late-' . $subject . '", 1, 0)) as periodCheck6, SUM(if(period7 = "present-' . $subject . '" OR period7 = "late-' . $subject . '", 1, 0)) as periodCheck7, SUM(if(period8 = "present-' . $subject . '" OR period8 = "late-' . $subject . '", 1, 0)) as periodCheck8, SUM(if(period9 = "present-' . $subject . '" OR period9 = "late-' . $subject . '", 1, 0)) as periodCheck9 from attendance WHERE (period1 LIKE "%' . $subject . '" OR period2 LIKE "%' . $subject . '" OR period3 LIKE "%' . $subject . '" OR period4 LIKE "%' . $subject . '" OR period5 LIKE "%' . $subject . '" OR period6 LIKE "%' . $subject . '" OR period7 LIKE "%' . $subject . '" OR period8 LIKE "%' . $subject . '" OR period9 LIKE "%' . $subject . '")' . $student . $date . ' GROUP BY date ORDER BY date ASC';
        }
        $result = $conn->query($sql);
        $xData = [];
        $yData = []; 
        $dateSet = false;
        while($row = $result->fetch_assoc()){
            if($dateSet == false){
                $dateSet = true;
                global $startDate;
                $startDate = $row['date'];
            }
            foreach(range(1,9) as $period){
                if($row['period' . $period] != ""){
                    $status = explode('-',$row['period' . $period]);
                    if($status[1] == $subject || $overview != false){
                        array_push($xData,$row['date'] . ' period ' . $period);
                        array_push($yData,$row['periodCheck' . $period]);
                    }
                }
            }
        }
        $xDataString = '["' . implode('","',$xData) . '"]';
        $yDataString = '["' . implode('","',$yData) . '"]';


        if($studentId != false){
            $student = ' WHERE student_id = ' . $studentId;
        }
        if($startDate != false && $studentId == ''){
            $date = ' WHERE date >= "' . $startDate . '"';
        }
        $sql = 'SELECT date, SUM(if(period1 = "late-' . $subject . '", 1, 0)) as periodCheck1, SUM(if(period2 = "late-' . $subject . '", 1, 0)) as periodCheck2, SUM(if(period3 = "late-' . $subject . '", 1, 0)) as periodCheck3, SUM(if(period4 = "late-' . $subject . '", 1, 0)) as periodCheck4, SUM(if(period5 = "late-' . $subject . '", 1, 0)) as periodCheck5, SUM(if(period6 = "late-' . $subject . '", 1, 0)) as periodCheck6, SUM(if(period7 = "late-' . $subject . '", 1, 0)) as periodCheck7, SUM(if(period8 = "late-' . $subject . '", 1, 0)) as periodCheck8, SUM(if(period9 = "late-' . $subject . '", 1, 0)) as periodCheck9  from attendance ' . $student . $date . ' GROUP BY date';
        $result = $conn->query($sql);
        $late = []; 
        while($row = $result->fetch_assoc()){
            foreach(range(1,9) as $period){
                $late[$row['date'] . ' period '. $period] = $row['periodCheck' . $period];
            }
        }


        $sql = 'SELECT date, SUM(if(period1 = "Aabsent-' . $subject . '", 1, 0)) as periodCheck1, SUM(if(period2 = "Aabsent-' . $subject . '", 1, 0)) as periodCheck2, SUM(if(period3 = "Aabsent-' . $subject . '", 1, 0)) as periodCheck3, SUM(if(period4 = "Aabsent-' . $subject . '", 1, 0)) as periodCheck4, SUM(if(period5 = "Aabsent-' . $subject . '", 1, 0)) as periodCheck5, SUM(if(period6 = "Aabsent-' . $subject . '", 1, 0)) as periodCheck6, SUM(if(period7 = "Aabsent-' . $subject . '", 1, 0)) as periodCheck7, SUM(if(period8 = "Aabsent-' . $subject . '", 1, 0)) as periodCheck8, SUM(if(period9 = "Aabsent-' . $subject . '", 1, 0)) as periodCheck9  from attendance ' .$student . $date . ' GROUP BY date';
        $result = $conn->query($sql);
        $activity = []; 
        while($row = $result->fetch_assoc()){
            foreach(range(1,9) as $period){
                $activity[$row['date'] . ' period '. $period] = $row['periodCheck' . $period];
            }
        }

        $sql = 'SELECT date ,SUM(IF(period1 = "absent-' . $subject . '" AND reason = "illness", 1, 0)) as periodCheck1, SUM(if(period2 = "absent-' . $subject . '" AND reason = "illness", 1, 0)) as periodCheck2, SUM(if(period3 = "absent-' . $subject . '" AND reason = "illness", 1, 0)) as periodCheck3, SUM(if(period4 = "absent-' . $subject . '" AND reason = "illness", 1, 0)) as periodCheck4, SUM(if(period5 = "absent-' . $subject . '" AND reason = "illness", 1, 0)) as periodCheck5, SUM(if(period6 = "absent-' . $subject . '" AND reason = "illness", 1, 0)) as periodCheck6, SUM(if(period7 = "absent-' . $subject . '" AND reason = "illness", 1, 0)) as periodCheck7, SUM(if(period8 = "absent-' . $subject . '" AND reason = "illness", 1, 0)) as periodCheck8, SUM(if(period9 = "absent-' . $subject . '" AND reason = "illness", 1, 0)) as periodCheck9, 
        SUM(IF(period1 = "absent-' . $subject . '" AND reason = "appointment", 1, 0)) as appointPeriodCheck1, SUM(if(period2 = "absent-' . $subject . '" AND reason = "appointment", 1, 0)) as appointPeriodCheck2, SUM(if(period3 = "absent-' . $subject . '" AND reason = "appointment", 1, 0)) as appointPeriodCheck3, SUM(if(period4 = "absent-' . $subject . '" AND reason = "appointment", 1, 0)) as appointPeriodCheck4, SUM(if(period5 = "absent-' . $subject . '" AND reason = "appointment", 1, 0)) as appointPeriodCheck5, SUM(if(period6 = "absent-' . $subject . '" AND reason = "appointment", 1, 0)) as appointPeriodCheck6, SUM(if(period7 = "absent-' . $subject . '" AND reason = "appointment", 1, 0)) as appointPeriodCheck7, SUM(if(period8 = "absent-' . $subject . '" AND reason = "appointment", 1, 0)) as appointPeriodCheck8, SUM(if(period9 = "absent-' . $subject . '" AND reason = "appointment", 1, 0)) as appointPeriodCheck9,
        SUM(IF(period1 = "absent-' . $subject . '" AND reason = "other", 1, 0)) as otherPeriodCheck1, SUM(if(period2 = "absent-' . $subject . '" AND reason = "other", 1, 0)) as otherPeriodCheck2, SUM(if(period3 = "absent-' . $subject . '" AND reason = "other", 1, 0)) as otherPeriodCheck3, SUM(if(period4 = "absent-' . $subject . '" AND reason = "other", 1, 0)) as otherPeriodCheck4, SUM(if(period5 = "absent-' . $subject . '" AND reason = "other", 1, 0)) as otherPeriodCheck5, SUM(if(period6 = "absent-' . $subject . '" AND reason = "other", 1, 0)) as otherPeriodCheck6, SUM(if(period7 = "absent-' . $subject . '" AND reason = "other", 1, 0)) as otherPeriodCheck7, SUM(if(period8 = "absent-' . $subject . '" AND reason = "other", 1, 0)) as otherPeriodCheck8, SUM(if(period9 = "absent-' . $subject . '" AND reason = "other", 1, 0)) otherPeriodCheck9,
        SUM(IF(period1 = "absent-' . $subject . '" AND reason = "suspension", 1, 0)) as suspPeriodCheck1, SUM(if(period2 = "absent-' . $subject . '" AND reason = "suspension", 1, 0)) as suspPeriodCheck2, SUM(if(period3 = "absent-' . $subject . '" AND reason = "suspension", 1, 0)) as suspPeriodCheck3, SUM(if(period4 = "absent-' . $subject . '" AND reason = "suspension", 1, 0)) as suspPeriodCheck4, SUM(if(period5 = "absent-' . $subject . '" AND reason = "suspension", 1, 0)) as suspPeriodCheck5, SUM(if(period6 = "absent-' . $subject . '" AND reason = "suspension", 1, 0)) as suspPeriodCheck6, SUM(if(period7 = "absent-' . $subject . '" AND reason = "suspension", 1, 0)) as suspPeriodCheck7, SUM(if(period8 = "absent-' . $subject . '" AND reason = "suspension", 1, 0)) as suspPeriodCheck8, SUM(if(period9 = "absent-' . $subject . '" AND reason = "suspension", 1, 0))suspPeriodCheck9,
        SUM(IF(period1 = "absent-' . $subject . '" AND reason = "", 1, 0)) as unstatedPeriodCheck1, SUM(if(period2 = "absent-' . $subject . '" AND reason = "", 1, 0)) as unstatedPeriodCheck2, SUM(if(period3 = "absent-' . $subject . '" AND reason = "", 1, 0)) as unstatedPeriodCheck3, SUM(if(period4 = "absent-' . $subject . '" AND reason = "", 1, 0)) as unstatedPeriodCheck4, SUM(if(period5 = "absent-' . $subject . '" AND reason = "", 1, 0)) as unstatedPeriodCheck5, SUM(if(period6 = "absent-' . $subject . '" AND reason = "", 1, 0)) as unstatedPeriodCheck6, SUM(if(period7 = "absent-' . $subject . '" AND reason = "", 1, 0)) as unstatedPeriodCheck7, SUM(if(period8 = "absent-' . $subject . '" AND reason = "", 1, 0)) as unstatedPeriodCheck8, SUM(if(period9 = "absent-' . $subject . '" AND reason = "", 1, 0)) unstatedPeriodCheck9 FROM attendance ' . $student . $date . ' GROUP BY date';
        $result = $conn->query($sql);
        $illness = [];
        $appointment = [];
        $suspension = [];
        $other = [];
        $unstated = [];
        while($row=$result->fetch_assoc()){
            foreach(range(1,9) as $period){
                $illness[$row['date'] . ' period '. $period] = $row['periodCheck' . $period];
                $appointment[$row['date'] . ' period '. $period] = $row['appointPeriodCheck' . $period];
                $other[$row['date'] . ' period '. $period] = $row['otherPeriodCheck' . $period];
                $suspension[$row['date'] . ' period '. $period] = $row['suspPeriodCheck' . $period];
                $unstated[$row['date'] . ' period '. $period] = $row['unstatedPeriodCheck' . $period];
            }
        }
        if($studentId != false){
            $student = ' AND student_id = ' . $studentId;
        }
        if($startDate != false){
            $date = ' AND date >= "' . $startDate . '"';
        }
        $sql = 'SELECT * FROM attendance WHERE (period1 = "absent-' . $subject . '" OR period2 = "absent-' . $subject . '" OR period3 = "absent-' . $subject . '" OR period4 = "absent-' . $subject . '" OR period5 = "absent-' . $subject . '" OR period6 = "absent-' . $subject . '" OR period7 = "absent-' . $subject . '" OR period8 = "absent-' . $subject . '" OR period9 = "absent-' . $subject . '") AND reason LIKE "%,%" ' . $student . $date; 
        $result = $conn->query($sql);
        while($row=$result->fetch_assoc()){
            $reasons = explode(',',$row['reason']);
            $reasonMap = [];
            foreach(explode(',',$row['missedPeriods']) as $key=>$periods){
                if(strlen($periods) > 1){
                    $range = range(subtr($periods,0,1),subtr($periods,2,1));
                    foreach($periods as $period){
                        $reasonMap[$period] = $reasons[$key];
                    }
                }
                else{
                    $reasonMap[$periods] = $reasons[$key];
                }
            }

            foreach(range(1,9) as $period){
                if($row['period' . $period] == 'absent-' . $subject){
                    if(!array_key_exists($period,$reasonMap)){
                        $unstated[$row['date'] . ' period '. $period] += 1;                  
                    }
                    else{
                        $reason = $reasonMap[$period];
                        if($reason == 'illness'){
                            $illness[$row['date'] . ' period '. $period] += 1;
                        }
                        
                        else if($reason == 'appointment'){
                            $appointment[$row['date'] . ' period '. $period] += 1;
                        }
                        
                        else if($reason == 'suspension'){
                            $suspension[$row['date'] . ' period '. $period] += 1;
                        }
                        
                        else if($reason == 'other'){
                            $other[$row['date'] . ' period '. $period] += 1;
                        }
                    }
                }
            }
        }
        
        return [$xDataString,$yDataString,$activity,$illness,$appointment,$other,$suspension,$unstated,$late];
    }

    function onActivity($conn,$date='unset'){
        if($date == 'unset'){
            $date = date('Y-m-d');
        }
        $sql = 'SELECT * FROM attendance WHERE date = "' . $date .'" AND ' . $_SESSION['period'] . ' = "Aabsent-' . $_SESSION['subjectNow'] . '"';
        $result=$conn->query($sql);
        $studentsOnActivity = [];
        while($row = $result->fetch_assoc()){
            array_push($studentsOnActivity,$row['student_id']);
        }
        return $studentsOnActivity;
    }

?>

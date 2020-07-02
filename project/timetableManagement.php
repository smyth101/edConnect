<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }

    if(isset($_POST['day'])){
        $day = strtolower($_POST['day']);
        $subjectCode = $_POST['subjectCode'] . $_POST['year'] . $_POST['identifier'];
        $sqlRoom = 'SELECT subjectCode FROM timetable WHERE room = "' . $_POST['room'] . '" AND day = "' . $day . '" AND period="' . $_POST['period'] . '"';
        $resultRoom = $conn->query($sqlRoom);
        $sqlMultiple = 'SELECT room FROM timetable WHERE subjectCode = "' . $subjectCode . '" AND day = "' . $day . '" AND period = ' . $_POST['period'] ;
        $resultMultiple = $conn->query($sqlMultiple);
        if($resultRoom->num_rows > 0){
            $row = $resultRoom->fetch_assoc();
            $statusMessage = '<span class="text-danger">This room has already been assigned to ' . $row['subjectCode'] . '</span>';
        }
        else if($resultMultiple->num_rows > 0){
            $row = $resultMultiple->fetch_assoc();
            $statusMessage = '<span class="text-danger">This subject has already been assigned for this period in room ' . $row['room'] . '</span>';
        }
        else{
            $stmt = $conn->prepare('INSERT INTO timetable (day,period,subject,subjectCode,start_time,end_time,room) VALUES(?,?,?,?,?,?,?)');
            $stmt->bind_param('sssssss',$day,$period,$subject,$subjectCode,$sDate,$eDate,$room);
            $subject = $_POST['subject'];
            $period = $_POST['period'];
            $sDate = $_POST['startDate'];
            $eDate = $_POST['endDate'];
            $room = $_POST['room'];
    
            $stmt->execute();
            if(!$stmt->error){
                $statusMessage = '<span class="text-success">Timetable successfully Updated</span>';
            }
            else{
                $statusMessage = '<span class="text-danger">Something went wrong. Timetable was not updated</span>';
            }
        }
    }

    if(isset($_POST['updateSubject'])){
        foreach($_POST['updateSubject'] as $key=>$subj){
            $stmt = $conn->prepare('UPDATE timetable SET subjectCode = ? WHERE subject = ?');
            $stmt->bind_param('ss',$_POST['updateCode'][$key], $subj);
            $stmt->execute();
            if($stmt->error){
                $statusMessage .= '<span class="col-12 text-danger">Subject codes did not update successfully</span>';
            }
        }
        if(!isset($statusMessage)){
            $statusMessage = '<span class="text-success">Subject Codes successfully updated</span>';
        }
    }

    if(isset($_POST['updateSubj'])){
        if($_POST['updateSubjectCode'] != $_POST['updateFromCode']){
            $sql = 'SELECT subjectCode FROM timetable WHERE subjectCode = "' . $_POST['updateSubjectCode'] . '" AND period = ' . $_POST['updatePeriod'] . ' AND day = "' . $_POST['updateDay'] . '"';
            $result = $conn->query($sql);
            if($result->num_rows > 0){
                $statusMessage = '<span class="text-danger">This subject is already scheduled for the selected time slot</span>';
            }
        }
        if($_POST['updateRoom'] != $_POST['updateFromRoom']){
            $sql = 'SELECT subjectCode FROM timetable WHERE room = "' . $_POST['updateRoom'] . '"  AND period = ' . $_POST['updatePeriod'] . ' AND day = "' . $_POST['updateDay'] . '"';
            $result = $conn->query($sql);
            if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                $statusMessage = '<span class="text-danger">This room has already been assigned to ' . $row['subjectCode'] . '</span>';
            }
        }
        if(!isset($statusMessage)){
            $stmt = $conn->prepare('UPDATE timetable SET day = ?,period=?,subject=?,subjectCode=?,start_time=?,end_time=?,room=? WHERE day = ? AND period = ? AND subjectCode = ?');
            $stmt->bind_param('ssssssssss',$_POST['updateDay'],$_POST['updatePeriod'],$_POST['updateSubj'],$_POST['updateSubjectCode'],$_POST['updateStart'],$_POST['updateEnd'],$_POST['updateRoom'],$_POST['updateFromDay'],$_POST['updateFromPeriod'],$_POST['updateFromCode']);
            $stmt->execute();
            echo $stmt->error;
        }

    }

    if(isset($_POST['deleteDay'])){
        $stmt = $conn->prepare('DELETE FROM timetable WHERE day = ? AND period = ? AND room = ?');
        $stmt->bind_param('sss',$_POST['deleteDay'],$_POST['deletePeriod'],$_POST['deleteRoom']);
        $stmt->execute();
        if($stmt->error){
            $statusMessage = '<span class="text-danger">Timetable was not removed</span>';
        }
        else{
            $statusMessage = '<span class="text-success">Timetable row was successfully deleted</span>';
        }
    }
?>
<!DOCTYPE html>
<head>
    <title>edConnect | timetable management</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script>var subjectList;var roomList;var subjectAdded = false;</script>
</head>
<body onload='getTimetable()'>
<?php
require('navbar.php');
?>
<div class='row ml-0 mr-0 col-12 third-nav ml-0 mr-0'>
    <ul class='nav'>
        <li class="third-nav-item"><a href='actionables.php'>Supervision</a></li>
        <li class="third-nav-item"><a href='detentions.php'>Detentions</a></li>
        <li class="third-nav-item"><a href='manageStaff.php'>Manage Staff</a></li>
        <li class="third-nav-item"><a href='timetableManagement.php'>Manage Timetable</a></li>
        <li class="third-nav-item"><a href='studentApproval.php'>Student Approval</a></li>
        <li class="third-nav-item"><a href='studentManagement.php'>Manage Students</a></li>
    </ul>
</div>
<div class='row justify-content-center mt-3 ml-0 mr-0 text-center '>
    <h3 class='col-12 mb-4'>Timetable Management</h3>
    <div class='col-12' id='status'><?php echo (isset($statusMessage))?$statusMessage:'';?></div>
</div>
<div class='container'>
    <form action='timetableManagement.php' method='post'>
        <div class='row ml-0 mr-0'>
            <span class='ml-4 ml-lg-0'>
                Day:
                <select name='day' onchange='timetableLiveUpdate(event.target)' required>
                    <option value='' disabled selected>--select day--</option>
                    <option>Monday</option>
                    <option>Tuesday</option>
                    <option>Wednesday</option>
                    <option>Thursday</option>
                    <option>Friday</option>
                    <option>Saturday</option>
                    <option>Sunday</option>
                </select>
            </span>
            <span class='ml-5'>
                Period:
                <select name='period' onchange='timetableLiveUpdate(event.target)'>
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                    <option>6</option>
                    <option>7</option>
                    <option>8</option>
                    <option>9</option>
                </select>
            </span>
            <span class='ml-5  mt-3 mt-lg-0'>
                Subject:
                <select  onchange='subjectSelect(event.target)' name='subject' required>
                    <option value = '' disabled selected>--select subject--</option>
                    <option value='English'>English</option>
                    <option value='Maths'>Maths</option>
                    <option value='Irish'>Irish</option>
                    <option value='C.S.P.E'>C.S.P.E</option>
                    <option value='Science'>Science</option>
                    <option value='French'>French</option>
                    <option value='Spanish'>Spanish</option>
                    <option value='German'>German</option>
                    <option value='Italian'>Italian</option>
                    <option value='History'>History</option>
                    <option value='Geography'>Geography</option>
                    <option value='Business'>Business</option>
                    <option value='Technology'>Technology</option>
                    <option value='Metal Work'>Metal Work</option>
                    <option value='Wood Work'>Wood Work</option>
                    <option value='Technical Graphics'>Technical Graphics</option>
                    <option value='Art'>Art</option>
                    <option value='Music'>Music</option>
                    <option value='Home Economics'>Home Economics</option>
                    <option value='Religion'>Religion</option>
                    <option value='Ancient Greek'>Ancient Greek</option>
                    <option value='Latin'>Latin</option>
                    <option value='Classical Studied'>Classical Studied</option>
                    <option value='LCVP'>LCVP</option>
                    <option value='Physical Education'>Physical Education</option>
                    <option value='Arabic'>Arabic</option>
                    <option value='Russian'>Russian</option>
                    <option value='Japanese'>Japanese</option>
                </select>
                <button class='btn btn-primary text-white font-weight-bold' style='padding:0 5px 0 5px' title='create subject' onclick='addTimetableItem(event.target,"Subject")' type='button'>
                    +
                </button>
            </span>
            <span class='ml-5 mt-3 mt-lg-0'>Class Year: 
                <select onchange='timetableLiveUpdate(event.target)' name='year' required>
                    <option selected disabled>--select year--</option>
                    <option value=1>1st year</option>
                    <option value=2>2nd year</option>
                    <option value=3>3rd year</option>
                    <option value=4>4th year</option>
                    <option value=5>5th year</option>
                    <option value=6>6th year</option>
                </select>
            </span>
            <span class='pr-5 pr-lg-0 ml-5 mr-5 mr-lg-0 mt-3 mt-lg-0' title="Class identifier is used for distinguishing differant classes
            of the same subject in the same year">
                class Identifier: 
                <select name='identifier' required>
                    <option selected disabled>-</option>
                    <?php
                    for($i = 1; $i <= 15; $i++){
                        echo '<option>' .$i . ' </option>';
                    }
                    ?>
                </select>
            </span>
            <span class='mt-3'> Start time: <input type='time' name='startDate' required></span>
            <span class='ml-5 mt-3'>End Time: <input type='time' name='endDate' required></span>
            <span class='ml-5 mt-3'>
                Room: 
                <select name='room' onchange='timetableLiveUpdate(event.target)' required>
                    <option value='' selected disabled> --select room--</option>
                    <?php
                        $sql = 'SELECT room FROM timetable GROUP BY room';
                        $result = $conn->query($sql);
                        while($row = $result->fetch_assoc()){
                            echo '<option>' . $row['room'] . '</option>';
                        }
                    ?>
                </select>
                <button class='btn btn-primary text-white font-weight-bold' style='padding:0 5px 0 5px' title='create room' onclick='addTimetableItem(event.target,"Room")' type='button'>
                    +
                </button>
            </span>
            <span class='ml-5 mt-3'>live update <input type='checkbox' id='liveUpdate' checked> on 
                <select id='updateOn'>
                        <option>Subject</option>
                        <option>Day</option>
                        <option>Period</option>
                        <option>Year</option>
                        <option>Room</opton>
                </select>
            </span>
            <input name='subjectCode' type='hidden'>
            <span class='ml-5 mt-3'><button class='btn btn-primary'>Add</button>
        </div>
    </form>
</div>
<div class='container mt-5' style='display:inline-block'>
    <form action='timetableManagement.php' method='post'>
        <div class='row justify-content-center text-center ml-0 mr-0'>
            <span class='col-12'>
                <h4 >Current timetable</h4>
            </span>
            <div class='col-12 text-right mr-5'>
                    <input type='text' placeholder='search subject or code' onkeyup='getTimetable(this.value)'> 
            </div>
            <div class='table-responsive col-12'>
                <table class='table' id='currentTimetable'>
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Period</th>
                            <th>Subject</th>
                            <th>Class year</th>
                            <th>subject code</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Room</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
<div class='container' style='display:inline-block;' id='subjectcode-container'>
    <form action='timetableManagement.php' method='post'>
        <div class='row justify-content-center text-center ml-0 mr-0'>
            <h4 class='col-12'>Subject Codes</h4>
            <span class='col-12'>
                <button class='btn btn-primary' type='button' onclick='editSubjectCodes(event.target)'>Edit</button>
            </span>
            <table class='col-12' id='subjectCodeTable'>
            <?php
                $sql = 'SELECT subjectCode,subject FROM timetable GROUP BY subject';
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()){
                    echo '<tr>';
                    $code = substr($row['subjectCode'],0,3);
                    echo '<td><span name="updateSubject[]">' . $row['subject'] . '</span></td><td><span name="updateCode[]">' . $code . '</span></td>';
                    echo '</tr>';
                }
            ?>
            </table>
        </div>
    </form>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="edeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Delete Timetable Row</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to remove this entry from the timetable.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick='document.getElementById("deleteForm").submit()'>Delete</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>
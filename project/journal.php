<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    require('info.php');
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }

    principalViewCheck($conn);
?>
<!DOCTYPE html>
<head>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <title>edConnect | Journal</title>
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
</head>
<body>
<?php

if(isset($_POST['note-description'])){
    if($_POST['note-type'] == 'Note'){
        $stmt = $conn->prepare("INSERT INTO notes (sender_type, sender_id, note_type, note, date, assigned_at,reciever_id) VALUES (?, ?, ?, ?, ?, ?,? )");
        $stmt->bind_param("sssssss", $userType, $userId, $noteType, $description, $date, $assigned_date,$recieverId);
        $userId = $_SESSION['accountId'];
        $userType = $_SESSION['accountType'];   
        $description = $_POST['note-description'];
        $noteType =  $_POST['note-category'];
        $date = $_POST['start-date'];
        $recieverId = $_SESSION['subjectNow'];
        $assigned_date = date("Y-m-d h:i:s");
        $stmt->execute();
    }
    else{
        $stmt = $conn->prepare("INSERT INTO journal (subjectCode, date, description,due_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $subjectCode, $assign_date, $description, $due_date);
        $subjectCode = $_SESSION['subjectNow'];
        $description = $_POST['note-description'];
        $due_date =  $_POST['note-d-date'];
        $assign_date = $_POST['note-type'];
        $stmt->execute();
    }
}

require('navbar.php');



if(isset($_POST['journal-date'])){
    $today = date('Y-m-d',strtotime($_POST['journal-date']));
}
else if(isset($_POST['subject-journal-date'])){
    $today = date('Y-m-d',strtotime($_POST['subject-journal-date']));
}
else if(isset($_POST['start-date'])){
    $today = date('Y-m-d',strtotime($_POST['start-date']));
}
else{
    $today = date("Y-m-d");
}
$dayIndex = date_format(date_create($today),'N');
$startOffset = $dayIndex - 1;
$endOffset = 7 - $dayIndex;


$Date = $today;
$endDate = date('Y-m-d', strtotime($Date. ' + ' . $endOffset . ' days'));
$fridayDate = date('Y-m-d', strtotime($Date. ' + ' . ($endOffset - 2) . ' days'));
$startDate = date('Y-m-d', strtotime($Date. ' - ' . $startOffset . ' days'));
$prevDate = date('Y-m-d', strtotime($startDate. ' - 7 days'));
$nextDate = date('Y-m-d', strtotime($startDate. ' + 7 days'));


$days = [];
$days['Monday'] = "";
$days['Tuesday'] = "";
$days['Wednesday'] = "";
$days['Thursday'] = "";
$days['Friday'] = "";

$subjectCodes = implode('","', $_SESSION['subjectCodes']);
if($_SESSION['accountType'] == 'staff'){
    if($_SESSION['staffPrivileges'] == 'higher'){
        echo '
        <div class="row mr-0 ml-0 mt-2">
            <form method="post" action="journal.php" class="col-1 offset-9">
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
    echo "
        <div class='container'>
        <form action='journal.php' method='post' id='classCode'>
        <input type='hidden' value='" . $startDate . "' name='subject-journal-date'>
        Subject: <select name='classCode' onchange='submitter(\"classCode\")' id='classCodeDrpdwn'>";
    if(isset($_POST['classCode'])){
        if($_POST['classCode'] != 'All'){
            unset($_SESSION['journalAll']);
            $_SESSION['subjectNow'] = $_POST['classCode'];
        }
    }
    if((isset($_POST['classCode']) && $_POST['classCode'] == 'All') || isset($_SESSION['journalAll'])){
        echo '<option selected="selected">All</option>';
        $_SESSION['journalAll'] = true;
    }
    else{
        echo '<option>All</option>';
    }
    foreach($_SESSION['subjectCodes'] as $subjectCode){
        if(isset($_POST['classCode']) && $_POST['classCode'] == 'All'){
            echo '<option>' . trim($subjectCode) . '</option>';
        }
        else{
            if($_SESSION['journalAll'] != true && trim($_SESSION['subjectNow']) == trim($subjectCode)){
                echo '<option selected="selected">' . trim($subjectCode) . '</option>';
            }
            else{
                echo '<option>' . trim($subjectCode) . '</option>';
            }
        }
    }
    echo '</select></form><br>';
    if($startDate >= date("Y-m-d",strtotime('-'. (date('N')-1) . ' days'))){
        echo '<form action="journal.php" method="post" id="note-form">
            Day/Note: <select name="note-type" onchange="noteTypeChange(this)">
                <option value="' . $startDate . '">Monday</option>
                <option value="' . date('Y-m-d', strtotime($startDate. ' + 1 days')) . '">Tuesday</option>
                <option value="' . date('Y-m-d', strtotime($startDate. ' + 2 days')) . '">Wednesday</option>
                <option value="' . date('Y-m-d', strtotime($startDate. ' + 3 days')) . '">Thursday</option>
                <option value="' . date('Y-m-d', strtotime($startDate. ' + 4 days')) . '">Friday</option>
                <option value="Note">Note</option>
            </select>
            <input type="hidden" value="' . $startDate . '" name="start-date">
                Description: <textarea name="note-description" required></textarea>
                <span id="note-category">Note type:
                <select name="note-category">
                    <option value="behaviour">Behavioural</option>
                    <option value="feedback">feedback</option>
                </select>
                </span>

                <span id="due-date">Due date: <input type="date" name="note-d-date"></span>
            <input type="button" value="Add note" onclick="submitNote()" class="btn btn-primary"';
            if(isset($_POST['classCode']) && $_POST['classCode'] == 'All'){
                echo ' disabled';
            }
            echo '>  
            <div id="note-to-all-modal"><div id="note-to-all-container">Are you sure you want to add the same note to all of your subjects students journal<input type="submit" onclick="confirmSubmitNote()" value="Yes, add note"><input type="button" value="cancel" onclick="cancelNote()"></div> </div>
            </form>
           ';
    }
    echo '</div>';
    if((isset($_POST['classCode']) && $_POST['classCode'] == 'All') || isset($_SESSION['journalAll'])){
        $sql = 'SELECT journal.date,journal.description,journal.due_date,timetable.subject from journal INNER JOIN (SELECT subject,subjectCode FROM timetable GROUP BY subjectCode) timetable ON timetable.subjectCode = journal.subjectCode AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '" AND journal.subjectCode IN ("' . $subjectCodes . '")';
        $sqlSupervision = 'SELECT type, date FROM supervision WHERE staff_id = "' . $_SESSION['userId'] . '" AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '"';
    }
    else{
        $sql = 'SELECT journal.date,journal.description,journal.due_date,timetable.subject from journal INNER JOIN (SELECT subject,subjectCode FROM timetable GROUP BY subjectCode) timetable ON timetable.subjectCode = journal.subjectCode AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '" AND journal.subjectCode = "' . $_SESSION['subjectNow'] . '"';
        $sqlGrade = 'SELECT classactivities.date,classactivities.description,timetable.subject from classactivities INNER JOIN (SELECT subject,subjectCode FROM timetable GROUP BY subjectCode) timetable ON timetable.subjectCode = classactivities.subjectCode AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '" AND classactivities.subjectCode = "' . $_SESSION['subjectNow'] . '"';
        $sqlSupervision = 'SELECT type, date FROM supervision WHERE staff_id = "' . $_SESSION['userId'] . '" AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '"';
    }
}
else if($_SESSION['accountType'] == 'parent'){
    $sql = 'SELECT journal.date,journal.description,journal.due_date,timetable.subject from journal INNER JOIN (SELECT subject,subjectCode FROM timetable GROUP BY subjectCode) timetable ON timetable.subjectCode = journal.subjectCode AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '" AND journal.subjectCode IN ("' . $subjectCodes . '")';
}
else{
    $sql = 'SELECT journal.date,journal.description,journal.due_date,timetable.subject from journal INNER JOIN (SELECT subject,subjectCode FROM timetable GROUP BY subjectCode) timetable ON timetable.subjectCode = journal.subjectCode AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '" AND journal.subjectCode IN ("' . $subjectCodes . '")';
}
if(!isset($sqlGrade)){
    $sqlGrade = $sqlGrade = 'SELECT tests.date,tests.description,tests.subject,grades.mark FROM grades RIGHT JOIN(SELECT classactivities.date,classactivities.description,timetable.subject from classactivities INNER JOIN (SELECT subject,subjectCode FROM timetable GROUP BY subjectCode) timetable ON timetable.subjectCode = classactivities.subjectCode AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '" AND classactivities.subjectCode IN ("' . $subjectCodes . '")) tests ON grades.student_id = "' . $_SESSION['userId'] . '" AND grades.date = tests.date';
}
if($_SESSION['accountType'] != 'staff'){
    $sqlDetention = 'SELECT * FROM detention WHERE student_id = "' . $_SESSION['userId'] . '" AND date BETWEEN "' . $startDate . '" AND "' . $endDate . '"';
    $result = $conn->query($sqlDetention);
    while($row = $result->fetch_assoc()){
        $dateSplit = explode('-',$row['date']);
        $today = date("l", mktime(0,0,0,$dateSplit[1],explode(' ',$dateSplit[2])[0],$dateSplit[0]));
        $days[$today] .= '<tr><td style="color:red;text-align:center;" colspan="3"> ' . $row['detention_type'] . ' Detention</td></tr>'; 
    }
}

if(isset($sqlSupervision)){
    $result=$conn->query($sqlSupervision);
    while($row=$result->fetch_assoc()){
        $dateSplit = explode('-',$row['date']);
        $today = date("l", mktime(0,0,0,$dateSplit[1],explode(' ',$dateSplit[2])[0],$dateSplit[0]));
        if(isset($days[$today])){
            $days[$today] .= '<tr><td style="color:red;text-align:center;" colspan="3"> ' . $row['type'] . ' Supervision</td></tr>';        
        }
    }
}
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $dateSplit = explode('-',$row['date']);
    $today = date("l", mktime(0,0,0,$dateSplit[1],explode(' ',$dateSplit[2])[0],$dateSplit[0]));
    $d_date = ($row['due_date'] != "0000-00-00")?$row['due_date']:"";
    $days[$today] .= '<tr><td>' . $row['subject'] . '</td><td>' . $row['description'] . '</td><td>' . $d_date . '</td></tr>'; 
}
$notes = '';
$sql = 'SELECT notes.*,staff.name FROM notes LEFT JOIN staff ON staff.staff_id = notes.sender_id WHERE notes.date = "' . $startDate . '" AND (notes.reciever_id ="' . $_SESSION['userId'] . '" OR notes.reciever_id IN ("' . $subjectCodes . '")) ORDER BY assigned_at ASC';
$result=$conn->query($sql);
echo mysqli_error($conn);
while($row = $result->fetch_assoc()){
    $notes .= '<tr><td>' . $row['name'] . '</td><td>' . $row['note'] . '</td></tr>';
}
$result=$conn->query($sqlGrade);
echo mysqli_error($conn);
while($row = $result->fetch_assoc()){
    $dateSplit = explode('-',$row['date']);
    $today = date("l", mktime(0,0,0,$dateSplit[1],explode(' ',$dateSplit[2])[0],$dateSplit[0]));
    if(!isset($row['mark'])){
        $mark='';
    }
    else{
        $mark = ' :' . $row['mark'] . '%';
    }
    $days[$today] .= '<tr><td>TEST-' . $row['subject'] . '</td><td>' . $row['description'] . $mark . '</td><td></td></tr>'; 
}
?>

<div class='ml-5 mt-2'><?php echo $startDate . ' - ' . $fridayDate?></div>   
<div class='page-change' id='prev-page-change'>
    <img class='page-change-icon' <?php echo ($startDate > $schoolStartDate)? 'src="images/left-active.svg" onclick="changeJrnlPage(\'' . $prevDate . '\')"':'src="images/left-inactive.svg"';?>>
</div>
<div class='page-change' id='next-page-change'>
    <img class='page-change-icon' <?php echo ($endDate < $schoolEndDate)? 'src="images/right-active.svg" onclick="changeJrnlPage(\'' . $nextDate . '\')"':'src="images/right-inactive.svg"';?>>
</div>  
<div id='jrnl-container' class='row ml-0 mr-0 mb-3'>
    <div class='col-12 col-lg-4 p-0'>
        <div class='jrnl-day' style='background-color:rgba(54, 162, 235, 0.2)'>
            <table>
                <tr><th>Monday</th></tr>
                <tr><th>Subject</th><th>Description</th><th>due date</th></tr>
                <?php echo $days['Monday'] ?>
            </table>
        </div>
        <div class='jrnl-day' style='background-color:rgba(255, 206, 86, 0.2)'>
        <table>
                <tr><th>Tuesday</th></tr>
                <tr><th>Subject</th><th>Description</th><th>due date</th></tr>
                <?php echo $days['Tuesday'] ?>
            </table>
        </div>
    </div>
    <div class='col-12 col-lg-4 p-0'>
        <div class='jrnl-day' style='background-color:rgba(75, 192, 192, 0.2)'>
        <table>
                <tr><th>Wednesday</th></tr>
                <tr><th>Subject</th><th>Description</th><th>due date</th></tr>
                <?php echo $days['Wednesday'] ?>
            </table>
        </div>
        <div class='jrnl-day' style='background-color:rgba(153, 102, 255, 0.2)'>
        <table>
                <tr><th>Thursday</th></tr>
                <tr><th>Subject</th><th>Description</th><th>due date</th></tr>
                <?php echo $days['Thursday'] ?>
            </table>
        </div>
    </div>
    <div class='col-12 col-lg-4 p-0'>
        <div class='jrnl-day' style='background-color:rgba(255, 159, 64, 0.2)'>
        <table>
                <tr><th>Friday</th></tr>
                <tr><th>Subject</th><th>Description</th><th>due date</th></tr>
                <?php echo $days['Friday'] ?>
            </table>
        </div>
        <div class='jrnl-day' style='background-color:rgba(160, 200, 100, 0.2)'>
        <table>
                <tr><th>Notes</th></tr>
                <?php echo $notes ?>
            </table>
        </div>
    </div>
</div>
<form id='journal-date' action='journal.php' method='post'>
    <input type='hidden' id='journal-date-input' name='journal-date'>
</form>

</body>
</html>
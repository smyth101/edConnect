<?php session_start();
require('connection.php');

if(!isset($_SESSION['userId'])){
    header('location:login.php');
}

if(isset($_GET['sc'])){
    $sql = 'SELECT subjectCode FROM timetable WHERE subject = "' . $_GET['sc'] . '" GROUP BY subjectCode';
    $result = $conn->query($sql);
    $response = '';
    while($row = $result->fetch_assoc()){
        if($row['subjectCode'] == $_GET['c']){
            $response .= '<option selected>' . $row['subjectCode'] . '</option>';
        }
        else{
            $response .= '<option>' . $row['subjectCode'] . '</option>';
        }
    }
    echo $response;
    exit();
}

$s = $_GET["s"];
if(isset($_GET['lo']) && isset($_GET['lv'])){
    $lo = $_GET['lo'];
    $lv = $_GET['lv'];
    if($lo == 'subjectCode'){
        $sql = 'SELECT * FROM timetable WHERE ' . $lo . ' LIKE "' . $lv . '"';
    }
    else{
        $sql = 'SELECT * FROM timetable WHERE ' . $lo . ' = "' . $lv . '"';
    }
    
}
else if($_GET['s'] != 'false'){
    $sql = 'SELECT * FROM timetable WHERE subjectCode LIKE "' . $s . '%" OR subject LIKE "' . $s . '%"';
}
else{
    $sql= 'SELECT * FROM timetable';
}
$response = '';            
$result = $conn->query($sql);
while($row = $result->fetch_assoc()){
    $response .= '<tr>';
    $response .= '<td>' . $row['day'] . '</td>';
    $response .= '<td>' . $row['period'] . '</td>';
    $response .= '<td>' . $row['subject'] . '</td>';
    $response .= '<td>' . substr($row['subjectCode'],3,1) . '</td>';
    $response .= '<td>' . $row['subjectCode'] . '</td>';
    $response .= '<td>' . substr($row['start_time'],0,5) . '</td>';
    $response .= '<td>' . substr($row['end_time'],0,5) . '</td>';
    $response .= '<td>' . $row['room'] . '</td>';
    $response .= '<td><button onclick=\'editTimetable(event.target)\' type="button">edit</button></td>';
    $response .= '</tr>';
    
}

echo $response;

?>
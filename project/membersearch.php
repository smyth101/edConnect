<?php
session_start();
require('connection.php');
if(!isset($_SESSION['userId'])){
  header('location:login.php');
}

$q=$_GET["q"];

if (strlen($q)>0) {
  $hint="";
  $sql = 'SELECT * FROM students WHERE name LIKE "' . $q . '%" LIMIT 5';
  $result = $conn->query($sql);
  if($_SESSION['accountType'] == 'staff'){
      while($row = $result->fetch_assoc()){
          $hint .= '<span style="cursor:pointer" onclick="addActivityMember([\''. $row['student_id'] . '\'],[\'' . addslashes($row['name']) . '\'])">' . $row['name'] . '</span><br>';
      }
      $sql = 'SELECT * FROM timetable WHERE subjectCode LIKE "' . $q . '%" GROUP BY subjectCode LIMIT 5';
      $result = $conn->query($sql);
      while($row = $result->fetch_assoc()){
        $sqlStudents = 'SELECT name,student_id FROM students WHERE subjectCode LIKE "%' . $row['subjectCode'] . '%"';
        $resultStudents = $conn->query($sqlStudents);
        $memberNameList = [];
        $memberIdList = [];
        while($student = $resultStudents->fetch_assoc()){
            array_push($memberIdList,$student['student_id']);
            array_push($memberNameList,$student['name']);
        }
        $sqlStaff = 'SELECT name,staff_id FROM staff WHERE subjectCode LIKE "%' . $row['subjectCode'] . '%" AND staff_id !="' . $_SESSION['userId'] . '"';
        $resultStaff = $conn->query($sqlStaff);
        while($staff = $resultStaff->fetch_assoc()){
            array_push($memberIdList,$staff['staff_id']);
            array_push($memberNameList,addslashes($staff['name']));
        }
        $memberNames = '';
        for($i = 0;$i < sizeof($memberNameList)-1;$i++){
          $memberNames .= '\'' . addslashes($memberNameList[$i]) . '\',';
        }
        $memberNames .= '\'' . addslashes($memberNameList[sizeof($memberNameList)-2]) . '\'';
        $hint .= '<span style="cursor:pointer" onclick="addActivityMember([\''. implode('\',\'',$memberIdList) . '\'],[' . $memberNames . '])">' . $row['subjectCode'] . '</span><br>';
      }
  }
  $sql = 'SELECT * FROM staff WHERE name LIKE "' . $q . '%" LIMIT 3';
  $result = $conn->query($sql);
  while($row = $result->fetch_assoc()){
      $hint .= '<span style="cursor:pointer" onclick="addActivityMember([\''. $row['staff_id'] . '\'],[\'' . addslashes($row['name']) . '])">' . $row['name'] . '</span><br>';
  }
}

if ($hint=="") {
  $response="no suggestion";
} else {
  $response=$hint;
}

echo $response;
?>
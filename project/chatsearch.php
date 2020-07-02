<?php
session_start();
if(!isset($_SESSION['userId'])){
  header('location:login.php');
}
require('connection.php');

$q=$_GET["q"];
if(isset($_GET["p"])){
  $p=$_GET["p"];
}
else{
  $p='';
}

if (strlen($q)>0) {
  $hint="";
  $sql = 'SELECT * FROM students WHERE name LIKE "' . $q . '%" LIMIT 5';
  $result = $conn->query($sql);
  if($_SESSION['accountType'] == 'staff'){
      // student list
      while($row = $result->fetch_assoc()){
          $hint .= '<span style="cursor:pointer" onclick="addChatMember([\''. $row['student_id'] . '\'],[\'' . addslashes($row['name']) . '\'],\'' . $p . '\')">' . $row['name'] . '</span><br>';
      }
      // students from subject
      $sql = 'SELECT * FROM timetable WHERE subjectCode LIKE "' . $q . '%" AND subjectCode IN ("' . implode('","',$_SESSION['subjectCodes'])  . '") GROUP BY subjectCode LIMIT 5';
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
            array_push($memberNameList,$staff['name']);
        }
        $memberNames = '';
        for($i = 0;$i < sizeof($memberNameList)-1;$i++){
          $memberNames .= '\'' . addslashes($memberNameList[$i]) . '\',';
        }
        $memberNames .= '\'' . addslashes($memberNameList[sizeof($memberNameList)-1]) . '\'';
        $hint .= '<span style="cursor:pointer" onclick="addChatMember([\''. implode('\',\'',$memberIdList) . '\'],[' . $memberNames . '])">' . $row['subjectCode'] . '</span><br>';
      }
      // parent list
      $sql = 'SELECT * FROM parents WHERE name LIKE "' . $q . '%" LIMIT 3';
      $result = $conn->query($sql);
      while($row = $result->fetch_assoc()){
          $hint .= '<span style="cursor:pointer" onclick="addChatMember([\''. $row['parent_id'] . '\'],[\'' . addslashes($row['name']) . '\'])">' . $row['name'] . '</span><br>';
      }
  } 
  // staff list
  $sql = 'SELECT * FROM staff WHERE name LIKE "' . $q . '%" LIMIT 3';
  $result = $conn->query($sql);
  while($row = $result->fetch_assoc()){
      $hint .= '<span style="cursor:pointer" onclick="addChatMember([\''. $row['staff_id'] . '\'],[\'' . addslashes($row['name']) . '\'])">' . $row['name'] . '</span><br>';
  }
}

if ($hint=="") {
  $response="no suggestion";
} else {
  $response=$hint;
}

echo $response;
?>
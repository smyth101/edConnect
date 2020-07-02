<?php
session_start();
if(!isset($_SESSION['userId'])){
  header('location:login.php');
}
require('connection.php');

$q=$_GET["q"];

if (strlen($q)>0) {
  $hint="";
  if($_SESSION['accountType'] == 'staff'){
    $sql = 'SELECT * FROM students WHERE name LIKE "' . $q . '%" LIMIT 5';
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        $hint .= '<span onclick="searchSubmit(\''. $row['student_id'] . '\')">' . $row['name'] . '</span><br>';
    }
  }
  $sql = 'SELECT * FROM staff WHERE name LIKE "' . $q . '%" AND staff_id != "' . $_SESSION['userId'] . '" LIMIT 3';
  $result = $conn->query($sql);
  while($row = $result->fetch_assoc()){
      $hint .= '<span onclick="searchSubmit(\''. $row['staff_id'] . '\')">' . $row['name'] . '</span><br>';
  }

}

if ($hint=="") {
  $response="no suggestion";
} else {
  $response=$hint;
}

echo $response;
?>
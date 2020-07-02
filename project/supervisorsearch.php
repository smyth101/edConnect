<?php
session_start();
require('connection.php');

$q=$_GET["q"];
if(isset($_GET["a"])){
  $func = 'addActionableSupervisor';
  $userVisible = '';
}
else{
  $func = 'addActivitySupervisor';
  $userVisible = 'AND staff_id != "' . $_SESSION['userId'] . '"';
}

if (strlen($q)>0) {
  $hint="";
  $sql = 'SELECT * FROM staff WHERE name LIKE "' . $q . '%" '  . $userVisible . ' LIMIT 3';
  $result = $conn->query($sql);
  while($row = $result->fetch_assoc()){
      $hint .= '<span style="cursor:pointer" onclick="' . $func . '([\''. $row['staff_id'] . '\'],[\'' . $row['name'] . '\'])">' . $row['name'] . '</span><br>';
  }
}

if ($hint=="") {
  $response="no suggestion";
} else {
  $response=$hint;
}

echo $response;
?>
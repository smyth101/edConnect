<?php
session_start();
require('connection.php');

$q=$_GET["q"];


if (strlen($q)>5) {
  $hint="";
  $sql = 'SELECT * FROM staff WHERE login_id = "' . $q . '"';
  $result = $conn->query($sql);
  if($result->num_rows > 0){
      $response = '<span class="text-danger">name taken</span>';
  }
  else{
      $response = '<span class="text-success">name available</span>';
  }
}
else{
    $response = '(minimum 6 characters)';
}

echo $response;
?>
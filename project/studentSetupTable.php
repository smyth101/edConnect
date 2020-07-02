<?php
     session_start();
     require('connection.php');
     require('common-functions.php');
     
     
     if(!isset($_SESSION['userId'])){
         header('location:login.php');
     }
     $s = $_GET['s'];
     if($s == "false"){
        $sql = 'SELECT name, year, student_id, address FROM students WHERE password = ""';
     }
     else{
         $sql = 'SELECT name, year, address, student_id FROM students WHERE password = "" AND subjectCode = "' . $s . '"';
     }
     $result = $conn->query($sql);
     $response = '';
     while($row = $result->fetch_assoc()){
         $response .= '<tr><td>' . $row['name'] . '</td><td>' . $row['year'] . '</td><td>' . $row['address'] . '</td><td><button type="button" data-toggle="modal" data-target="#confirmStudentModal" onclick="document.getElementById(\'confirmAddress\').innerHTML=\'' . $row['address'] . '\';document.getElementById(\'confirmId\').value=\'' . $row['student_id'] . '\';document.getElementById(\'confirmName\').innerHTML=\'' . addslashes($row['name']) . '\';">Set up</button></td></tr>';
     }
     echo $response;
 

?>
<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }


    $s = $_GET['s'];
    if($s == "false"){
        $s = '';
    }
    $sql = 'SELECT * FROM students WHERE name LIKE "' . $s . '%"';

    $result = $conn->query($sql);
    $response = '';
    while($row = $result->fetch_assoc()){
        $response .= '<tr><td>' . $row['name'] . '</td><td>' . $row['year'] . '</td><td>' . $row['subjectCode'] . '</td><td><button type="button" onclick="editStudent(event.target,\'' . $row['student_id'] . '\')">edit</button><button type="button" onclick="forgottenCred(\'' . $row['name'] . '\',\'' . $row['address'] . '\',\'' . $row['student_id'] . '\')" class="btn default"><u>Forgotten Credentials</u></td></tr>';
    }
    echo $response;
?>
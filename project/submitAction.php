<?php
session_start();
if(!isset($_SESSION['userId'])){
    header('location:login.php');
}
require('connection.php');
$stmt = $conn->prepare('INSERT INTO detention (staff_id,student_id,detention_type,date,assigned_at,reason) VALUES(?, ?, ?, ?, ?, ?)');
$stmt->bind_param('ssssss',$_SESSION['userId'],$sid,$type,$date,$assigned,$reason);
$sid = $_POST['action-user'];
$type = $_POST['detention-type'];
$date = $_POST['detention-date'];
$assigned = $_POST['detention-assigned'];
$reason = $_POST['detention-reason'];
$stmt->execute();
?>
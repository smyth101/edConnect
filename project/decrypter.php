<?php
session_start();
if(!isset($_SESSION['userId'])){
    header('location:login.php');
}
require('common-functions.php');



if(!isset($_GET['f'])){
    echo '';
    exit();
}
$permission = false;
if(explode('/',$_GET['f'])[0] == 'student_profile'){
    $uid = explode('.',explode('/',$_GET['f'])[1])[0];
    if($_SESSION['accountType'] == 'staff' || $_SESSION['userId'] == $uid){
        $permission = true;
    }
}
else{
    $bulk = new MongoDB\Driver\BulkWrite;
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    $query = new MongoDB\Driver\Query(['members' => $_SESSION['accountId']]);
    $rows = $manager->executeQuery('chat.conversation',$query);
    $id = explode('/',$_GET['f']);
    $id = $id[1];
    foreach($rows as $row){
        if($row->_id == $id){
            $permission = true;
        }
    }
}
if($permission == false){
    echo '';
    exit();

}
$content = $_GET['f'];
echo '{"data": ' . '"' . base64_encode(decrypt_file(file_get_contents($_GET['f']),$pKey)) . '",';
echo '"extention":' . '"' . strtolower(pathinfo($_GET['f'],PATHINFO_EXTENSION)) . '"';
echo '}';
?>
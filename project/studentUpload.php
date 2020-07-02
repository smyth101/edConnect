<?php
session_start();
if(!isset($_SESSION['userId'])){
    header('location:login.php');
}
require('common-functions.php');
$target_dir = 'student_profile/';
$file_name = basename($_FILES["fileToUpload"]["name"]);
$target_file = $target_dir . $file_name;
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$target_file = $target_dir . $_POST['imageId'] . '.' . $fileType;

// Check file size
if ($_FILES["fileToUpload"]["size"] > 2000000) {
    $uploadOk = 0;
}
// Allow certain file formats
if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" ) {
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if($uploadOk == 0) {
    $_SESSION['errorMessage'] = "Sorry, your file was either of wrong file type or too large.";
// if everything is ok, try to upload file
}
else{
    $fileContent = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
    $encryptedContent = encrypt_file($fileContent,$pKey);
    file_put_contents($_FILES["fileToUpload"]["tmp_name"], $encryptedContent);
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

    }
    else {
        $_SESSION['errorMessage'] =  "Sorry, there was an error uploading your file.";
    }
}
header('location:studentManagement.php');
?>

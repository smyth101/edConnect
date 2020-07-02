<?php
// session_start();
if(!isset($_SESSION['userId'])){
    header('location:login.php');
}
$target_dir = 'conversation_files/' . $_POST['fileFolderId'] . '/';
$file_name = basename($_FILES["fileToUpload"]["name"]);
$target_file = $target_dir . $file_name;
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if file already exists
while(file_exists($target_file)){
    $explodedFileName = explode('.',$file_name);
    $extType = array_pop($explodedFileName);
    $extType = strtolower($extType);
    if($extType == 'jpg' || $extType == 'jpeg' || $extType == 'png' || $extType == 'gif' || $extType == 'pdf' || $extType == 'docx' || $extType == 'pptx'){
        $file_name = implode($explodedFileName) . '(1).' . $extType;
    }
    else{
        $file_name .= '(1)';
    }
    $target_file = $target_dir . $file_name;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 20000000) {
    $uploadOk = 0;
}
// Allow certain file formats
if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" && $fileType != "pdf" && $fileType != "docx" && $fileType != "pptx" ) {
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
        $_SESSION['imageName'] = $file_name;
        $_SESSION['convId'] = $_POST['fileFolderId'];
        if($fileType == "jpg" || $fileType == "png" || $fileType == "jpeg" || $fileType == "gif"){
            $_SESSION['fileType'] = 'img';
        }
        else{
            $_SESSION['fileType'] = 'file';
        }
        $fi = new FilesystemIterator('conversation_files/' . $_POST['fileFolderId'] . '/', FilesystemIterator::SKIP_DOTS);
        $filecount = iterator_count($fi);
        if($filecount == 1){
            copy('403template.html','conversation_files/' . $_POST['fileFolderId'] . '/index.html');
        }

    } 
    else {
        $_SESSION['errorMessage'] =  "Sorry, there was an error uploading your file.";
    }
}
// header('location:chat.php');
?>

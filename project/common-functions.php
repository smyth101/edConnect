<?php
    function setSubjectCodes($conn){
        if(!isset($_SESSION['subjectCode'])){
            $dbAccountType = $_SESSION['dbAccountType'];
            $accountType = $_SESSION['accountType'];
            $accountId = $_SESSION['accountId'];
            if($accountType == 'parent'){
                $sql = 'SELECT student_id FROM parents WHERE parent_id = "' . $accountId . '"';
                $studentId = $conn->query($sql);
                echo mysqli_error($conn);
                $row = $studentId->fetch_assoc();
                $accountType = 'student';
                $dbAccountType = 'students';
            }
            if(isset($_SESSION['staffPrivileges']) && $_SESSION['staffPrivileges'] == 'higher' && isset($_SESSION['principalView']) && $_SESSION['principalView'] == 'true'){
                if(isset($_POST['principalViewStaff']) || isset($_SESSION['principalViewStaff'])){
                    if(!isset($_SESSION['principalViewStaff'])){
                        $_SESSION['principalViewStaff'] = $_POST['principalViewStaff']; 
                    }
                    $sql = 'SELECT subjectCode FROM staff WHERE staff_id="' . $_SESSION['principalViewStaff'] . '"';
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    $resultList = explode(',',$row['subjectCode']);
                }
                else{
                    $sql = 'SELECT subjectCode FROM timetable GROUP BY subjectCode ORDER BY subjectCode ASC';
                    $result = $conn->query($sql);
                    $resultList = [];
                    while($row = $result->fetch_assoc()){
                        array_push($resultList,$row['subjectCode']);
                    }
                }
                $_SESSION['subjectCodes'] = $resultList;
            }
            else{
                $sql = 'SELECT subjectCode FROM ' . $dbAccountType . ' WHERE ' . $accountType . '_id="' . $_SESSION['userId'] . '"';
                $subjctList = $conn->query($sql);
                echo mysqli_error($conn);
                $row = $subjctList->fetch_assoc();
                $_SESSION['subjectCodes'] = explode(',',$row['subjectCode']);
            }
        }
    }

    function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function principalViewCheck($conn){
        if(isset($_POST['principal-view'])){
            $_SESSION['principalView'] = $_POST['principal-view'];
            if($_SESSION['principalView'] == 'false'){
                unset($_SESSION['subjectNow']);
                unset($_SESSION['principalViewStaff']);
            }
        }
    
        if(isset($_POST['principal-view']) || !isset($_SESSION['subjectCodes'])){
            setSubjectCodes($conn);
            if(isset($_POST['principal-view']) && !isset($_SESSION['subjectNow'])){
                $_SESSION['subjectNow'] = $_SESSION['subjectCodes'][0];
            }
        }
    }

    $pKey = 'wMVGtyYIV2cuEfrVlO7hQ8g7qHUue81R';
    function encrypt_file($file,$pKey){
        $encryption_key = base64_decode($pKey);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($file, 'aes-256-cbc', $encryption_key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    function decrypt_file($file,$pKey){
        $encryption_key = base64_decode($pKey);
        list($encrypted_data, $iv) = explode('::', base64_decode($file), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
    }

    function sendMail($schoolName,$receiverEmail,$receiverName,$subject,$message){
       require '../vendor/autoload.php';
       $email = new \SendGrid\Mail\Mail(); 
       $email->setFrom("info@edconnect.ie", $schoolName . "-Edconnect");
       $email->setSubject($subject);
       $email->addTo($receiverEmail, $receiverName);
       $email->addContent("text/plain", $message);
       $email->addContent(
          "text/html", $message
       );
       $sendgrid = new \SendGrid('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');
       try {
          $response = $sendgrid->send($email);
       } catch (Exception $e) {
       echo 'Caught exception: '. $e->getMessage() ."\n";
       }
   }    
?>

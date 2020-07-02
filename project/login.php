<?php 
    session_start();
    if(isset($_POST['signout'])){
        session_destroy();
    }
    require 'connection.php';
    require 'common-functions.php';
    require 'info.php';

    if (isset($_POST['accountType'])) {
        $accountType = $_POST['accountType'];
        $userID = $conn->real_escape_string($_POST['userID']);
        $password = md5($_POST['password']);
        $dbAccountType = $_POST['accountType'] . ($_POST['accountType'] == 'student' || $_POST['accountType'] == 'parent'?"s":"");
        $sql = "SELECT * FROM ". $dbAccountType . " WHERE login_id ='$userID';";
        $user_details = mysqli_query($conn,$sql);
        if(mysqli_num_rows($user_details) == 1){
            $result = mysqli_fetch_assoc($user_details);
            if($password == $result['password'] ){
                $_SESSION['name'] = $result['name'];
                if($accountType == 'parent'){
                    $_SESSION['userId'] = explode(',',$result['student_id'])[0];
                }
                else{
                    $_SESSION['userId'] = $result[$accountType . '_id'];
                }
                if($accountType == 'staff'){
                    $_SESSION['staffPrivileges'] = $result['type'];
                }
                $_SESSION['accountType'] = $accountType;
                $_SESSION['accountId'] = $result[$accountType . '_id'];
                $_SESSION['dbAccountType'] = $dbAccountType;
                header("location: index.php");
            }
            else{
                $errorMessage = "<span class='col-12 text-danger'><b>Incorrect username and/or password entered.</b></span>";
            }
        }
        else{
            $errorMessage = "<span class='col-12 mt-2 text-danger'><b>Incorrect username and/or password entered</b></span>";
        }
    }
    else if(isset($_POST['email'])){
        if($_POST['recoverAccount'] == 'staff'){
            $stmt = $conn->prepare('UPDATE staff SET p_code = ? WHERE email = ?');
            $aType = 'staff';
        }
        else{
            $stmt = $conn->prepare('UPDATE parents SET p_code = ? WHERE email = ?');
            $aType = 'parents';
        }
        $pCode = generateRandomString(16);
        $stmt->bind_param('ss',$pCode,$_POST['email']);
        $stmt->execute();
        if($stmt->affected_rows == 0){
            $errorMessage = "<span class='col-12 mt-2 text-danger'><b>Invalid email entered</b></span>";
        }
        else{
            $errorMessage = "<span class='col-12 mt-2 text-success'><b>Password recovery sent</b></span>";
            sendMail($schoolName,$_POST['email'],'unknown','edConnect Account Recovery','To change your edConnect account password simply go to <a>edconnect.ie/setPassword.php?p_code=' . $pCode . '&a_type=' . $aType . '</a>');
        }
    }
    
?>
<!DOCTYPE html>
<head>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href='style.css?<?php echo time(); ?>'>
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>
        edconnect | login
    </title>
</head>
<body>  
    <div class='row ml-0 justify-content-center login-bg'>
        <div class='login-modal col-7 col-lg-3'>
            <span>
            <span class='mb-2 d-none' id='back-arrow'>
                <img src='images/back_arrow.png' style='cursor:pointer' onclick='hideLogin()'>
            </span>
            <h2 class='d-inline mb-2'>Log In</h2></span><br>
            <?php echo (isset($errorMessage))?$errorMessage:''?>
            <div class="btn-group" data-toggle="buttons">
                <form method='post' action='login.php'>
                    <label class="btn btn-primary col-12 mt-3">
                        Staff <input type='radio' value='staff' name='accountType' class='signin-btn' onclick="showLogin(this.value)"><br>
                    </label>
                    <label class="btn btn-primary col-12">
                        Student <input type='radio' value='student' name='accountType' class='signin-btn' onclick="showLogin()"><br>
                    </label>
                    <label class="btn btn-primary col-12">
                        Parent/Guardian <input type='radio' value='parent' class='signin-btn' name='accountType' onclick="showLogin(this.value)"><br>
                    </label>
            </div>
            <div class="login-details mt-3">
                User ID:  <input type='text' name='userID'><br><br>
                Password: <input type='password' name='password'>
                <button class='btn btn-primary mt-4'>Submit</button>
            </div>
            </form>
        </div>
    </div> 
</body>
</html>

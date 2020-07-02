<?php
    if(!isset($_GET['p_code']) || !isset($_GET['a_type'])){
        header('location:login.php');
    }
    require('connection.php');

    if(isset($_POST['new_password'])){
        if($_POST['new_password'] != $_POST['confirm_password']){
            $errorMessage = 'Passwords did not match';
        }
        else{
            if(isset($_POST['login_name'])){
                if(trim($_GET['a_type'],'"') == 'staff'){
                    $stmt = $conn->prepare("UPDATE staff SET password = ?, login_id = ?, p_code = '' WHERE email = ? AND p_code = ?");
                }
                else{
                    $stmt = $conn->prepare("UPDATE parents SET password = ?, login_id = ?, p_code='' WHERE email = ? AND p_code = ?");
                }
                $stmt->bind_param('ssss',$password,$loginId,$email,$pCode);
                $loginId = $_POST['login_name'];

            }
            else{
                if(trim($_GET['a_type'],'"') == 'staff'){
                    $stmt = $conn->prepare("UPDATE staff SET password = ? WHERE email = ? AND p_code = ?");
                }
                else{
                    $stmt = $conn->prepare("UPDATE parents SET password = ? WHERE email = ? AND p_code = ?");
                }
                $stmt->bind_param('sss',$password,$email,$pCode);
            }
            $email = $_POST['email'];
            $password = md5($_POST['new_password']);
            $pCode = trim($_GET['p_code'],'"');
            echo $conn->error;
            $stmt->execute();
            if($stmt->affected_rows == 1){
                $successMessage = '<span class="col-12 text-success">Account has been successfully updated</span>';
            }
            else{
                $errorMessage = 'There was a problem updating your details';
            }
        }
    }
?>
<!DOCTYPE html>
<head>
    <title>
        edConnect
    </title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div id='main-nav' class="row ml-0 mr-0">
        <ul class='col-9 nav'>
            <li id='logo' class='col-1' style='margin-left:0'>
                <span style='color:red'>e<span style='color:blue'>d<span style='color:grey'>Connect</span>
            </li>
        </ul>
</div>
<div class='container'>
    <div class='row justify-content-center text-center mt-3'>
        <h3 class='col-12'>Account Creation/Recovery</h3>
        <?php if(isset($successMessage)){
            echo $successMessage;
            echo '</div></div></body></html>';
            exit();
        }
        ?>
    </div>
    <form action='setPassword.php?p_code=<?php echo $_GET['p_code'] ?>&a_type=<?php echo $_GET['a_type']?>' method='post'>
        <div class='row justify-content-center text-center'>
            <p class='col-12'>Confirm email to set username and password or to reset your password</p>
            <?php if(isset($_POST['email'])){
                $sql = 'SELECT * FROM ' . trim($_GET['a_type'],'"') . ' WHERE email = "' . $_POST['email'] . '" AND p_code="' . trim($_GET['p_code'],'"') . '"';
                $result = $conn->query($sql);
                if($result->num_rows == 0){
                    echo '<span class="col-12 text-danger">Please ensure that the link is the one that was sent and that your email is entered correctly</span>';
                    echo "Email: <input type='email' name='email' required>
                    <button class='btn btn-primary ml-3'>Submit</button>";
                }
                else{
                    $row = $result->fetch_assoc();
                    if($_GET['p_code'] == $row['p_code'] || strlen($row['p_code']) == 16){
                        if(isset($errorMessage)){
                            echo '<span class="col-12 text-danger">' . $errorMessage . '</span>';
                        }
                        else{
                            echo '<span class="col-12 text-success">Email Confirmed</span>';
                        }
                        if($row['password'] == ''){
                            echo '<span class="col-4 offset-2" >login Username: <input type="text" onkeyup="checkLoginNameAvailable(this.value)" name ="login_name" required></span><div id="name_status" class="text-left">(minimum 6 characters)</div>';
                        }
                        else{
                            echo '<span class="col-4 offset-2 text-center" >login Username: ' . $row['login_id'] . '</span>';
                        }
                        echo '<span class="col-12 mt-3">New Password: <input type="password" name="new_password" required></span>';
                        echo '<span class="col-12 mt-3">Confirm Password: <input type="password" name="confirm_password" required></span>';
                        echo '<input type="hidden" name="email" value="' . $_POST['email'] . '">';
                        echo '<span class="col-12 mt-3"><button class="btn btn-primary">Submit</button></span>';
                    }
                    else{
                        echo '<span class="col-12 text-danger">Please ensure that the link is the one that was sent and that your email is entered correctly</span>';
                        echo "Email: <input type='email' name='email' required>
                        <button class='btn btn-primary ml-3'>Submit</button>";
                    }
                }

            }
            else{
                echo "Email: <input type='email' name='email' required>
                <button class='btn btn-primary ml-3'>Submit</button>";
            }?>
        </div>
    </form>
</div>
</body>
</html>

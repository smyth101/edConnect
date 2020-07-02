<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }
    if($_SESSION['accountType'] != 'staff'){
        header('location:index.php');
    }
    
    unset($_SESSION['subjectCodes']);
    unset($_SESSION['principalView']);
    unset($_SESSION['principalViewStaff']);
    setSubjectCodes($conn);

    if(isset($_POST['search-value'])){
        $userId = $_POST['search-value'];
        $_SESSION['profileSearch'] = $userId;
    }
    else if(isset($_POST['profile-user'])){
        $userId = $_POST['profile-user'];
    }
    else{
        $userId = $_SESSION['profileSearch'];
    }
    $sql = 'SELECT student_id FROM students WHERE student_id="' . $userId . '"';
    $result = $conn->query($sql);
    if($result->num_rows == 0){
        $profileType = 'staff';
    }
    else{
        $profileType = 'student';
    }

    if(isset($_POST['detention-type'])){
        $detentionType = $_POST['detention-type'];
        $reason = $_POST['detention-reason'];
        $date = $_POST['detention-date'];
        $assigned = $_POST['detention-assigned'];
        $sql = 'SELECT student_id FROM detention WHERE student_id ="' . $userId . '" AND detention_type = "' . $detentionType . '" and date = "' . $date . '"';
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            $errorMessage = '<span class="text-danger">This student already has a detention for the chosen time</span>';
        }
        else{
            $stmt = $conn->prepare("INSERT INTO detention (student_id,staff_id,detention_type,date,assigned_at,reason) VALUES (?, ?, ?, ?,? ,? )");
            $stmt->bind_param("ssssss",$userId, $_SESSION['userId'], $detentionType, $date, $assigned, $reason );
            $stmt->execute();
        }
    }

?>
<!DOCTYPE html>
<head>
    <title>edConnect | profile</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
</head>
<body>
<?php
require('navbar.php');
?>
<div class='row ml-0 mr-0'>
<div id='profile-main-container' class='col-lg-8 offset-lg-1 col-12'>
    <div id='profile-header'>

    <img src=
        <?php
        if(!file_exists('student_profile/' . $userId . '.jpg') && !file_exists('student_profile/' . $userId . '.jepg') && !file_exists('student_profile/' . $userId . '.png')){
            echo '"images/profile.jpg"';
        };
        ?>
     width='100px' height='100px' id ='profile-image' class='m-3'>
    <?php
        if(file_exists('student_profile/' . $userId . '.jpg')){
            echo '<script>decryptFile("student_profile/' . $userId . '.jpg","profile-image")</script>';
        }
        else if(file_exists('student_profile/' . $userId . '.jepg')){
            echo '<script>decryptFile("student_profile/' . $userId . '.jpeg","profile-image")</script>';
        }
        else if(file_exists('student_profile/' . $userId . '.png')){
            echo '<script>decryptFile("student_profile/' . $userId . '.png","profile-image")</script>';
        }
        if($profileType == 'staff'){
            $sql = 'SELECT name from staff WHERE staff_id = "' . $userId . '"';
        }
        else{
            $sql = 'SELECT name from students WHERE student_id = "' . $userId . '"';
        }
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $profileName = $row['name'];
        echo '<h2 style="float:left" class="mt-4">' . $profileName . '</h2>' ;


        if($_SESSION['accountType'] == 'staff' && $profileType != 'staff'){
            echo "
            <input type='button' value='Detention' onclick='showProfileDetention()' class ='m-4'>
            <div id='profile-detention-container'>
                <form method='post' action='profile.php'>";
                if(isset($errorMessage)){
                    echo $errorMessage . '<br>';
                    echo '<script>window.onload = showProfileDetention()</script>';
                }
                echo "
                    <span class='mt-2 mt-lg-0'>Detention Date:
                        <input name='detention-date' type='date' value='" . (new DateTime('tomorrow'))->format('Y-m-d') . "'>
                    </span>
                    <span class='mt-2 mt-lg-0'>
                        Detention type: 
                        <select name='detention-type'>
                            <option selected='selected'>Lunch Time</option>
                            <option>After School</option>
                        </select>
                    </span>
                    <span class='mt-2 mt-lg-0'>
                        reason:
                        <input type='text' name='detention-reason'>
                        <input type='hidden' name='detention-assigned' value='" . date('Y-m-d') . "'>
                        <input type='hidden' name='profile-user' value='" . $userId . "'>
                    <span class='mt-2 mt-lg-0'>
                    <button class='mt-2 mt-lg-0'>Add detention</button><input type='button' value='cancel' onclick='cancelDetention()'>
                </form>
            </div>";
        }
    ?>
    </div>
    <?php 
        if($_SESSION['accountType']=='staff' && $profileType == 'student'){
            $sql = 'SELECT subjectCode FROM students WHERE student_id = "' . $userId . '"';
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $studentSC = explode(',',$row['subjectCode']);
            $diff = array_diff($studentSC,$_SESSION['subjectCodes']);
            if(sizeof($diff) == sizeof($studentSC) &&  $_SESSION['staffPrivileges'] != 'higher'){
                echo 'You do not have privileges to this students details';
                echo '</div></body></html>';
                exit();
            }
        }
    ?>
    <div id='prfile-attendance-container'  class='mb-4'>
        <h2>Attendance</h2>
        <?php
            if($profileType == 'staff'){
                echo '<form method="post" action="index.php">
                        <input type="hidden" value="true" name="principal-view">
                        <input type="hidden" value="' . $userId . '" name="principalViewStaff">
                        <button>View Attendance</button>
                    </form>';
            }
            else{
                echo "<button type='button'><a href='index.php?student=\"" . $userId  . "\"' class='text-dark'>view attendance</a></button>";
            }
        ?>
    </div>
    <div id='profile-grades-container' class='mb-4'>
        <h2>Grades</h2>
        <?php
            if($profileType == 'staff'){
                echo '<form method="post" action="grades.php?gradePage=analytics">
                        <input type="hidden" value="true" name="principal-view">
                        <input type="hidden" value="' . $userId . '" name="principalViewStaff">
                        <button>View Grades</button>
                    </form>';
            }
            else{
                echo "<button type='button'><a href='grades.php?gradePage=analytics&student=\"" . $userId  . "\"' class='text-dark'>View Grades</a></button>";
            }
        ?>
    </div>
    <div id='profile-grades-container'  class='mb-4'>
        <h2>Timetable</h2>
        <?php
            if($profileType == 'staff'){
                echo '<form method="post" action="timetable.php">
                        <input type="hidden" value="true" name="principal-view">
                        <input type="hidden" value="' . $userId . '" name="principalViewStaff">
                        <button>View Timetable</button>
                    </form>';
            }
            else{
                echo "<button type='button'><a href='timetable.php?student=\"" . $userId  . "\"' class='text-dark'>View Timetable</a></button>";
            }
        ?>
    </div>
</div>
<?php
    if($profileType == 'student'){
        echo "<div id='profile-side-container' class='col-12 col-lg-3 mt-3'><h2>Contact</h2>";
        $sql = 'SELECT * FROM students WHERE student_id = "' . $userId . '"';
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo 'Year: ' . $row['year'] . '<br>';
        echo 'Address: ' . $row['address'] . '<br>';
        $sql = 'SELECT * FROM parents WHERE student_id LIKE "%' . $userId . '%"';
        $result = $conn->query($sql);
        $contactIndex = 1;
        while($row = $result->fetch_assoc()){
            echo 'contact ' . $contactIndex . ' : ' . $row['name'] . '<br>';
            echo '   phone: ' . $row['phone'] . '<br>';
            echo '   email: ' . $row['email'] . '<br>';
            $contactIndex += 1;
        }
    }

?>
</div>
</body>
</html>
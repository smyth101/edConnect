<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }

    if(isset($_POST['image'])){
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $_POST['image']));
        $data = encrypt_file($data,$pKey);
        file_put_contents('student_profile/' . $_POST['imageId'] . '.png', $data);
    }

    if(isset($_POST['deleteId'])){
        $sql = 'DELETE FROM students WHERE student_id ="' . $_POST['deleteId'] . '"';
        $result = $conn->query($sql);
        if($conn->affected_rows == 0){
            $statusMessage = '<span class="text-danger">There was a problem removing this student</span>';
        }
        else if($conn->affected_rows == 1){
            $statusMessage = '<span class="text-success">Student successfully removed</span>';
            $sql = 'SELECT parent_id,student_id FROM parents WHERE student_id = "' . $_POST['deleteId'] . '" OR student_id LIKE "%,' . $_POST['deleteId'] . ',%" OR student_id LIKE "%,' . $_POST['deleteId'] . '" OR student_id LIKE "' . $_POST['deleteId'] . ',%"';
            $result=$conn->query($sql);
            while($row=$result->fetch_assoc()){
                $students = explode(',',$row['student_id']);
                $students = array_diff($students,[$_POST['deleteId']]);
                if(sizeof($students) == 0){
                    $stmt = $conn->prepare('DELETE FROM parents WHERE parent_id = ?');
                    $stmt->bind_param('s',$row['parent_id']);
                }
                else{
                    $stmt = $conn->prepare('UPDATE parents SET student_id = ? WHERE parent_id = ?');
                    $stmt->bind_param('ss',$studentList,$row['parent_id']);
                }
                $studentList = implode(',',$students);
                $stmt->execute();
            }
        }
    }
    if(isset($_POST['id'])){
        $stmt = $conn->prepare('UPDATE students SET name= ?, year = ?, subjectCode = ? WHERE student_id = ?');
        $stmt->bind_param('siss',$_POST['name'],$_POST['year'],$subjects,$_POST['id']);
        if(isset($_POST['subject'])){
            $subjects = implode(',',$_POST['subject']);
        }
        else{
            $subjects = '';
        }
        $stmt->execute();
        if($stmt->error){
            $statusMessage = '<span class="text-danger">There was a problem updating student</span>';
        }
        else{
            $statusMessage = '<span class="text-success">student successfully updated</span>';
        }
    }

?>
<!DOCTYPE html>
<head>
    <title>edConnect | manage students</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
</head>
<body>
<?php
require('navbar.php');
?>
<div class='row ml-0 mr-0 col-12 third-nav'>
    <ul class='nav'>
        <li class="third-nav-item"><a href='actionables.php'>Supervision</a></li>
        <li class="third-nav-item"><a href='detentions.php'>Detentions</a></li>
        <li class="third-nav-item"><a href='manageStaff.php'>Manage Staff</a></li>
        <li class="third-nav-item"><a href='timetableManagement.php'>Manage Timetable</a></li>
        <li class="third-nav-item"><a href='studentApproval.php'>Student Approval</a></li>
        <li class="third-nav-item"><a href='studentManagement.php'>Manage Students</a></li>
    </ul>
</div>
<div class='container'>
    <div class='row justify-content-center text-center mt-3'>
        <h3 class='col-12'>Set up Students</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Year</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody id='studentSetupList'>
            </tbody>
     </table>
    </div>
</div>


<div class='container'>
    <form action='studentManagement.php' method='post'>
        <div class='row justify-content-center mt-4 text-center'>
            <h3 class='col-12 text-center'>Student List</h3>
            <span class='col-12'><?php echo (isset($statusMessage))?$statusMessage:'';?></span>
            <?php echo (isset($updateMessage))?$updateMessage:'';?>
            <span class="col-12"><input type="text" onkeyup="showStudents(this.value)" placeholder="student name"></span>
            <div class='col-12 col-lg-10 mt-2 table-responsive'>
                <table class=' table'>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>year</th>
                            <th>subjectCode</th>
                        </tr>
                    </thead>
                    <tbody  id='studentList'>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
<script> 
    showStudentSetup();
    showStudents();
    <?php
        $sql = 'SELECT subjectCode FROM timetable GROUP BY subjectCode ORDER BY subjectCode DESC';
        $result = $conn->query($sql);
        $subjectList = [];
        $output = 'subjectList = ["';
        while($row=$result->fetch_assoc()){
            array_push($subjectList, $row['subjectCode']);
        }
        $output .= implode('","',$subjectList);
        $output .= '"];';
        echo $output;
    ?>
</script>
<form action="studentManagement.php" method="post">
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Remove Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary">Delete</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            </div>
        </div>
    </div>
</form>
<form action="studentManagement.php" method="post" id="studentForm">
        <?php
            if(isset($_POST['confirmId'])){
                $sql = 'SELECT name FROM students WHERE student_id = "' . $_POST['confirmId'] . '"';
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                $name = explode(' ', $row['name']);
                while(true){
                    if(sizeof($name) == 1){
                        $login = $name[0] . rand(0,9) . rand(0,9);
                    }
                    else{
                        var_dump($name);
                        $last = $name[sizeof($name) -1];
                        $login = $last . $name[0][0] . rand(0,9) . rand(0,9);
                    }
                    $sql = 'SELECT login_id FROM students WHERE login_id = "' . $login . '"';
                    $result = $conn->query($sql);
                    if($result->num_rows == 0){
                        break;
                    }
                }
                $password = strtolower(generateRandomString(8));
                $stmt = $conn->prepare('UPDATE students SET password = ?, login_id = ? WHERE student_id = ?');
                $passwordEncrypt = md5($password);
                $stmt->bind_param('sss',$passwordEncrypt,$login,$_POST['confirmId']);
                $stmt->execute();

                echo 
                '<div class="modal fade" id="studentCredentialModal" tabindex="-1" role="dialog" aria-labelledby="studentCredentialModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="studentCredentialModalLabel">Student Credentials</h5>
                        </div>
                        <div class="modal-body" id="confirmBody">
                            the following are your credentials for signing into edConnect<br>
                            Username: <b>' . $login . '</b><br>
                            Password: <b>' . $password . '</b>
                            <p>
                                Once you know your login credentials press confirm and give control back to staff member.
                            </p>
                        </div>
                        <div class="modal-footer" id="confirmFooter">
                            <button class="btn btn-primary" type="button" ' . ((isset($_POST['forgotCred']))?"data-dismiss=\"modal\" aria-label=\"Close\" " :'onclick="studentImage()"') .  ' id="confirmCredBtn">Confirm</button>
                        </div>
                        </div>
                    </div>
                </div>
                <script>$("#studentCredentialModal").modal("show")</script>';
            }
            else{
                echo 
                '<div class="modal fade" id="confirmStudentModal" tabindex="-1" role="dialog" aria-labelledby="confirmStudentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confrimStudentModalLabel">Confirm Student</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Please confirm that the student now in use of this device is <b><span id="confirmName"></span></b><br>address:<b><span id="confirmAddress"></span></b>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="confirmId" name="confirmId">
                            <button class="btn btn-primary">Confirm</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                        </div>
                    </div>
                </div>';
            }
        ?>
</form>
<?php 
if(isset($_POST['confirmId'])){
    echo '<script>var confId = "' . $_POST['confirmId'] . '"</script>';
}
?>
</body>
</html>
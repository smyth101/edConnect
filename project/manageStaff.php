<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    require('info.php');
    
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }
    if(isset($_POST['name0'])){
        $successCount = 0;
        $statusMessage = '';
        $i = 0;
        $stmt = $conn->prepare('INSERT INTO staff (name,email,subjectCode,staff_id,type,p_code,login_id,qualified_in) VALUES(?,?,?,?,?,?,?,?)');
        $stmt->bind_param('ssssssss',$name,$email,$subjectCode,$staff_id,$type,$pCode,$login,$qualifiedIn);
        while(isset($_POST['name' . $i])){
            $name =$_POST['name'. $i];
            $email = $_POST['email'. $i];
            if($name == '' || $email == ''){
                $i++;
                continue;
            }
            if(isset($_POST['subjectCode' . $i])){
                $subjectCode = implode(',',$_POST['subjectCode'. $i]);
            }
            else{
                $subjectCode = '';
            }
            if(isset($_POST['subject' . $i])){
                $qualifiedIn = implode(',',$_POST['subject'. $i]);
            }
            else{
                $qualifiedIn = '';
            }
            $type = $_POST['type'. $i];
            $staff_id = generateRandomString(8);
            $unique = false;
            while($unique != true){
                $sql = 'SELECT * FROM staff WHERE staff_id = "' . $staff_id . '"';
                $result = $conn->query($sql);
                if($result->num_rows == 0){
                    $unique = true;
                }
                else{
                    $staff_id = generateRandomString(8);
                }
            }
            $pCode = generateRandomString(16);
            $login = md5(generateRandomString(12));
            $stmt->execute();
            if($stmt->error){
                $statusMessage .= '<span class="text-danger">' . $stmt->error . '</span><br>';
            }
            else{
                $successCount += 1;
                sendMail($schoolName,$email,$name,'edConnect Account Creation','To setup your edConnect Staff account simply go to <a>edconnect.ie/setPassword.php?p_code=' . $pCode . '&a_type=staff</a>');
            }
            
            $i++;
        }
        $statusMessage .= '<span class="text-success">' . $successCount . ' staff members successfully added</span>';

    }

    if(isset($_POST['updateName'])){
        if(isset($_POST['currentName'])){
            $stmt = $conn->prepare('UPDATE staff SET name = ?, email = ?, subjectCode = ?, qualified_in = ?,type = ? WHERE name = ? AND email = ?');
            $stmt->bind_param('sssssss',$_POST['updateName'],$_POST['updateEmail'],$subjectCodes,$qualified,$_POST['updatePrivilige'],$_POST['currentName'],$_POST['currentEmail']);
            if(isset($_POST['updateSubject'])){
                $subjectCodes = implode(',',$_POST['updateSubject']);
            }
            else{
                $subjectCodes = '';
            }
            if(isset($_POST['updateQualified'])){
                $qualified = implode(',',$_POST['updateQualified']);
            }
            else{
                $qualified = '';
            }
            $stmt->execute();
            if($stmt->error){
                $updateMessage = '<span class="text-danger col-12">' . $stmt->error . '</span><br>';
            }
            else{
                $updateMessage = '<span class="text-success">Staff Member successfully updated</span>';
            }

        }
        else if(isset($_POST['deleteName'])){
            $stmt = $conn->prepare('DELETE from staff WHERE name = ? AND email = ?');
            $stmt->bind_param('ss',$_POST['deleteName'],$_POST['deleteEmail']);
            $stmt->execute();
            if(!$stmt->error){
                $updateMessage = '<span class="text-success col-12"> Staff member has been successfully removed</span>';
            }
        }
    }

?>
<!DOCTYPE html>
<head>
    <title>edConnect | manage staff</title>
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
        <h3>Add Staff</h3>
    </div>
    <div class='row justify-content-lg-start justify-content-center text-center text-lg-start mt-2'>
        <span class='col-12 col-lg-3'>add rows: <input type='number' value='1' min='1' max='20' id='num-of-rows'><button type='button' class='btn btn-primary ml-2' onclick='addStaffRows(document.getElementById("num-of-rows").value)'>Add</button></span>
        <?php echo (isset($statusMessage))? '<span class="col-4">' . $statusMessage . '</span>':'' ;?>
    </div>
    <form action='manageStaff.php' method='post'>
    <div class='row justify-content-center text-center addStaffContainer'>
    <span class='col-12 col-lg-2 mt-2 mnt-lg-0'>Name: <input type='text' name='name0'></span>
    <span class='col-12 col-lg-2 mt-2 mnt-lg-0'>Email: <input type='email' name='email0'></span>
    <span class='col-6 col-lg-2 mt-2 mnt-lg-0'>Subjects:
    <ul class='vertical-list' id='vertical-list'>
        <?php 
            $sql = 'SELECT subjectCode FROM timetable GROUP BY subjectCode';
            $result = $conn->query($sql);
            $subjectList = [];
            while($row = $result->fetch_assoc()){
                array_push($subjectList, $row['subjectCode']);
            }
            $sql = 'SELECT subjectCode FROM staff';
            $result=$conn->query($sql);
            $assigned = [];
            while($row = $result->fetch_assoc()){
                $staffSubjects = explode(',',$row['subjectCode']);
                foreach($staffSubjects as $subject){
                    if(!in_array($subject,$assigned)){
                        if($subject != ''){
                            array_push($assigned,$subject);
                        }
                    }
                }
            }
            $unassigned = array_diff($subjectList,$assigned);
            if(sizeof($unassigned) > 0){
                echo '<li><b>unassigned</b></li>';
                foreach($unassigned as $subject){
                    echo '<li><input type="checkbox" onchange="staffSubjectSelect(event.target)" name="subjectCode0[]" value="'  . $subject . '">' . $subject . '</li>';
                }
            }
            if(sizeof($assigned) > 0){
                echo '<li><b>assigned</b></li>';
                foreach($assigned as $subject){
                    echo '<li><input type="checkbox" onchange="staffSubjectSelect(event.target)" name="subjectCode0[]" value="'  . $subject . '">' . $subject . '</li>';
                }
            }
        ?>
    </ul></span>
    <span class='col-6 col-lg-3 mt-2 mt-lg-0'>Qualified in: <ul class='vertical-list'>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='English'>English</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Maths'>Maths</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Irish'>Irish</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='C.S.P.E'>C.S.P.E</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Science'>Science</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='French'>French</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Spanish'>Spanish</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='German'>German</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Italian'>Italian</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='History'>History</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Geography'>Geography</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Business'>Business</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Technology'>Technology</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Metal Work'>Metal Work</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Wood Work'>Wood Work</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Technical Graphics'>Technical Graphics</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Art'>Art</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Music'>Music</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Home Economics'>Home Economics</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Religion'>Religion</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Ancient Greek'>Ancient Greek</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Latin'>Latin</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Classical Studied'>Classical Studied</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='LCVP'>LCVP</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Physical Education'>Physical Education</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Arabic'>Arabic</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Russian'>Russian</li>
        <li><input type='checkbox' onchange='staffSubjectSelect(event.target)' name='subject0[]' value='Japanese'>Japanese</li>
    </ul></span>
    <span class='col-12 col-lg-2 mt-2 mt-lg-0'>Priviliges:
    <select name='type0'>
            <option selected>standard</option>
            <option>higher</option>
    </select></span>
    </div>
    <div class='row justify-content-lg-end justify-content-center mt-2 mt-lg-0'>
        <button class='btn btn-primary'>Submit</button>
    </div>
    </form>
    <form action='manageStaff.php' method='post' id='currentStaffForm'>
        <div class='row justify-content-center mt-4 text-center'>
            <h3 class='col-12 text-center'>Current Staff</h3>
            <?php echo (isset($updateMessage))?$updateMessage:'';?>
            <div class='col-12 col-lg-10 table-responsive'>    
                <table class='table'>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subjects</th>
                        <th>Qualified in</th>
                        <th>Priviliges</th>
                    </tr>
                    <?php
                        $sql = 'SELECT * FROM staff';
                        $result = $conn->query($sql);
                        while($row=$result->fetch_assoc()){
                            echo '<tr>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>' .$row['subjectCode'] . '</td>';
                            echo '<td>' . $row['qualified_in'] . '</td>';
                            echo '<td>' . $row['type'] . '</td>';
                            echo '<td><button type="button" onclick="editCurrentStaff(event.target)" class="editBtn">Edit</button></td>';
                            echo '</tr>';
                        }
                    ?>
                </table>
            </div>
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Remove Staff Member</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelDeleteStaff()">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to remove this staff members ability to access edconnect?<br>
                        <b>Remove:</b> <span id='toRemoveName'></span>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="cancelDeleteStaff()">Cancel</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
</html>

<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    require('info.php');
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }

    if(isset($_POST['applId'])){
        $sql = 'UPDATE applications SET status = "' . $_POST['applStatus'] . '" , processed_by ="' . $_SESSION['userId'] . '" WHERE application_id = "' . $_POST['applId'] . '"';
        $conn->query($sql);
        if($conn->affected_rows == 1 && $_POST['applStatus'] != 'rejected'){
            // Create student account
            $sql = 'SELECT * FROM applications WHERE application_id="' . $_POST['applId'] . '"';
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $stmt = $conn->prepare('INSERT INTO students(name,student_id,year,address,login_id) VALUES (?,?,?,?,?)');
            $stmt->bind_param('sssss',$row['student_name'],$studentId,$year,$address,$loginId);
            $year = 1;
            $address = implode(',',[$row['parent_addr_1'],$row['parent_addr_2'],$row['parent_county']]);
            $studentId = generateRandomString(8);
            $loginId = generateRandomString(16);
            $stmt->execute();
            if($stmt->error){
                $statusMessage = '<span class="text-danger col-12">There was an error creating the students account</span>';
            }
            else{
                $statusMessage = '<span class="text-success col-12">Student account has been created</span>';
            }
            // create first parent/guardian account
            $parentSql = 'SELECT student_id FROM parents WHERE email = "' . $row['parent_email'] . '"';
            $parentResult = $conn->query($parentSql);
            if($parentResult->num_rows == 1){
                $stmt = $conn->prepare('UPDATE parents SET student_id = ? WHERE email = ?');
                $studentId = implode(',',array_push(explode(',',$parentResult['student_id']),$studentId));
                $stmt->bind_param('ss',$studentId,$row['parent_email']);
            }
            else{
                $stmt = $conn->prepare('INSERT INTO parents(name,student_id,address,email,login_id,phone,parent_id,p_code) VALUES (?,?,?,?,?,?,?,?)');
                $stmt->bind_param('ssssssss',$row['parent_name'],$studentId,$address,$row['parent_email'],$loginId,$row['parent_number'],$parentId,$pCode);
            }
            $parentId = generateRandomString(8);
            $loginId = generateRandomString(16);
            $pCode = generateRandomString(16);
            $stmt->execute();
            if($stmt->error){
                $statusMessage .= '<span class="text-danger col-12">There was an error creating the parent/guardian account</span>';
            }
            else{
                $statusMessage .= '<span class="text-success col-12">Parent/Guardian account has been created</span>';
                sendMail($schoolName,$row['parent_email'],$row['parent_name'],'edConnect Account Creation','To setup your edConnect parent/guardian account simply go to <a>edconnect.ie/setPassword.php?p_code=' . $pCode . '&a_type=parents</a>');
            }
            if($row['second_parent_name'] != ''){
                // create second parent/guardian account
                $parentSql = 'SELECT student_id FROM parents WHERE email = "' . $row['second_parent_email'] . '"';
                $parentResult = $conn->query($parentSql);
                if($parentResult->num_rows == 1){
                    $stmt = $conn->prepare('UPDATE parents SET student_id = ? WHERE email = ?');
                    $studentId = implode(',',array_push(explode(',',$parentResult['student_id']),$studentId));
                    $stmt->bind_param('ss',$studentId,$row['second_parent_email']);
                }
                else{
                    $stmt = $conn->prepare('INSERT INTO parents(name,student_id,address,email,login_id,phone,parent_id) VALUES (?,?,?,?,?,?,?,?)');
                    $stmt->bind_param('ssssssss',$row['second_parent_name'],$studentId,$address,$row['second_parent_email'],$loginId,$row['second_parent_number'],$parentId,$pCode);
                }
                $parentId = generateRandomString(8);
                $loginId = generateRandomString(16);
                $address = implode(',',[$row['second_parent_addr_1'],$row['second_parent_addr_2'],$row['second_parent_county']]);
                $pCode = generateRandomString(16);
                $stmt->execute();
                if($stmt->error){
                    echo $stmt->error;
                    $statusMessage .= '<span class="text-danger col-12">There was an error creating the parent/guardian account</span>';
                }
                else{
                    $statusMessage .= '<span class="text-success col-12">Parent/Guardian account has been created</span>';
                    sendMail($schoolName,$row['second_parent_email'],$row['second_parent_name'],'edConnect Account Creation','To setup your edConnect parent/guardian account simply go to <a>edconnect.ie/setPassword.php?p_code=' . $pCode . '&a_type=parents</a>');
                }
            }
            
        }
    }
?>

<!DOCTYPE html>
<head>
    <title>
        edConnect | student approval
    </title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
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
<div class="container">
    <div class="row justify-content-center text-center">
        <h3 class='col-12 mt-3'>Required Approval List</h3>
        <?php echo (isset($statusMessage))?$statusMessage:'';?>
        <table class='col-10 col-lg-6 mt-2'>
            <tr>
                <th>Name</th>
                <th>Address</th>
            </tr>
            <?php
                $sql = "SELECT student_name, parent_addr_1,parent_addr_2,parent_county,application_id FROM applications WHERE status != 'accepted' AND status != 'rejected'";
                $result=$conn->query($sql);
                if($result->num_rows == 0){
                    echo '<tr><td colspan="4">No pending approvals</td></tr>';
                }
                else{
                    while($row = $result->fetch_assoc()){
                        echo '<tr>';
                        echo '<td>' . $row['student_name'] . '</td>';
                        echo '<td>' . implode(', ',[$row['parent_addr_1'],$row['parent_addr_2'],$row['parent_county']]) . '</td>';
                        echo '<td><button type="button" onclick="acceptApplication(event.target,\'' . $row['application_id'] . '\')" class="text-success">Accept</button>';
                        echo '<td><button type="button" onclick="rejectApplication(event.target,\'' . $row['application_id'] . '\')" class="text-danger">Reject</button>';
                        echo '</tr>';
                    }
                }
            ?>
        </table>
    </div>
</div>
</body>
</html>
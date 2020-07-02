<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }

    if(isset($_POST['actionableSupervisorId'])){
        $sql = 'SELECT * FROM supervision WHERE staff_id="' . $_POST['actionableSupervisorId'] . '" and date="' . $_POST['date'] . '"'; 
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()){
            if($row['type'] == $_POST['type']){
                $errorMsg = 'Staff member has already been assigned to this supervision';
                break;

            } 
            else if(($row['type'] == 'Lunch Time Detention' && $_POST['type'] == 'Lunch Corridor') || ($row['type'] == 'Lunch Corridor' && $_POST['type'] == 'Lunch Time Detention')){
                $errorMsg = 'Staff member has already been assigned to a clashing supervision';
                break;
            }
        }
        if(!isset($errorMsg)){
            $sql = 'UPDATE supervision SET removed = 1 WHERE date = "' . $_POST['date'] . '" AND type="' . $_POST['type'] . '"';
            $result = $conn->query($sql);

            $stmt = $conn->prepare("INSERT INTO supervision (staff_id,assigned_by,assigned_at,date,type,removed) VALUES(?, ?, ?, ? , ?, ?)");
            $stmt->bind_param("sssssi",$_POST['actionableSupervisorId'],$_SESSION['userId'],$dateTime,$_POST['date'],$_POST['type'],$removed);
            $dateTime = date("Y-m-d h:i:sa");
            $removed = 0;
            $stmt->execute();
        }
    }
?>
<!DOCTYPE html>
<head>
    <title>edConnect | supervision</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
</head>
<body>
<?php
require('navbar.php');
?>
<div class='row ml-0 mr-0 col-12 third-nav ml-0 mr-0'>
    <ul class='nav'>
        <li class="third-nav-item"><a href='actionables.php'>Supervision</a></li>
        <li class="third-nav-item"><a href='detentions.php'>Detentions</a></li>
        <li class="third-nav-item"><a href='manageStaff.php'>Manage Staff</a></li>
        <li class="third-nav-item"><a href='timetableManagement.php'>Manage Timetable</a></li>
        <li class="third-nav-item"><a href='studentApproval.php'>Student Approval</a></li>
        <li class="third-nav-item"><a href='studentManagement.php'>Manage Students</a></li>
    </ul>
</div>
<div class='row justify-content-center mt-3 ml-0 mr-0'>
    <h3>Add/Update Supervision</h3>
</div>
<form action='actionables.php' method='post'>
    <div class='row justify-content-center mt-3 ml-0 mr-0'>
        <span class='col-12 col-lg-3  text-center'>
            <span id='supervisorSearch'>Search Staff:
                <input type="text"  onkeyup="showResult(this.value,'supervisorsearch',false,true)" placeholder='search'>
                <div id="supervisorsearch"></div>
            </span>
            <div id='supervisors'>
            </div>
        </span>
        <span class='col-12 col-lg-2 text-center mt-2 mt-lg-0'>Date:<input type='date' name='date' required></span>
        <span class='col-12 col-lg-2 mt-2 mt-lg-0 text-center'>Type:
            <select name='type'>
                <option>Lunch Time Detention</option>
                <option>Break Corridor</option>
                <option>Lunch Corridor</option>
                <option>After School Detention</option>
            </select>
        </span>
        <span class='col-1 mt-2 mt-lg-0'><button class='btn btn-primary'>Add</button></span>
    </div>
</form>
<?php
    if(isset($errorMsg)){
        echo "<div class='row justify-content-center text-warning'>
            " . $errorMsg . "
        </div>";
    }
?>
<div class='row justify-content-center mt-5 ml-0 mr-0'>
    <h3>Upcoming Supervisors</h3>
</div>
<div class='row justify-content-center ml-0 mr-0 text-center'>
    <table class='col-10 col-lg-6'>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Date</th>
        </tr>
        <?php
            $sql = 'SELECT st.name,su.date,su.type FROM supervision as su LEFT JOIN staff as st ON su.staff_id = st.staff_id WHERE date >= CURDATE() AND removed=0 ORDER BY date ASC';
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()){
                echo '<tr>';
                echo '<td>' . $row['name'] . '</td><td>' . $row['type'] . '</td><td>' . $row['date'] . '</td>';
                echo '</tr>';
            }
        ?>
    </table>
</div>


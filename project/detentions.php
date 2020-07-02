<?php
    session_start();
    require('connection.php');
    require('common-functions.php');
    
    
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }

    if(isset($_POST['detention_type'])){
        $dType = $_POST['detention_type'];
        $date = $_POST['date'];
    }
?>
<!DOCTYPE html>
<head>
    <title>edConnect | detentions</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <script src='scripts.js?<?php echo time(); ?>'></script>
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
        <li class="third-nav-item"><a href='studentmanagement.php'>Manage Students</a></li>
    </ul>
</div>
<div class='row justify-content-center ml-0 mr-0 mt-3'>
   <h3> Detention List</h3>
</div>
<form action='detentions.php' method='post'>
    <div class='row justify-content-center ml-0 mr-0 mt-3'>
        <span>Type:
            <select name='detention_type' onchange='form.submit()'>
                <option <?php echo (isset($dType) && $dType == 'All')?' selected':'';?> >All</option>
                <option <?php echo (isset($dType) && $dType == 'Lunch Time')?' selected':'';?>>Lunch Time</option>
                <option <?php echo (isset($dType) && $dType == 'After School')?' selected':'';?>>After School</option>
            </select>
        </span>
        <span>Date:
            <select name='date' onchange='form.submit()'>
                <?php
                    $sql = 'SELECT date FROM detention GROUP BY DATE ORDER BY DATE DESC';
                    $result = $conn->query($sql);
                    while($row=$result->fetch_assoc()){
                        if(!isset($date)){
                            $date = $row['date'];
                            echo '<option selected>' . $row['date'] . '</option>';
                        }
                        else if(isset($date) && $date == $row['date']){
                            echo '<option selected>' . $row['date'] . '</option>';
                        }
                        else{
                            echo '<option>' . $row['date'] . '</option>';
                        }
                    }
                ?>
            </select>
        </span>
    </div>
</form>
<div class='row justify-content-center mt-3 ml-0 mr-0'>
    <div class='col-12 col-lg-6 text-center table-responsive'>
        <table class='table col-12'>
            <tr>
                <th>Name</th>
                <th>type</th>
                <th>Reason</th>
                <th>Date</th>
                <th>Attended</th>
            </tr>
        <?php
            if(isset($dType) && $dType == 'Lunch Time'){
                $filter = ' AND detention_type = "Lunch Time"';
            }
            else if(isset($dType) && $dType == 'After School'){
                $filter = ' AND detention_TYPE = "After School"';
            }
            else{
                $filter = '';
            }
            $sql = 'SELECT * FROM detention LEFT JOIN students ON detention.student_id = students.student_id WHERE date="' . $date .'"' . $filter;
            $result = $conn->query($sql);
            while($row=$result->fetch_assoc()){
                echo '<tr>';
                echo '<td>' . $row['name'] . '</td><td>' . $row['detention_type'] . '</td><td>' . $row['reason'] . '</td><td>' . $row['date'] . '</td><td>' . $row['status'] . '</td>';
                echo '</tr>'; 
            }
        ?>
        </table>
    </div>
</div>



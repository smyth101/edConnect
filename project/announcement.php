<?php
    session_start();
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }
    require('connection.php');
    require('common-functions.php');
    
    if(isset($_POST['message'])){
        if($_POST['announceType'] == 'ASAP'){
            $time = ' ' . date("h:i:sa");
        }
        else{
            $time = ' 00:00:00';
        }
        $stmt = $conn->prepare("INSERT INTO announcements (staff_id,date,message,type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $_SESSION['userId'],$date,$_POST['message'],$_POST['announceType']);
        $date = $_POST['date'] . $time;
        $stmt->execute();
    }
?>

<!DOCTYPE html>
<head>
    <title>
        edConnect | Announcements
    </title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <script src='scripts.js?<?php echo time(); ?>'></script>
</head>
<body>
<?php
require('navbar.php');
?>
<form method='post' action='announcement.php'>
<div class="row justify-content-center mt-3 lm-0 mr-0"><h3>New Announcement</h3></div>
<div class="row justify-content-center lm-0 mr-0 mt-lg-3 mt-0"><span class='col-12 col-lg-3 text-center mt-3 mt-lg-0'>Message: <textarea name='message'></textarea></span><span class='col-12 col-lg-3 text-center mt-3 mt-lg-0'> Type:<select name='announceType' onchange='changeType()'><option>10 am announcement</option><option>ASAP</option></select></span><span class='col-12 col-lg-3 text-center mt-3 mt-lg-0'>Date:<input name='date' type='date'></span><span class='col-12 col-lg-3 text-center text-lg-left mt-2 mt-lg-0'><button class="btn btn-primary">Submit</button></span>
</form></div>
<div class="row justify-content-center mt-3 text-center">
<h3>Upcoming/Todays Announcements</h3>
</div>
<div class="row justify-content-center">
    <div class="table-responsive col-lg-6 col-10">
        <table class='text-center col-12'>
            <tr>
            <th class="text-center">Message</th><th class="text-center">From</th><th class="text-center">Date</th><th class="text-center">Type</th>
            </tr>
            <?php
            $sql = 'SELECT announcements.*,staff.name FROM announcements LEFT JOIN staff ON staff.staff_id =  announcements.staff_id WHERE DATE(announcements.date) = DATE(NOW()) ORDER BY type DESC,date DESC';
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()){
                echo '<tr><td>' . $row['message'] . '</td><td>' . $row['name'] . '</td><td>' . $row['date'] . '</td><td>' . $row['type'] . '</td></tr>';
            }
            $sql = 'SELECT announcements.*,staff.name FROM announcements LEFT JOIN staff ON staff.staff_id =  announcements.staff_id WHERE announcements.date > CURDATE() AND announcements.type = "10 am announcement" ORDER BY announcements.date ASC';
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()){
                echo '<tr><td>' . $row['message'] . '</td><td>' . $row['name'] . '</td><td>' . $row['date'] . '</td><td>' . $row['type'] . '</td></tr>';
            } 
            ?>
        </table>
    </div>
</div>
<script>
    document.getElementsByName('date')[0].value = formatDate()

    function changeType(){
        type = document.getElementsByName('announceType')[0].value
        if(type == 'ASAP'){
            document.getElementsByName('date')[0].style.display = 'none'
        }
        else{
            document.getElementsByName('date')[0].style.display = 'inline-block'
        }

    }
</script>
</body>
</html>
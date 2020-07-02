<?php 
	require("../connection.php");

	$json=file_get_contents("teststatus.json");
	$json_data=json_decode($json,true);
	
	$status=$json_data["status"];
	$time = explode(':',$json_data["time"]);
	$d = mktime($time[0],$time[1]);
	$x = date("H:i", $d);
	$newtimestamp = strtotime($x .  ' + 20 minute');
	$timeout = date('H:i',$newtimestamp);
	
	if(isset($_POST["testlink"]) || ($status=="blocked" && date('H:i') >= $timeout && isset($_POST["start"]))){
		$sql="DROP TABLE activities";
		$conn->query($sql);
		$sql="DROP TABLE announcements";
		$conn->query($sql);
		$sql="DROP TABLE attendance";
		$conn->query($sql);
		$sql="DROP TABLE classactivities";
		$conn->query($sql);
		$sql="DROP TABLE detention";
		$conn->query($sql);
		$sql="DROP TABLE grades";
		$conn->query($sql);
		$sql="DROP TABLE journal";
		$conn->query($sql);
		$sql="DROP TABLE notes";
		$conn->query($sql);
		$sql="DROP TABLE parents";
		$conn->query($sql);
		$sql="DROP TABLE staff";
		$conn->query($sql);
		$sql="DROP TABLE students";
		$conn->query($sql);
		$sql="DROP TABLE supervision";
		$conn->query($sql);
		$sql="DROP TABLE timetable";
		$conn->query($sql);
		$sql="DROP TABLE applications";
		$conn->query($sql);
		
		$myfile = fopen("teststatus.json", "w");
		$txt = '{"status":"unblocked","time":"' . date('H:i') . '"}' ;
		fwrite($myfile, $txt);
		fclose($myfile);

		$query = '';
		$sqlScript = file('database.sql');
		foreach ($sqlScript as $line)	{
			
			$startWith = substr(trim($line), 0 ,2);
			$endWith = substr(trim($line), -1 ,1);
			
			if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
				continue;
			}
				
			$query = $query . $line;
			if ($endWith == ';') {
				mysqli_query($conn,$query) or die('<div class="error-response sql-import-response">Problem in executing the SQL query <b>' . $query. '</b></div>');
				$query= '';		
			}
		}

		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk1 = new MongoDB\Driver\BulkWrite;
		$bulk2 = new MongoDB\Driver\BulkWrite;
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
		$bulk->delete([]);
		$manager->executeBulkWrite('chat.token', $bulk);
		$bulk1->delete([]);
		$manager->executeBulkWrite('chat.message', $bulk1);
		$bulk2->delete([]);
		$manager->executeBulkWrite('chat.conversation', $bulk2);
		
		$filename = 'conversation.json';
		$lines = file($filename, FILE_IGNORE_NEW_LINES);
		$input = '';
		foreach($lines as $line){
		  $input .= $line;
		}
		$bulk3 = new MongoDB\Driver\BulkWrite;
		$input = json_decode($input);
		foreach ($input as $object) {
			$bson = MongoDB\BSON\fromJSON(json_encode($object));
			$document = MongoDB\BSON\toPHP($bson);
			$bulk3->insert($document);
		}
		$manager->executeBulkWrite('chat.conversation', $bulk3);
		
		$filename = 'message.json';
		$lines = file($filename, FILE_IGNORE_NEW_LINES);
		$input = '';
		foreach($lines as $line){
		  $input .= $line;
		}
		$bulk4 = new MongoDB\Driver\BulkWrite;
		$input = json_decode($input);
		foreach ($input as $object) {
			$bson = MongoDB\BSON\fromJSON(json_encode($object));
			$document = MongoDB\BSON\toPHP($bson);
			$bulk4->insert($document);
		}
		$manager->executeBulkWrite('chat.message', $bulk4);

		$filename = 'token.json';
		$lines = file($filename, FILE_IGNORE_NEW_LINES);
		$input = '';
		foreach($lines as $line){
		  $input .= $line;
		}
		$bulk5 = new MongoDB\Driver\BulkWrite;
		$input = json_decode($input);
		foreach ($input as $object) {
			$bson = MongoDB\BSON\fromJSON(json_encode($object));
			$document = MongoDB\BSON\toPHP($bson);
			$bulk5->insert($document);
		}
		$manager->executeBulkWrite('chat.token', $bulk5);

		
		exec('cp ../conversation_files/.gitignore conversation_files/');
		exec('rm -rfv ../conversation_files/*');
		exec('cp -R conversation_files/. ../conversation_files');
		
		exec('cp ../student_profile/.gitignore student_profile/');
		exec(' rm -rfv ../student_profile/*');
		exec('cp -R student_profile/. ../student_profile');

		if(isset($_POST["testlink"])){
			header('location:' . $_POST['testlink']);
		}
	}
	
	$status=$json_data["status"];
	$time = explode(':',$json_data["time"]);
	$d = mktime($time[0],$time[1]);
	$x = date("H:i", $d);
	$newtimestamp = strtotime($x .  ' + 20 minute');
	$timeout = date('H:i',$newtimestamp);

	if($status=="unblocked" || date('H:i') >= $timeout){
		if(isset($_POST["start"])){
			$myfile = fopen("teststatus.json", "w");
			$txt = '{"status":"blocked","time":"' . date('H:i') . '"}' ;
			fwrite($myfile, $txt);
			fclose($myfile);
			$start=true;
		}
	}
	else{
		$blocked=true;		
	}

	if(isset($start)){
		$usrNum = rand(10,100);
		$passwrodNum = rand(10,100);
		$sql = 'UPDATE students SET login_id = "test' . $usrNum . '" , password = "' . md5('password' . $passwrodNum) . '" WHERE student_id = "CVPX4Dqh"';
		$conn->query($sql);
			
		$sql = 'UPDATE parents SET login_id = "test' . $usrNum . '" , password = "' . md5('password' . $passwrodNum) . '" WHERE parent_id = "psXXTIMt"';
		$conn->query($sql);

		$sql = 'UPDATE staff SET login_id = "test' . $usrNum . '" , password = "' . md5('password' . $passwrodNum) . '" WHERE staff_id = "d7wLlX3D"';
		$conn->query($sql);
	}

?>


<!DOCTYPE html>
<html lang="en">
    <head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<link rel="stylesheet" href="../style.css?<?php echo time()?>">
		
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

		<script>
			$(document).ready(function() {
			$("input[name$='usertype']").click(function() {
				var test = $(this).val();

				$("div.desc").hide();
				$("#usertype" + test).show();
			});
			});
		</script>
		
		<title>
			edConnect | testing page
		</title>
	</head>
	
	<body> 	
		<div id="main-nav" class='row justify-content-center'>
			<h2><b> EdConnect Testing Page </b></h2>
		</div>		
		
		<div id="sub-nav" class="row justify-content-center">
			Please start the trial, Select your account type and carry out the list of tasks which are displayed to you and then fill out the questionnaire at the end.
		</div>
		
		<div id="sub-nav" class="row justify-content-center mb-3">
			<?php echo (!isset($start))?'Click the button below to begin.':''; ?>
		</div>
			
		<div id="usertype">
		
		<?php 
			if(isset($blocked)){
				echo "<span class='row justify-content-center'>Someone is currently carrying out the test. Please try again at ".$timeout.". Thank you.</span></div></body></html>";
				exit();
			}
		?>
		<form method="post" action="test.php"><span class="row justify-content-center mb-3"><span class='col-12 text-center'><input type="checkbox" onclick="if(this.checked == true){document.getElementsByName('start')[0].disabled = false}else{document.getElementsByName('start')[0].disabled = true}"> I confirm that all information entered during this test will be fictional, any files uploaded are publicly available. </span> <?php echo (!isset($start))?'<button name="start" class="btn btn-primary" onclick="this.innerHTML = \'Trial Starting\'" disabled>Start Trial</button>':'<b>Trial Started!<br>username: test' . $usrNum . '<br>password: password' . $passwrodNum . '<br>We reccommend you open a new tab to sign in to edconnect.ie</b>';?> </span></form>
		
		<p>Please select which user type you are and the corresponding list of tasks will be displayed to you:</p>
			<input type="radio" name="usertype" checked="checked" value="1"  />Staff<br>
			<input type="radio" name="usertype" value="2" />Student<br>
			<input type="radio" name="usertype" value="3" />Parent/Guardian<br>
		<p>please ensure that all information uploaded is 100% fictional or public information</p>
			<div id="usertype1" class="desc">
				<br>
				<h4>Staff User Tasks</h4>
				<ol id="testlist">
					<li>Sign in.</li>
					<li>View your timetable.</li>
					<li>Take attendance for your class.</li>
					<li>Edit the attendance list.</li>
					<li>View the attendance analytics.</li>
					<li>Assign your class homework.</li>
					<li>Submit grades for a class test.</li>
					<li>Edit the grades list.</li>
					<li>View the grades analytics.</li>
					<li>Schedule an activity.</li>
					<li>View your messages.</li>
					<li>Send a message.</li>
					<li>Create a group chat.</li>
					<li>Add a person to the group chat.</li>
					<li>Remove someone from the group chat.</li>
					<li>Submit a 10am announcement.</li>
					<li>Search for a student user. (Ciara Carter)</li>
					<li>Assign the student a detention.</li>
					<li>Sign out.</li>
				</ol><br>
				<p> Once you have carried out all the tasks, please fill out the <b>STAFF USER</b> questionnaire by clicking the button below:<form method="post" action="test.php"><div class="row justify-content-center"><button class="btn btn-primary" name="testlink" type="submit" value="https://forms.gle/TQsx8U1uThkQHry46">finished</button></div> </form></p>
				<br>
			</div>
			
			<div id="usertype2" class="desc" style="display: none;">
				<br>
				<h4>Student User Tasks</h4>
				<ol id="testlist">
					<li>Sign in.</li>
					<li>View your timetable.</li>
					<li>View your past attendance.</li>
					<li>View your homework.</li>
					<li>View your past grades.</li>
					<li>View the grades analytics.</li>
					<li>View upcoming activities.</li>
					<li>View your messages.</li>
					<li>Send a message.</li>
					<li>Sign out.</li>
				</ol><br>
				<p> Once you have carried out all the tasks, please fill out the <b>STUDENT USER</b> questionnaire by clicking the button below:<form method="post" action="test.php"><div class="row justify-content-center"><button class="btn btn-primary" name="testlink" type="submit" value="https://forms.gle/f8YZs2WPvtt39JVU9">finished</button></div> </form></p>
				<br>
			</div>
			
			<div id="usertype3" class="desc" style="display: none;">
				<br>
				<h4>Parent/Guardian User Tasks</h4>
				<ol id="testlist">
					<li>Sign in.</li>
					<li>View your child's timetable.</li>
					<li>View your child's past attendance.</li>
					<li>View your child's homework.</li>
					<li>View your child's past grades.</li>
					<li>View the grades analytics.</li>
					<li>View your child's upcoming activities.</li>
					<li>Give your child permission to attend an upcoming activity.</li>
					<li>View your messages.</li>
					<li>Send a message.</li>
					<li>Sign out.</li>
				</ol><br>
				<p> Once you have carried out all the tasks, please fill out the <b>PARENT/GUARDIAN USER</b> questionnaire by clicking the button below:<form method="post" action="test.php"><div class="row justify-content-center"><button class="btn btn-primary" name="testlink" type="submit" value="https://forms.gle/Ns6Q5cUSUNHueVd17">finished</button></div> </form></p>		
				<br>
			</div>
			
			<!-- <?php
				if(isset($_POST["testlink"])){
					echo "<script>window.open('".$_POST['testlink']."', '_self')</script>";
				}
			?> -->
	
	</body>
</html>

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
		exec(' rm conversation_files/.gitignore');
		
		exec('cp ../student_profile/.gitignore student_profile/');
		exec(' rm -rfv ../student_profile/*');
		exec('cp -R student_profile/. ../student_profile');
		exec(' rm student_profile/.gitignore');

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
			edConnect | Demo
		</title>
	</head>
	
	<body> 	
		<div id="main-nav" class='row justify-content-center ml-0 mr-0 mb-5'>
			<h2 class='m-3'><b> EdConnect Demo Page </b></h2>
		</div>		
		

		

			
		<div id="usertype">
		
		<?php 
			if(isset($blocked)){
				echo "<span class='row justify-content-center ml-0 mr-0'>Someone is currently carrying out the demo. Please try again at ".$timeout.". Thank you.</span></div></body></html>";
				exit();
			}
		?>

        <p>Welcome to Edconnect demo. Once you start your demo you will be able to use the application as a student, teacher and parent/guardian. Once you start the demo you will have 20 minutes to get demo the application. The credentials that you are given when you sign in are used for all 3 account types in this demo.</p>
        <p>Edconnect is available on your browser or as an Android or Windows application.</p>
        <h3 class='text-center mt-3'>Edconnect for browser</h3>
        <p>To access the app on your browser simply go to <a href='edconnect.ie'>edconnect.ie</a></p>
        <h3 class='text-center mt-3'>Edconnect for Android</h3>
        <p>To access edConnect on your android device download the application <a href='../mobile/app-release.apk' download>here</a>. 
        <a href='../mobile/app-release.apk' download><img src='images/android.png' class='ml-5'></a></p>
        <br><br>If you are having trouble installing edConnect please consult this step by step guide <a href='https://www.lifewire.com/install-apk-on-android-4177185'>https://www.lifewire.com/install-apk-on-android-4177185</a>
        <h3 class='text-center mt-3'>Edconnect for Windows</h3>
        <p>To access edConnect on your windows device download the application <a href='../desktop/edconnectSetup1.0.0.exe' download>here</a>.
        <a href='../desktop/edconnectSetup1.0.0.exe' download><img src='images/windows.png' class='ml-5' width='300px'></a></p></p>
        <p>As the windows application does not have a signed certificate the edconnect installer may be flagged as harmful, to carry on with the installation click keep in your browser tray where the file was downloaded. if windows ptotection pops up click on more info and then click on run anyway.</p> 
        <form method="post" action="demo.php"><span class="row justify-content-center mb-3ml- mr-0"><span class='col-12 text-center'><input type="checkbox" onclick="if(this.checked == true){document.getElementsByName('start')[0].disabled = false}else{document.getElementsByName('start')[0].disabled = true}"> I confirm that all information entered during this demo will be fictional, any files uploaded are publicly available. </span> <?php echo (!isset($start))?'<button name="start" class="btn btn-primary" onclick="this.innerHTML = \'Demo Starting\'" disabled>Start Demo</button>':'<b>Demo Started!<br>username: test' . $usrNum . '<br>password: password' . $passwrodNum . '</b>';?> </span></form>
		
	
	</body>
</html>

    
    

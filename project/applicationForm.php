<?php 
    require('info.php');
    require('connection.php');
    require('common-functions.php');
    if(isset($_POST['studentName'])){
        if(($_POST['parentEmail'][0] != $_POST['parentEmailConfirm'][0]) || (sizeof($_POST['parentEmail']) > 1 && $_POST['parentEmail'][1] != $_POST['parentEmailConfirm'][1])){
            $errorMessage = 'Emails entered did not match';
        }
        else{
            if(sizeof($_POST['parentName']) > 1){
                $stmt = $conn->prepare('INSERT INTO applications (student_name,student_dob,primary_school,parent_name,parent_email,parent_number,parent_addr_1,parent_addr_2,parent_county,second_parent_name,second_parent_email,second_parent_number,second_parent_addr_1,second_parent_addr_2,second_parent_county) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
                $stmt->bind_param('sssssssssssssss', $_POST['studentName'], $_POST['dob'], $_POST['primarySchool'], $_POST['parentName'][0], $_POST['parentEmail'][0], $_POST['parentNumber'][0], $_POST['parentAddr1'][0], $_POST['parentAddr2'][0], $_POST['parentCounty'][0], $parentName, $parent2email, $parent2number, $parent2addr1, $parent2addr2, $parent2county);
                $parentName = $_POST['parentName'][1];
                $parent2email = $_POST['parentEmail'][1];
                $parent2number = $_POST['parentNumber'][1];
                $parent2addr1 = $_POST['parentAddr1'][1];
                $parent2addr2 = $_POST['parentAddr2'][1];
                $parent2county = $_POST['parentCounty'][1];
            }
            else{
                $stmt = $conn->prepare('INSERT INTO applications (student_name,student_dob,primary_school,parent_name,parent_email,parent_number,parent_addr_1,parent_addr_2,parent_county) VALUES (?,?,?,?,?,?,?,?,?)');
                $stmt->bind_param('sssssssss', $_POST['studentName'], $_POST['dob'], $_POST['primarySchool'], $_POST['parentName'][0], $_POST['parentEmail'][0], $_POST['parentNumber'][0], $_POST['parentAddr1'][0], $_POST['parentAddr2'][0], $_POST['parentCounty'][0]);
            }
            $stmt->execute();
           if(!$conn->error){
              sendMail($schoolName,$_POST['parentEmail'][0],$_POST['parentName'][0],'Application Success','Application has been recieved, further information on entrance exams will be issued in the next few weeks');            
              $applicationSuccess = true;
           }
        }
    }
?>
<!DOCTYPE html>
<head>
    <title>Application Form</title>
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <div class='row justify-content-center ml-0 mr-0' id='main-nav'>
        <h2 class='p-3'>
            <?php echo $schoolName?>
        </h2>
    </div>
   <?php 
	if(isset($applicationSuccess)){
           echo '<span>Application successfully sent, you should receive an email shortly as confirmation</span></body></html>';
	   exit();
        }   
   ?>
    <div class='container'>
        <div class='row justify-content-center text-center'>
            <h3 class='mt-4'>2020-2021 Application</h3>
            <p  >Welcome to the Application for <?php echo $schoolName ?> for the 2020-2021 year. Please fill out the application form below and ensure that all information is correct before submitting. On submittion of application form you should recieve an email within several minutes to confirm that your application has been sent. If you did not retrieve an email after some time has passed contact the school at sampleSchool@gmail.com
            </p> 
        </div>
        <form action='applicationForm.php' method='post'>
            <div class='row justify-content-center text-center' id='applicationForm'>
                <?php 
			echo (isset($errorMessage))?'<span class="text-danger">' . $errorMessage . '</span>':'';
		?>
                <span class='col-12 mt-3 border-bottom'><h5>Student Information</h5></span>
                <span class='col-12 mt-3'>Student full name: <input type='text' name='studentName' required></span>
                <span class='col-12 mt-3'>Date of Birth: <input type='date' name='dob' required></span>
                <span class='col-12 mt-3'>Primary School attended: <input type='text' name='primarySchool' required></span>
                <span class='col-12 mt-5 border-bottom'><h5>Parent/Guardian Information</h5></span>
            </div>
            <div class='row justify-content-center text-center parentDetails'>
                <span class='col-12 mt-3'>Parent/Guardian Name: <input type='text' name='parentName[]' required></span>
                <span class='col-12 mt-3'>Contact Number: <input type='text' name='parentNumber[]' required></span>
                <span class='col-12 mt-3'>Email: <input type='email' name='parentEmail[]' required></span>
                <span class='col-12 mt-3'>Confirm Email: <input type='email' name='parentEmailConfirm[]' required></span>
                <span class='col-12 mt-3'>Address 1: <input type='text' name='parentAddr1[]' required></span>
                <span class='col-12 mt-3'>Address 2: <input type='text' name='parentAddr2[]' required></span>
                <span class='col-12 mt-3'>County: <input type='text' name='parentCounty[]' required></span>
                <span class='col-12 mt-3 secondParentBtn'><button type='button' onclick='addSecondParent()'>Add Second Parent/Guardian</button></span> 
            </div>
            <div class='row justify-content-center text-center' id='submitContainer'>
                <span class='col-12 border-top mt-4'><button class='btn btn-primary mt-2 mb-4'>Submit</button></span>
            </div>
        </form>
    </div>
<script>
    function addSecondParent(){
        parentDetails = document.getElementsByClassName('parentDetails')[0]
        secondParentDetails = parentDetails.cloneNode(true)
        secondHeader = document.createElement('span')
        secondHeader.setAttribute('class','col-12 mt-5')
        secondHeader.innerHTML = '<h5>Second Parent/Guardian Information</h5>'
        secondParentDetails.insertBefore(secondHeader,secondParentDetails.firstChild)
        clearForm = document.createElement('form')
        clearForm.appendChild(secondParentDetails)
        clearForm.reset()
        btn = document.getElementsByClassName('secondParentBtn')[0]
        btn.style.display='none';
        submitContainer = document.getElementById('submitContainer')
        formContainer = document.getElementsByTagName('form')[0]
        formContainer.insertBefore(secondParentDetails,submitContainer)
        secondBtn = document.getElementsByTagName('button')[1]
        secondBtn.innerHTML = 'Discard Second Parent/Guardian Information'
        secondBtn.setAttribute('onclick','removeSecondParent()');
    }

    function removeSecondParent(){
        form = document.getElementsByTagName('form')[0]
        form.removeChild(document.getElementsByClassName('parentDetails')[1])
        btn = document.getElementsByClassName('secondParentBtn')[0]
        btn.style.display = 'block';
    }
</script>
</body>
</html>

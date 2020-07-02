<?php
    if($_SESSION['accountType'] == 'staff' && $_SESSION['staffPrivileges'] == 'higher'){
        $actionables = "<li><a href='actionables.php'>Actionables</a></li>";
    }
    else{
        $actionables = "";
    }
?>
<header>
    <div id='main-nav' class="row ml-0 mr-0">
        <nav class="navbar navbar-expand-lg navbar-light col-12 ml-0">
        <div>
            <form action='index.php' method='post' id='home-form'><input type='hidden' name='home' value='true'><span class="navbar-brand" onclick='document.getElementById("home-form").submit()'><span style='color:red'>e<span style='color:blue'>d<span style='color:grey'>Connect</span></span>
            </form>
        </div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link d-none d-lg-block" href='index.php'>Classes</a></li>
                    <li class="nav-item dropdown d-lg-none">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Class
                        </a>
                        <div class="dropdown-menu bg-transparent border-0" aria-labelledby="navbarDropdown">
                        <?php echo "
                        <a class='dropdown-item' href='index.php'>Attendance</a>
                        <a class='dropdown-item' href='journal.php'>Journal</a>
                        <a class='dropdown-item' href='grades.php'>Grades</a>
                        ";
                        echo ($actionables != "")?"<a class='dropdown-item' href='actionables.php'>Actionables</a>":"";?>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href='chat.php'>Chat</a></li>
                    <li class="nav-item"><a class="nav-link" href='timetable.php'>Timetable</a></li>
                    <li class="nav-item"><a class="nav-link" href='activities.php'>Activities</a></li>
                    <?php echo ($_SESSION['accountType'] == 'staff')?'<li class="nav-item"><a class="nav-link" href="announcement.php">Announcement</a></li>':'';?>
                </ul>
                <?php
                if($_SESSION['accountType'] == 'parent'){
                echo "    
                    <div id ='settingsContainer' onclick='settingsDropdown(\"settingsListContainer\")' style='display:inline-block' class='mr-5 mt-3 mt-lg-0'>
                        <div id='settingBtn'><img src='images/settings_icon.png' ><span>Settings</span></div>
                            <div id='settingsListContainer' style='display:none' class='pr-5'>
                                <ul id='settingsItem'>";
                                    if($_SESSION['accountType'] == 'parent'){
                                        $sql = 'SELECT student_id FROM parents WHERE parent_id = "' . $_SESSION['accountId'] . '"';
                                        $result = $conn->query($sql);
                                        $row = $result->fetch_assoc();
                                        $students = explode(',',$row['student_id']);
                                        foreach($students as $student){
                                            $sql = 'SELECT name FROM students WHERE student_id = "' . $student . '"';
                                            $result = $conn->query($sql);
                                            $row = $result->fetch_assoc();
                                            if($student == $_SESSION['userId']){
                                                echo '<li><b>' . $row['name'] . '</b></li>';
                                            }
                                            else{
                                                echo '<form action="index.php" method="post" id="accountChange' . $student . '"><input type="hidden" name="accountChange" value="' . $student . '"><li onclick="changeAccount(\'accountChange' . $student . '\')">' . $row['name'] . '</li></form>';
                                            }
                                        }
                                    }
                                    echo "
                                    <form action='login.php' method='post' id='signoutForm'>
                                        <li onclick='signout()' id='signoutBtn'>sign out</li>
                                        <input type='hidden' name='signout'>
                                    </form>
                                </ul>
                            </div>
                    </div>
                    <span class='pb-5 d-block'></span>";
                }
                else{
                    if($_SESSION['accountType'] == 'staff'){
                        echo "
                            <span class='d-block d-lg-none'><h5 style='color:rgba(33,37,41,.5);'>Student/Staff Search</h5></span>
                            <div>
                                <input type='text' onkeyup=\"showResult(this.value,'livesearch')\" placeholder='search' class='mt-2 mr-3'>
                                <div id='livesearchContainer'>
                                    <div id='livesearch'></div>
                                </div>
                            </div>
                            <form  id='search-form' method='post' action='profile.php'>
                                <input name='search-value' type='hidden' id='search-value'>
                            </form>";
                    }
                    echo "
                    <ul id='settingsItem'>
                        <form action='login.php' method='post' id='signoutForm'>
                            <li onclick='signout()' id='signoutBtn' class='text-dark' style='font-size:150%'>sign out</li>
                            <input type='hidden' name='signout'>
                        </form>
                    </ul>";
                    
                }?>
            </div>
        </nav>
   
    </div>
<?php
    $parentFilename = explode('/',$_SERVER["SCRIPT_FILENAME"]);
    $parentFilename = $parentFilename[sizeof($parentFilename)-1];
    if($parentFilename == 'index.php' || $parentFilename == 'journal.php' || $parentFilename == 'grades.php' || $parentFilename == 'actionables.php' || $parentFilename == 'detentions.php' || $parentFilename == 'manageStaff.php' || $parentFilename == 'timetableManagement.php' || $parentFilename == 'studentApproval.php' || $parentFilename == 'studentManagement.php'){
        echo "
        <div id='sub-nav' class='row ml-0 mr-0 d-none d-lg-block'>
            <ul class='nav'>
                <li><a href='index.php'>Attendance</a></li>
                <li><a href='journal.php'>Journal</a></li>
                <li><a href='grades.php'>Grades</a></li>
                " . $actionables . "
            </ul>
        </div>
        ";

    }
?>

</header>

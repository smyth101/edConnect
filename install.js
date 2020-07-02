const fse = require('fs-extra')
const path = require('path')
const mysql = require('mysql');

const new_user = process.argv[2]

function password_generator(){
  return Math.random().toString(36).slice(-8);
}


async function createDirectory(userLocation){
  return new Promise(async function(resolve){
    try {
      await fse.copy('./project', userLocation)
      console.log('user directory created successfully')
      resolve()
    } catch (err) {
      console.error(err)
      reject()
    }
  })
}


function updateDbConnection(userLocation,new_user,password){
  const connectionFile = path.join(userLocation,'connection.php');
  fse.readFile(connectionFile, 'utf8', function (err,data) {
    if (err) {
      return console.log(err);
    }
    let result = data.replace(/USERNAME/g, new_user);
    result = result.replace(/PASSWORD/g, password);
    fse.writeFile(connectionFile, result, 'utf8', function (err) {
       if (err) return console.log(err);
    });
  });
}

async function createDbAndUser(new_user,password){
  return new Promise(function(resolve){

    const con = mysql.createConnection({
      host: "localhost",
      user: "root",
      password: ""
    }); 
    con.connect(function(err) {
      if (err) throw err;
      console.log("Connected!");
    });
    con.query("CREATE USER '" + new_user + "'@'localhost' IDENTIFIED BY '" + password + "';");
    con.query("CREATE DATABASE " + new_user + "DB;",(err)=>{
      if(err) throw err
      con.query("GRANT ALL PRIVILEGES ON " + new_user + "DB.* TO '" + new_user + "'@'localhost';",(err,result)=>{
        if(err) throw err
        con.end()
        resolve(result)
      });
    });
  })
}

function DbTablesGenerator(new_user){
  const con = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: new_user + 'DB'
  }); 
  con.connect(function(err) {
    if (err) throw err;
    console.log("Connected!");
    con.query("CREATE TABLE `staff` (\n`name` varchar(255) NOT NULL,\n`staff_id` varchar(255) NOT NULL,\n`password` varchar(255) NOT NULL,\n`subjectCode` varchar(255) NOT NULL,\n`type` varchar(255) NOT NULL,\nPRIMARY KEY (`staff_id`)\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `grades` (\n`student_id` varchar(255) NOT NULL,\n`date` date NOT NULL,\n`subjectCode` varchar(255) NOT NULL,\n`mark` int(11) NOT NULL,\n`type` int(11) NOT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `journal` (\n`subjectCode` varchar(255) NOT NULL,\n`date` datetime NOT NULL,\n`description` varchar(255) NOT NULL,\n`due_date` datetime NOT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `students` (\n`name` varchar(255) NOT NULL,\n`student_id` varchar(255) NOT NULL,\n`password` varchar(255) NOT NULL,\n`subjectCode` varchar(255) NOT NULL,\n`year` int(11) NOT NULL,\nPRIMARY KEY (`student_id`)\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `timetable` (\n`day` varchar(255) NOT NULL,\n`start_time` time NOT NULL,\n`end_time` time NOT NULL,\n`subjectCode` varchar(255) NOT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `parents` (\n`name` varchar(255) NOT NULL,\n`parent_id` varchar(255) NOT NULL,\n`password` varchar(255) NOT NULL,\n`student_id` varchar(255) NOT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `notes` (\n`staff_id` varchar(255) NOT NULL,\n`sender_type` varchar(255) NOT NULL,\n`sender_id` varchar(255) NOT NULL,\n`note_type` varchar(255) NOT NULL,\n`note` text NOT NULL,\n`date` date NOT NULL,\n`detention_date` date NOT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `absenteeism` (\n`student_id` varchar(255) NOT NULL,\n`date` date NOT NULL,\n`abscent` varchar(255) NOT NULL,\n`present` varchar(255) NOT NULL,\n`reason` varchar(255) NOT NULL,\n`description` mediumtext NOT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `actionables` (\n`student_id` varchar(255) NOT NULL,\n`staff_id` varchar(255) NOT NULL,\n`type` varchar(255) NOT NULL,\n`date` date NOT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `activities` (\n`staff_id` varchar(255) NOT NULL,\n`student_id` text NOT NULL,\n`start_date` datetime NOT NULL,\n`end_date` datetime NOT NULL,\n`type` varchar(255) NOT NULL,\n`description` text NOT NULL,\n`permission_required` tinyint(1) NOT NULL,\n`permission_list` text NOT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    con.query("CREATE TABLE `announcements` (\n`date` datetime NOT NULL,\n`staff_id` varchar(255) NOT NULL,\n`message` mediumtext NOT NULL,\n`type` varchar(255) NOT NULL\n) ENGINE=InnoDB DEFAULT CHARSET=latin1",function(err){
      if(err) throw err;
      con.end();
    });
  });

}

async function main(){
  userLocation = path.join('./',new_user);
  await createDirectory(userLocation);
  const password = password_generator();
  updateDbConnection(userLocation,new_user,password);
  await createDbAndUser(new_user,password);
  DbTablesGenerator(new_user);
  console.log(password)
}


main()


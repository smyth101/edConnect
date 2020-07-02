const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [attendanceListStaffTest,submitAttendanceStaffTest,editAttendanceStaffTest,attendanceHistoryStaffTest,attendanceAnalyticsStaffTest,attendanceStudentTest,attendanceParentTest];

async function attendanceListStaffTest(driver,suiteTest = false){
	await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='class-sidebar']/ul/li[1]")).click();
    await driver.wait(until.elementLocated(By.id("attendanceTable")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function submitAttendanceStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='class-sidebar']/ul/li[1]")).click();
    await driver.wait(until.elementLocated(By.xpath("//*[@id='attendanceTable']/div[2]/input")), 10000);
    await driver.findElement(By.xpath("//*[@id='attendanceTable']/div[2]/input")).click();
    await driver.wait(until.elementLocated(By.id("editAttendBtn")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function editAttendanceStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='class-sidebar']/ul/li[1]")).click();
    await driver.wait(until.elementLocated(By.id("editAttendBtn")), 10000);
    await driver.findElement(By.id("editAttendBtn")).click();
    await driver.findElement(By.xpath("//*[@id='attendCheck0']")).click();
    await driver.findElement(By.xpath("//*[@id='attendanceTable']/div[3]/input")).click();
    var message = await driver.findElement(By.xpath("//*[@id='attendanceTable']/div[1]/table/tbody/tr[2]/td[2]")).getText();
    if(message == "present"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function attendanceHistoryStaffTest(driver,suiteTest = false){
	await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='class-sidebar']/ul/li[2]")).click();
    await driver.wait(until.elementLocated(By.id("attendanceTable")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function attendanceAnalyticsStaffTest(driver,suiteTest = false){
	await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='class-sidebar']/ul/li[3]")).click();
    await driver.wait(until.elementLocated(By.className("myChart")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function attendanceStudentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[2]")).click();
    await driver.findElement(By.name('userID')).sendKeys('CarterC10');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[1]/a")).click();
    var message = await driver.findElement(By.xpath("/html/body/div[1]/h3")).getText();
    if(message == "Attendance Information"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function attendanceParentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[3]")).click();
    await driver.findElement(By.name('userID')).sendKeys('bcarter');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[1]/a")).click();
    var message = await driver.findElement(By.xpath("/html/body/div[1]/h3")).getText();
    if(message == "Reason for Absenteeism"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function main(){
    for(let i = 0;i < unitTests.length;i++){
        let driver = await new Builder().forBrowser("firefox").build();
        await driver.get(URL);
        unitTests[i](driver);
    }
}


main();

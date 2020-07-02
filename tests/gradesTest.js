const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [addTestStaffTest,gradesStaffTest,addGradesStaffTest,editGradesStaffTest,gradesAnalyticsStaffTest,gradesStudentTest,gradesAnalyticsStudentTest,gradesParentTest,gradesAnalyticsParentTest];

async function addTestStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='gradePageForm1']/li")).click();
    await driver.findElement(By.name('testDate')).sendKeys('2020-06-01');
    await driver.findElement(By.name('testDescription')).sendKeys('test');
    await driver.findElement(By.xpath("/html/body/div[4]/button")).click();
    await driver.wait(until.elementLocated(By.xpath("/html/body/div[4]")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function gradesStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='gradePageForm2']/li")).click();
	await driver.wait(until.elementLocated(By.xpath("/html/body/div[4]")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function addGradesStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click(); 
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='gradePageForm2']/li")).click();
    await driver.findElement(By.xpath("/html/body/span/button")).click();
    await driver.wait(until.elementLocated(By.id("editBtn")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function editGradesStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click(); 
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='gradePageForm3']/li/span[1]")).click();
    await driver.findElement(By.id("editBtn")).click();
    await driver.findElement(By.xpath("/html/body/div[7]/form/table/tbody/tr[2]/td[2]/input[1]")).sendKeys('50');
    await driver.findElement(By.xpath("/html/body/div[7]/form/table/tbody/tr[2]/td[3]/input")).sendKeys('test');
    await driver.findElement(By.xpath("/html/body/div[7]/form/button")).click();
    var grade = await driver.findElement(By.xpath("/html/body/div[7]/table/tbody/tr[2]/td[2]")).getText();
    var reason = await driver.findElement(By.xpath("/html/body/div[7]/table/tbody/tr[2]/td[3]")).getText();
    if(grade == "50" && reason == "grade changed from 0. test"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function gradesAnalyticsStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='gradePageForm4']/li")).click();
	await driver.wait(until.elementLocated(By.className("myChart")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function gradesStudentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[2]")).click();
    await driver.findElement(By.name('userID')).sendKeys('CarterC10');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click(); 
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='gradePageForm3']/li/span[1]")).click();
    await driver.wait(until.elementLocated(By.xpath("/html/body/div[3]")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function gradesAnalyticsStudentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[2]")).click();
    await driver.findElement(By.name('userID')).sendKeys('CarterC10');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click(); 
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='gradePageForm4']/li")).click();
    await driver.wait(until.elementLocated(By.className("myChart")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function gradesParentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[3]")).click();
    await driver.findElement(By.name('userID')).sendKeys('bcarter');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click(); 
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='gradePageForm3']/li/span[1]")).click();
    await driver.wait(until.elementLocated(By.xpath("/html/body/div[3]")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function gradesAnalyticsParentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[3]")).click();
    await driver.findElement(By.name('userID')).sendKeys('bcarter');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click(); 
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='gradePageForm4']/li")).click();
    await driver.wait(until.elementLocated(By.className("myChart")), 10000);
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

const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [supervisionStaffTest,detentionsStaffTest,manageStaffTest,manageTimetableStaffTest,studentApprovalStaffTest,manageStudentsStaffTest];

async function supervisionStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[4]/a")).click();
	await driver.findElement(By.xpath("/html/body/div[1]/ul/li[1]/a")).click();
	var message = await driver.findElement(By.xpath("/html/body/div[2]/h3")).getText();
    if(message == "Add/Update Supervision"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function detentionsStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[4]/a")).click();
	await driver.findElement(By.xpath("/html/body/div[1]/ul/li[2]/a")).click();
	var message = await driver.findElement(By.xpath("/html/body/div[2]/h3")).getText();
    if(message == "Detention List"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function manageStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[4]/a")).click();
	await driver.findElement(By.xpath("/html/body/div[1]/ul/li[3]/a")).click();
	var message = await driver.findElement(By.xpath("/html/body/div[2]/div[1]/h3")).getText();
    if(message == "Add Staff"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function manageTimetableStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[4]/a")).click();
	await driver.findElement(By.xpath("/html/body/div[1]/ul/li[4]/a")).click();
	var message = await driver.findElement(By.xpath("/html/body/div[2]/h3")).getText();
    if(message == "Timetable Management"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function studentApprovalStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[4]/a")).click();
	await driver.findElement(By.xpath("/html/body/div[1]/ul/li[5]/a")).click();
	var message = await driver.findElement(By.xpath("/html/body/div[2]/div/h3")).getText();
    if(message == "Required Approval List"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function manageStudentsStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[4]/a")).click();
	await driver.findElement(By.xpath("/html/body/div[1]/ul/li[6]/a")).click();
	var message = await driver.findElement(By.xpath("/html/body/div[2]/div/h3")).getText();
    if(message == "Set up Students"){
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
const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [logoutStaffTest,logoutStudentTest,logoutParentTest];

async function logoutStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.id("signoutForm")).click();
    var message = await driver.findElement(By.xpath("/html/body/div/div/span/h2")).getText();
	if(message == "Log In"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function logoutStudentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[2]")).click();
    await driver.findElement(By.name('userID')).sendKeys('CarterC10');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.id("signoutForm")).click();
    var message = await driver.findElement(By.xpath("/html/body/div/div/span/h2")).getText();
    if(message == "Log In"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function logoutParentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[3]")).click();
    await driver.findElement(By.name('userID')).sendKeys('bcarter');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.xpath("//*[@id='settingBtn']/span")).click();
	await driver.wait(until.elementLocated(By.xpath("//*[@id='signoutBtn']")), 10000);	
	await driver.findElement(By.xpath("//*[@id='signoutBtn']")).click();
    var message = await driver.findElement(By.xpath("/html/body/div/div/span/h2")).getText();
    if(message == "Log In"){
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

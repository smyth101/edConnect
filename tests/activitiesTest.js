const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [activitiesStaffTest,activitiesCreateStaffTest,activitiesStudentTest,activitiesParentTest];

async function activitiesStaffTest(driver,suiteTest = false){
	await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[5]/a")).click();
    var message = await driver.findElement(By.xpath("/html/body/form/div[1]/h3")).getText();
    if(message == "Add Activity"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function activitiesCreateStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[5]/a")).click();
    await driver.findElement(By.xpath('/html/body/form/div[2]/div[3]/input')).sendKeys('geo61');
	await driver.findElement(By.name("activityName")).sendKeys('test');
    await driver.findElement(By.name("activityDesc")).sendKeys('test');
    await driver.findElement(By.name("s_date")).sendKeys('2020-06-01');
    await driver.findElement(By.name("e_date")).sendKeys('2020-06-01');
    var name = await driver.findElement(By.xpath("/html/body/div[2]/div[2]/table/tbody/tr[2]/td[1]")).getText();
    var description = await driver.findElement(By.xpath("/html/body/div[2]/div[2]/table/tbody/tr[2]/td[2]")).getText();
    var start = await driver.findElement(By.xpath("/html/body/div[2]/div[2]/table/tbody/tr[2]/td[3]")).getText();
    var end = await driver.findElement(By.xpath("/html/body/div[2]/div[2]/table/tbody/tr[2]/td[4]")).getText();
    if(name != "test" && description != "test" && start != "2020-06-01" && end != "2020-06-01"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function activitiesStudentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[2]")).click();
    await driver.findElement(By.name('userID')).sendKeys('CarterC10');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[5]/a")).click();
    var message = await driver.findElement(By.xpath("/html/body/div[1]/h3")).getText();
    if(message == "Upcoming Activities"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function activitiesParentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[3]")).click();
    await driver.findElement(By.name('userID')).sendKeys('bcarter');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[5]/a")).click();
    var message = await driver.findElement(By.xpath("/html/body/div[1]/h3")).getText();
    if(message == "Permission Required"){
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

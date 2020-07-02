const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [journalStaffTest,journalDayNoteStaffTest,journalStudentTest,journalParentTest];

async function journalStaffTest(driver,suiteTest = false){
	await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[2]/a")).click();
    await driver.wait(until.elementLocated(By.id("jrnl-container")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function journalDayNoteStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[2]/a")).click();
	await driver.findElement(By.name('note-description')).sendKeys('test');
    await driver.findElement(By.name('note-d-date')).sendKeys('2020-06-01');
    await driver.findElement(By.xpath("//*[@id='note-form']/input[2]")).click();
    var description = await driver.findElement(By.xpath("//*[@id='jrnl-container']/div[1]/div[1]/table/tbody/tr[4]/td[2]")).getText();
    var date = await driver.findElement(By.xpath("//*[@id='jrnl-container']/div[1]/div[1]/table/tbody/tr[4]/td[3]")).getText();
    if(description == "test" && date == "2020-06-01"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function journalStudentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[2]")).click();
    await driver.findElement(By.name('userID')).sendKeys('CarterC10');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[2]/a")).click();
    await driver.wait(until.elementLocated(By.id("jrnl-container")), 10000);
    if(suiteTest == false){
        driver.close();
    }
}

async function journalParentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[3]")).click();
    await driver.findElement(By.name('userID')).sendKeys('bcarter');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[1]/a")).click();
    await driver.findElement(By.xpath("//*[@id='sub-nav']/ul/li[2]/a")).click();
    await driver.wait(until.elementLocated(By.id("jrnl-container")), 10000);
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

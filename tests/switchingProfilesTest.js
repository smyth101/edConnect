const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [switchingProfilesParentTest];

async function switchingProfilesParentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[3]")).click();
    await driver.findElement(By.name('userID')).sendKeys('bcarter');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.xpath("//*[@id='settingBtn']/span")).click();
	await driver.wait(until.elementLocated(By.xpath("//*[@id='accountChangee2OZmTia']/li")), 10000);	
	await driver.findElement(By.xpath("//*[@id='accountChangee2OZmTia']/li")).click();	
	var absences = await driver.findElement(By.xpath("/html/body/div[2]/span[1]/h4")).getText();	
	if(absences == "0"){
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
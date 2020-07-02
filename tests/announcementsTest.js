const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [announcementsStaffTest,announcementsCreateStaffTest];

async function announcementsStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();	
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[6]/a")).click();
    var message = await driver.findElement(By.xpath("/html/body/form/div[1]/h3")).getText();
    if(message == "New Announcement"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function announcementsCreateStaffTest(driver,suiteTest = false){
	await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[6]/a")).click();
    await driver.findElement(By.name('message')).sendKeys('test');
	await driver.findElement(By.xpath("/html/body/form/div[2]/span[4]/button")).click();	
	var message = await driver.findElement(By.xpath("/html/body/div[2]/div/table/tbody/tr[2]/td[1]")).getText();
	var sender = await driver.findElement(By.xpath("/html/body/div[2]/div/table/tbody/tr[2]/td[2]")).getText();
	var type = await driver.findElement(By.xpath("/html/body/div[2]/div/table/tbody/tr[2]/td[4]")).getText();
	if(message == "test" && sender == "timmy tommy" && type == "10 am announcement"){
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

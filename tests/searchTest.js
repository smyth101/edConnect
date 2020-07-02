const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [searchStaffTest];

async function searchStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/div/input")).sendKeys('ciara carter');
	await driver.wait(until.elementLocated(By.xpath("//*[@id='livesearch']")), 10000);
	await driver.findElement(By.xpath("//*[@id='livesearch']/span")).click();
    await driver.findElement(By.xpath("//*[@id='profile-header']/h2")).getText().then(function(txt){
		if(txt == "Ciara Carter"){
			console.log("Success");
		}
		else{
			console.log("FAIL");
		}
	});	
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
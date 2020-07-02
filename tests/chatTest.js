const {Builder, By, Key, until} = require('selenium-webdriver');
const URL = "https://edconnect.ie/login.php";

const unitTests = [chatStaffTest,chatCreateStaffTest,chatStudentTest,chatParentTest];

async function chatStaffTest(driver,suiteTest = false){
	await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
	await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[3]/a")).click();
    var message = await driver.findElement(By.xpath("//*[@id='chat-sidebar']/h2")).getText();
    if(message == "Chats"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function chatCreateStaffTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[1]")).click();
    await driver.findElement(By.name('userID')).sendKeys('timmy');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[3]/a")).click();
    await driver.findElement(By.xpath("//*[@id='chat-sidebar']/input")).click();
    await driver.findElement(By.xpath("//*[@id='add-chat-container']/input")).sendKeys('molly hogan');
    await driver.wait(until.elementLocated(By.xpath("//*[@id='chatsearch']/span")), 10000);
    await driver.findElement(By.xpath("//*[@id='chatsearch']/span")).click();
    await driver.findElement(By.xpath("//*[@id='submit-new-chat']")).click();
    await driver.wait(until.elementLocated(By.xpath("//*[@id='chat-sidebar']/div[1]/div")), 10000);
    var name = await driver.findElement(By.xpath("//*[@id='chat-sidebar']/div[1]/div")).getText();
    if(name == 'Molly Hogan'){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }
    if(suiteTest == false){
        driver.close();
    }
}

async function chatStudentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[2]")).click();
    await driver.findElement(By.name('userID')).sendKeys('CarterC10');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[3]/a")).click();
    var message = await driver.findElement(By.xpath("//*[@id='chat-sidebar']/h2")).getText();
    if(message == "Chats"){
        console.log("Success");
    }
    else{
        console.log("FAIL");
    }

    if(suiteTest == false){
        driver.close();
    }
}

async function chatParentTest(driver,suiteTest = false){
    await driver.findElement(By.xpath("/html/body/div/div/div[1]/form/label[3]")).click();
    await driver.findElement(By.name('userID')).sendKeys('bcarter');
    await driver.findElement(By.name('password')).sendKeys('jimmy');
    await driver.findElement(By.tagName('button')).click();
    await driver.findElement(By.xpath("//*[@id='navbarSupportedContent']/ul[1]/li[3]/a")).click();
    var message = await driver.findElement(By.xpath("//*[@id='chat-sidebar']/h2")).getText();
    if(message == "Chats"){
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
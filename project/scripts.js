
function settingsDropdown(menuId){
    menu = document.getElementById(menuId);
    if(menu.style.display == 'none'){
        menu.style.display = 'block';
    }
    else{
        menu.style.display = 'none';
    }
}

function signout(){
    document.getElementById('signoutForm').submit();
}


function getAttendance(){
    let studentsList = document.getElementsByClassName('studentAttendance');
    console.log(studentsList);
    for(i=0;i < (studentsList.length);i++){
        let rowCells = studentsList[i].getElementsByTagName('td');
        console.log(rowCells[0].innerText);
        let inputs = rowCells[1].getElementsByTagName('input');
        let late = rowCells[2].getElementsByTagName('input')[0];
        if(inputs.length != 0 && inputs[0].type == "checkbox"){
            let checked = inputs[0].checked;
            if(checked == false){
                inputs[1].value = 'absent-' + inputs[1].value;
            }
            else{
                if(late.checked == true){
                    inputs[1].value = 'late-' + inputs[1].value;
                }
                else{
                    inputs[1].value = 'present-' + inputs[1].value;
                }
            }
        }
    }
    document.getElementById('attendanceTable').submit();
}

function submitter(formName){
    document.getElementById(formName).submit();
}

function classHistorySubmitter(){
    let dateOptions = document.getElementsByName("dateTime")[0];
    let selected = dateOptions.options[dateOptions.selectedIndex].innerText;
    selected = selected.split(' period')[0];
    document.getElementById('historyDate').value = selected;
    document.getElementById('dateTime').submit();
}

function editAttendance(){
    document.getElementById('editAttendBtn').style.display = 'none';
    document.getElementById('cancelAttendBtn').style.display = 'block';
    document.getElementsByTagName("th")[2].style.display='block';
    let currentList = document.getElementsByClassName('studentAttendance');
    for(i=0;i < currentList.length;i++){
        let cells = currentList[i].getElementsByTagName('td');
        inputCell = cells[1];
        lateCell = document.createElement('td')
        currentList[i].appendChild(lateCell)
        let status = inputCell.innerText;
        let inputData = inputCell.getElementsByTagName('input')[0];
        console.log(inputData);
        if(status == 'present'){
            inputCell.innerHTML = "";
            inputCell.innerHTML += '<input type="checkbox"  id="attendCheck' + i + '" checked>';
            inputCell.appendChild(inputData);
            lateCell.innerHTML += '<input type="checkbox" id="lateCheck' + i + '" onclick="setLate(' + i + ')">'
        }
        else if(status == 'late'){
            inputCell.innerHTML = "";
            inputCell.innerHTML += '<input type="checkbox"  id="attendCheck' + i + '" checked disabled>';
            inputCell.appendChild(inputData);
            lateCell.innerHTML += '<input type="checkbox" id="lateCheck' + i + '" onclick="setLate(' + i + ')" checked>'
        }
        else if(status == 'School Activity'){
            name = inputCell.getElementsByTagName('span')[0].getAttribute('data-activStudentId');
            console.log("name is : " + name)
            lateCell.innerHTML += '<input type="button" value="alter" onclick="alterActivityAttendance(event,\'' + i + '\',\'' + name + '\')">'; 
        }
        else{
            inputCell.innerHTML = "";
            inputCell.innerHTML +=  '<input  id="attendCheck' + i + '" type="checkbox">';
            inputCell.appendChild(inputData);
            lateCell.innerHTML += '<input type="checkbox" id="lateCheck' + i + '" onclick="setLate(' + i + ')">'
        }
    }
    let submitButton = document.createElement('input');
    submitButton.type = 'button';
    submitButton.value='submit';
    submitButton.setAttribute('onclick','getAttendance()');
    submitButton.setAttribute('class','btn btn-primary');
    submitContainer = document.createElement('div');
    submitContainer.setAttribute('class','row justify-content-center');
    submitContainer.appendChild(submitButton);
    document.getElementById('attendanceTable').appendChild(submitContainer);
}

function cancelBtn(){
    location.reload(true)
}


function setSidebarValue(value){
    document.getElementById('class-sidebar-value').value = value;
    document.getElementById('class-sidebar').submit();
}

function timetableToAttend(timeObj,subjectCode,timeslot=false){
    const timetableInput = document.getElementById('timetableAttendInput');
    timetableInput.value = subjectCode;
    const timetableForm = document.getElementById('timetableForm');
    let periodClicked = (timeObj.parentElement.parentElement.rowIndex);
    if(periodClicked==5 ||periodClicked==6 || periodClicked==7){
        periodClicked -= 1;
    }
    else if(periodClicked==9 ||periodClicked==10 || periodClicked==11){
        periodClicked -= 2;
    }
    if(timeslot == true){
        const date = new Date();
        const today = date.getDay();
        const dateClicked = (timeObj.parentElement.cellIndex) % 7;
        let todayDate = date;
        const todayTime = (date.getHours() * 100) + date.getMinutes();
        let selectedTime = timeObj.innerText.split('-')[0].split(':');
        selectedTime = selectedTime[0] + selectedTime[1];
        if(todayTime < selectedTime || today !=dateClicked){
            while(dateClicked != todayDate.getDay()){
                todayDate.setDate(todayDate.getDate() - 1);
            }
            const previousDate = todayDate.getFullYear() + '-' + (todayDate.getMonth() + 1) + '-' + todayDate.getDate();
            const historyInput = document.createElement('input');
            historyInput.value=previousDate;
            historyInput.type='hidden';
            historyInput.name='timetableToHistoryDate';
            timetableForm.appendChild(historyInput);
            const historyPeriod = document.createElement('input');
            historyPeriod.value='period' +  periodClicked;
            historyPeriod.type='hidden';
            historyPeriod.name='timetableToHistoryPeriod';
            timetableForm.appendChild(historyPeriod);
        }
        else{
            const todayPeriod = document.createElement('input');
            todayPeriod.value='period' +  periodClicked;
            todayPeriod.type='hidden';
            todayPeriod.name='timetableToTodayPeriod';
            timetableForm.appendChild(todayPeriod);
            console.log('hit')
        }        
    }
    timetableForm.submit();
}

function changeJrnlPage(date){
    document.getElementById('journal-date-input').value = date;
    document.getElementById('journal-date').submit();
}

function noteTypeChange(noteType){
    var nType = noteType.options[noteType.selectedIndex].text;
    if(nType == 'Note'){
        document.getElementById('due-date').style.display = 'none';
        document.getElementById('note-category').style.display = 'inline-block';

    }
    else{
        document.getElementById('due-date').style.display = 'inline-block';
        document.getElementById('note-category').style.display = 'none';
    }
}


function submitNote(){
    noteSubject = document.getElementById('classCodeDrpdwn');
    if(noteSubject.options[noteSubject.selectedIndex].text == 'All'){
        document.getElementById('note-to-all-modal').style.display = 'inline-block';
    }
    else{
        document.getElementById('note-form').submit();
    }
}

function confirmSubmitNote(){
    document.getElementById('note-to-all-modal').style.display = 'none';
}

function cancelNote(){
    document.getElementById('note-to-all-modal').style.display = 'none';
}


function showProfileDetention(){
    document.getElementById('profile-detention-container').style.display='inline-block';
}

function showResult(str,contentId,divId = false,actionable=false) {
    if(divId == false){
        divId = contentId;
        secondParam = '';
    }
    else{
        console.log('update')
        secondParam = '&p=update';
    }
    if(actionable == false){
        thirdParam = '';
    }
    else{
        thirdParam = '&a=true';
    }

    if (str.length==0) {
      document.getElementById(divId).innerHTML="";
      document.getElementById(divId).style.border="0px";
      return;
    }
    if (window.XMLHttpRequest) {
      xmlhttp=new XMLHttpRequest();
    } else { 
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
      if (this.readyState==4 && this.status==200) {
        document.getElementById(divId).innerHTML=this.responseText;
      }
}
    xmlhttp.open("GET",contentId + ".php?q="+str+secondParam+thirdParam,true);
    xmlhttp.send();
  }

function searchSubmit(studentId){
    document.getElementById('search-value').value = studentId;
    document.getElementById('search-form').submit();
}


function cancelDetention(){
    document.getElementById('profile-detention-container').style.display='none';
}

function formatDate() {
    var d = new Date(),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
}

function addActivityMember(userId,name){
    console.log(name)
    members = document.getElementById('members');
    document.getElementById('memberHeader').style.display = 'block'
    currentMembers = []
    mList = document.getElementsByName('activityMemberId[]')
    mList.forEach(element => {
        currentMembers.push(element.value)
    });
    for(i=0;i < userId.length;i++){
        if(!currentMembers.includes(userId[i])){
            member = document.createElement('div');
            member.setAttribute('class','member');
            member.setAttribute('id','member'+i)
            member.innerText = name[i];
            memberInput = document.createElement('input');
            memberInput.setAttribute('type','hidden');
            memberInput.setAttribute('value',userId[i])
            memberInput.setAttribute('name','activityMemberId[]');
            member.appendChild(memberInput);
            memberNameInput = document.createElement('input');
            memberNameInput.setAttribute('type','hidden');
            memberNameInput.setAttribute('value',name[i])
            memberNameInput.setAttribute('name','chatMemberName[]');
            member.appendChild(memberNameInput);
            removeMember = document.createElement('input');
            removeMember.setAttribute('type','button');
            removeMember.setAttribute('onclick','removeActivityMember("member' + i + '")');
            removeMember.setAttribute('value','x')
            member.appendChild(removeMember)
            members.appendChild(member);
        }
    }
}

function removeActivityMember(member){
    member = document.getElementById(member);
    type = member.getAttribute('class');
    memberParent = member.parentNode
    memberParent.removeChild(member);
    console.log(document.getElementsByClassName(type))
    if(document.getElementsByClassName(type).length == 0){
        document.getElementById(type + 'Header').style.display = 'none';
    }
}

function clearActivityMembers(){
    memberContainer = document.getElementById('members');
    members = document.getElementsByClassName('member');
    document.getElementById('memberHeader').style.display = 'none';
    numMember = members.length;
    for(let i = 0;i<numMember;i++){
        memberContainer.removeChild(members[0]);
    }
}

function viewActivityPerm(id){
    var permForm = document.createElement("form");
    permForm.setAttribute('method',"post");
    permForm.setAttribute('action',"activities.php");
    permForm.setAttribute('id','permForm');

    var i = document.createElement("input");
    i.setAttribute('type',"hidden");
    i.setAttribute('name','activity_id');
    i.setAttribute('value',id);

    permForm.appendChild(i);
    document.getElementById('permList').appendChild(permForm)
    document.getElementById('permForm').submit();
}

function addActivitySupervisor(userId,name){
    var members = document.getElementById('supervisors');
    document.getElementById('supervisorHeader').style.display = 'block'
    currentMembers = []
    sList = document.getElementsByName('activitySupervisorId[]')
    sList.forEach(element => {
        currentMembers.push(element.value)
    });
    for(i=0;i < userId.length;i++){
        if(!currentMembers.includes(userId[i])){
            var member = document.createElement('div');
            member.setAttribute('class','supervisor');
            member.setAttribute('id','supervisor'+i)
            member.innerText = name[i];
            memberInput = document.createElement('input');
            memberInput.setAttribute('type','hidden');
            memberInput.setAttribute('value',userId[i])
            memberInput.setAttribute('name','activitySupervisorId[]');
            member.appendChild(memberInput);
            memberNameInput = document.createElement('input');
            memberNameInput.setAttribute('type','hidden');
            memberNameInput.setAttribute('value',name[i])
            memberNameInput.setAttribute('name','activitySupervisorName[]');
            member.appendChild(memberNameInput);
            removeMember = document.createElement('input');
            removeMember.setAttribute('type','button');
            removeMember.setAttribute('onclick','removeActivityMember("supervisor' + i + '")');
            removeMember.setAttribute('value','x')
            member.appendChild(removeMember)
            members.appendChild(member);
        }
    }
}


function clearActivitySupervisors(){
    memberContainer = document.getElementById('supervisors');
    members = document.getElementsByClassName('supervisor');
    document.getElementById('supervisorHeader').style.display = 'none';
    numMember = members.length;
    for(let i = 0;i<numMember;i++){
        memberContainer.removeChild(members[0]);
    }
}

function changeAccount(formId){
    document.getElementById(formId).submit();
}

function setLate(index){
    checkbox = document.getElementById('lateCheck' + index);
    if(checkbox.checked == true){
        document.getElementById('attendCheck' + index).checked = true;
        document.getElementById('attendCheck' + index).disabled = true;
    }
    else{
        document.getElementById('attendCheck' + index).checked = false;
        document.getElementById('attendCheck' + index).disabled = false;
    }

}

function editGrades(){
    document.getElementById('distBtn').style.display = 'none';
    rows = document.getElementsByTagName('tr')
    i = 1
    // rows[0].innerHTML += '<th>Reason</th>';
    while(i < rows.length){
        mark = rows[i].getElementsByTagName('td')[1].innerHTML
        rows[i].removeChild(rows[i].getElementsByTagName('td')[2])
        rows[i].getElementsByTagName('td')[0].innerHTML += '<input type="hidden" value="' + rows[i].getElementsByTagName('td')[0].getAttribute('data-gredit') + '" name="changeMarkStudent[]">'
        rows[i].getElementsByTagName('td')[1].innerHTML = '<input type="number" onchange="markChange(event)" name="changeMark[]" data-original-mark=' + mark + ' value=' + mark + '><input type="hidden" value=' + mark + ' name="originalMark[]">';
        rows[i].innerHTML += '<td><input type="text" name="changeMarkReason[]"></td>';
        i+=1;
    }
    document.getElementById('editBtn').style.display = 'none';
    document.getElementById('cancelBtn').style.display = 'block';
    document.getElementsByTagName('table')[0].outerHTML = '<form action="grades.php" method="post"><input type="hidden" name="gradeDate" value="' + document.getElementsByName('gradeDate')[0].value + '">' + document.getElementsByTagName('table')[0].outerHTML + '<button class="btn btn-primary">submit</button></form>';    
}

function markChange(event){
    original = event.target.getAttribute('data-original-mark');
    changed = event.target.value
    if(original != changed){
        event.target.parentNode.parentNode.getElementsByTagName('td')[2].getElementsByTagName('input')[0].required = true;
    }
    else{
        event.target.parentNode.parentNode.getElementsByTagName('td')[2].getElementsByTagName('input')[0].required = false;
    }
}

function alterActivityAttendance(event, index,name){
    if(!!document.getElementById('indexActivityModal')){
        document.getElementById('indexActivityModal').style.display = 'block';
    }
    else{
        modal = document.createElement('div')
        modal.setAttribute('id','indexActivityModal')
        modal.innerHTML = 'Are you sure you want to change this students attendance status from being present at the listed activity. This should only be done if the student is currently present at the class.<input type="button" value="continue" id="continueBtn"><input type="button" value="cancel" id="cancelBtn">';
        document.body.appendChild(modal);
    }
    document.getElementById('continueBtn').addEventListener('click',function(){
        event.target.parentNode.parentNode.getElementsByTagName('td')[1].innerHTML = '<input id="attendCheck' + index + '" type="checkbox"><input type="hidden" name="attendList[]" value="' + name + '">'
        event.target.parentNode.parentNode.getElementsByTagName('td')[2].innerHTML = '<input type="checkbox" id="lateCheck'  + index + '" onclick="setLate(\'' + index + '\')"></td></tr>'
        document.getElementById('indexActivityModal').style.display = 'none';
    });
    document.getElementById('cancelBtn').addEventListener('click',function(){
        document.getElementById('indexActivityModal').style.display = 'none';
    })
}

function showGradeDist(changeValue=false,compare=false){
    if(changeValue != false){
        if(Number.isInteger(changeValue)){
            filter = changeValue
        }
        else{
            filter = changeValue.target.value 
        }
    }
    else{
        filter = 10
    }
    if(compare != false || changeValue != false){
        chartContainer.remove();
    }
    if(compare == true){
        compareBool = true
    }
    if(typeof prevResultArray != 'undefined'){
        if(compareBool == true){
            compare = true
        }
    }
    else{
        compare = false;
    }
    resultTable = document.getElementsByTagName('table')[0];
    results = resultTable.getElementsByTagName('tr');
    resultArray = [];
    for(let i=1;i<results.length;i++){
        result = results[i].getElementsByTagName('td')[1].innerHTML;
        resultArray.push(parseInt(result));
    }
    max = Math.max.apply(null, resultArray)
    if(compare == true){
        prevMax = Math.max.apply(null,prevResultArray);
        max = Math.max.apply(null,[max,prevMax])
    }
    labels = []
    values = {}
    for(i=1;(i*filter-(filter-1)) <= max;i++){
        values[i] = 0;
        label = ((i * filter) - filter) + '-' + (i * filter)
        labels.push(label)
    }
    resultArray.forEach(e => {
        values[Math.floor(e/filter)+1] += 1
    })
    valueArray = Object.values(values)

    if(typeof prevResultArray != 'undefined'){
        compareBtn = document.createElement('input')
        compareBtn.setAttribute('class','btn btn-primary mr-2')
        compareBtn.setAttribute('type','button')
        if(compare != false){
            compareBtn.setAttribute('value','reset')
            compareBtn.setAttribute('onclick','compareBool=false;showGradeDist('+filter+',false)');
            prevValues = {}
            for(i=1;(i*filter-(filter-1)) <= max;i++){
                prevValues[i] = 0;
            }
            prevResultArray.forEach(e => {
                prevValues[Math.floor(e/filter)+1] += 1
            })
            prevValueArray = Object.values(prevValues)
        }
        else{
            compareBtn.setAttribute('value','compare')
            compareBtn.setAttribute('onclick','showGradeDist('+filter+',true)');    
        }
    }
    chartContainer = document.createElement('div')
    chartContainer.setAttribute('id','chartContainer')
    chartDropdown = document.createElement('select')
    dropOption = document.createElement('option')
    dropOption.innerHTML = '10'
    if(filter==10){
        dropOption.selected = true
    }
    chartDropdown.appendChild(dropOption)
    dropOption = document.createElement('option')
    dropOption.innerHTML = '20'
    if(filter==20){
        dropOption.selected = true
    }
    chartDropdown.appendChild(dropOption)
    dropOption = document.createElement('option')
    dropOption.innerHTML = '25'
    if(filter==25){
        dropOption.selected = true
    }
    chartDropdown.setAttribute('onchange','showGradeDist(event)')
    chartDropdown.appendChild(dropOption)
    document.getElementsByClassName('modal-body')[0].appendChild(chartContainer)
    if(typeof prevResultArray != 'undefined'){
        chartContainer.appendChild(compareBtn)
    }
    chartContainer.appendChild(chartDropdown)
    canvas = document.createElement('canvas')
    canvas.setAttribute('class','myChart')
    canvas.setAttribute('height','80px');
    chartContainer.appendChild(canvas)
    let ctx = document.getElementsByClassName("myChart")[0]
    datasets = [{
        label: "student count",
         backgroundColor: "#3e95cd",
        data: valueArray
      }];
      if(compare == true){
          datasets.push({
            label: "student count",
             backgroundColor: "#ff4000",
            data: prevValueArray
          })
      }
    var myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: datasets
        },
        options: {
          legend: { display: false },
          title: {
            display: true,
            text: 'Grade distribution'
          }
        }
    });
}

function addActionableSupervisor(userId,name){
    var supervisorName = document.getElementById('supervisors');
    var member = document.createElement('div');
    member.setAttribute('class','supervisor');
    member.innerText = "staff supervisor: " + name;
    memberInput = document.createElement('input');
    memberInput.setAttribute('type','hidden');
    memberInput.setAttribute('value',userId)
    memberInput.setAttribute('name','actionableSupervisorId');
    member.appendChild(memberInput);
    removeMember = document.createElement('input');
    removeMember.setAttribute('type','button');
    removeMember.setAttribute('onclick','removeActionableSupervisor()');
    removeMember.setAttribute('value','change')
    removeMember.setAttribute('class','btn btn-secondary')
    member.appendChild(removeMember)
    supervisorName.appendChild(member);
    document.getElementById('supervisorSearch').style.display = 'none';
}

function removeActionableSupervisor(){
    document.getElementById('supervisorSearch').style.display = 'block';
    supervisor = document.getElementById('supervisors');
    supervisor.removeChild(supervisor.childNodes[0]);
}

function showActionModal(e){
    document.getElementById('action-student-name').innerHTML = e.innerHTML
    student_id = e.parentNode.childNodes[1].childNodes[1].value
    document.getElementById('action-user').value = student_id
    console.log(student_id)
}

function closeActionModal(){
    document.getElementById('action-student-name').innerHTML
    document.getElementById('actionForm').reset();
}

function changedTestType(e){
    classTest = document.getElementById('classTestdrpdwn');
    if(e.value == 'Class test'){
        classTest.style.display = 'none';
        classTest.getElementsByTagName('select')[0].disabled = true;

    }
    else{
        classTest.style.display = 'inline-block';
        classTest.getElementsByTagName('select')[0].disabled = false;
    }
}

function missedLastExams(student){
    let examsMissed = 0;
    let missedIndex = 2;
    let missed = student.data[student.data.length-1];
    while(isNaN(missed)){
        examsMissed+=1;
        if(missedIndex > student.data.length){
            break
        }
        missed = student.data[student.data.length - missedIndex];
        missedIndex +=1;
    }
    return student.label + ' has missed the last ' + examsMissed + ' exams';
}

function totalExamsMissed(student,missAllowance){
    let total = 0
    for(let i =0;i < student.data.lengh;i++){
        if(isNaN(student.data[i])){
            total += 1
        }
    }
    if(total > missAllowance){
        return student.label + ' has missed ' + total + ' exams this school year'
    }
    return false
}

function singleTestDropOff(student,average,dropValue,returnValue = false){
    let lastDiff = student.data[student.data.length -1] - average[average.length -1]
    let index = average.length - 2
    let secondDiff = 'NaN'
    while(index > 0){
        if(!isNaN(student.data[index])){
            secondDiff = student.data[index] - average[index];
            break
        }
        index = index -1 
    }
    console.log(secondDiff)
    if(secondDiff == 'NaN'){
        return false;
    }
    if((lastDiff + dropValue) > secondDiff){
        return false
    }
    let actualDiff = Math.abs(secondDiff - lastDiff)
    if(returnValue == true){
        return actualDiff
    }
    return student.label + ' has fallen below expected projection by ' + actualDiff + '%';
}

function multipleTestDropOff(student,average,dropValue,noOfTests){
    let total = 0
    for(let i=0;i < noOfTests;i++){
        if(student.data.length < 2){
            return false
        }
        console.log(student.data,overviewChart)
        single = singleTestDropOff(student,average,dropValue,true)
        if(single == false){
            return false
        }
        else{
            console.log(student.label,single)
            total += single
        }
        student.data.pop()
        average.pop()
        while(isNaN(student.data[student.data.length-1])){
            student.data.pop()
            average.pop()
        }
    }
    return student.label + ' grades have dropped below projection in the last 3 exams by ' + total;
}

function createInfoBox(message,chartIndex){
    rowContainer = document.createElement('div')
    rowContainer.setAttribute('class','col-12 justify-content-start mr-0 ml-0 mt-3')
    infoContainer = document.createElement('div')
    infoContainer.setAttribute('class','col-lg-4 col-10 ml-3 info-container');
    closeBtn = document.createElement('button')
    closeBtn.setAttribute('type','button')
    closeBtn.setAttribute('class','close')
    closeBtn.setAttribute('aria-label','Close')
    closeIcon = document.createElement('span')
    closeIcon.setAttribute('aria-hidden','true')
    closeIcon.innerHTML='&times;'
    closeBtn.appendChild(closeIcon)
    closeBtn.setAttribute('onclick','document.getElementsByClassName("info-container")[' + chartIndex + '].style.display="none"')
    infoContainer.appendChild(closeBtn)
    infoContainer.appendChild(document.createElement('br'))
    infoList = document.createElement('ul')
    for(let i = 0;i < message.length;i++){
        info = document.createElement('li')
        info.innerHTML = message[i]
        infoList.appendChild(info)
    }
    infoContainer.appendChild(infoList)
    rowContainer.appendChild(infoContainer)
    let currentChart = document.getElementsByClassName('myChart')[chartIndex]
    console.log(currentChart)
    let parent = currentChart.parentNode
    parent.insertBefore(rowContainer,currentChart)

}

function gradeInfo(){
    chartsLen = document.getElementsByClassName('myChart').length;
    headerIndex = 1
    for(let i = 0; i < chartsLen;i+=2){
        let message = [];
        overviewChart = eval('myLineChart' + i).data.datasets[2].data;
        studentChart = eval('myLineChart' + (i+1)).data;
        if(studentChart.labels.length >= 2){
            studentChart.datasets.forEach(student =>{
                let lastResult = student.data[student.data.length-1];
                let secondLast = student.data[student.data.length-2];
                if(isNaN(lastResult) && isNaN(secondLast)){
                    message.push(missedLastExams(student))
                }
                if(isNaN(lastResult)){
                    let totalMissed = totalExamsMissed(student,1)
                    if(totalMissed != false){
                        message.push(totalMissed)
                    }
                }
                if(!isNaN(lastResult)){
                    dropOff = singleTestDropOff(student,overviewChart,20)
                    if(dropOff != false){
                        message.push(dropOff)
                    }
                    console.log('name:' + student.label + ',' + lastResult)
                    studentCopy = {'data':student.data.slice(),'label':student.label}
                    overviewCopy = overviewChart.slice()
                    multiple = multipleTestDropOff(studentCopy,overviewCopy,5,2)
                    if(multiple != false){
                        message.push(multiple)
                    }
                }

            })
        }
        if(message.length > 0){
            createInfoBox(message,headerIndex)
        }
        headerIndex +=2
    }
}

function addStaffRows(n){
    containerCount = document.getElementsByClassName('addStaffContainer').length;
    let container = document.getElementsByClassName('addStaffContainer')[containerCount-1];
    for(i=0;i < n;i++){
        let containerClone = container.cloneNode(true);
        let clearForm = document.createElement('form');
        clearForm.appendChild(containerClone);
        clearForm.reset();
        cloneInputs = containerClone.getElementsByTagName('input');
        let index= containerCount + i;
        for(let j = 0; j < cloneInputs.length;j++){
            console.log(cloneInputs[j].type)
            if(cloneInputs[j].type == 'text' || cloneInputs[j].type == 'email'){
                cloneInputs[j].name= cloneInputs[j].name.substring(0,cloneInputs[j].name.length-1) + index;
            }
            else{
                cloneInputs[j].name = cloneInputs[j].name.substring(0,cloneInputs[j].name.length-3) + index + '[]';
                cloneInputs[j].parentNode.style.backgroundColor = 'white';
            }
        }
        let priviliges = containerClone.getElementsByTagName('select')[0];
        priviliges.name = priviliges.name.substring(0,priviliges.name.length-1) + index;
        container.after(containerClone)
        container = containerClone
    }

}

function checkLoginNameAvailable(name){
    if (window.XMLHttpRequest) {
        xmlhttp=new XMLHttpRequest();
      } else { 
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
          document.getElementById("name_status").innerHTML=this.responseText;
          if(this.responseText == '<span class="text-danger">name taken</span>'){
              document.getElementsByTagName('button')[0].disabled = true
          }
          else{
            document.getElementsByTagName('button')[0].disabled = false
          }
        }
      }
      xmlhttp.open("GET","staffsearch.php?q="+name,true);
      xmlhttp.send();
}

function staffSubjectSelect(e){
    parentN = e.parentNode
    if(e.checked == true){
        parentN.style.backgroundColor = '#4da3ff'
    }
    else{
        parentN.style.backgroundColor = 'white'
    }
}

function deleteStaff(e){
    currentName = document.getElementsByName('currentName')[0];
    currentEmail = document.getElementsByName('currentEmail')[0];
    currentName.name = 'deleteName';
    currentEmail.name = 'deleteEmail';
    document.getElementById('toRemoveName').innerHTML = currentName.value

}

function cancelDeleteStaff(){
    currentName = document.getElementsByName('deleteName')[0];
    currentEmail = document.getElementsByName('deleteEmail')[0];
    currentName.name = 'currentName';
    currentEmail.name = 'currentEmail';
}

function editCurrentStaff(e){
    parentN = e.parentNode;
    parentN.innerHTML = '<button type="button" class="text-danger mr-2" data-toggle="modal" data-target="#deleteModal" onclick="deleteStaff(event.target)" data-toggle="modal" data-target="#deleteModal">Delete</button><button class="mr-2" type="button" onclick="location.reload()">Cancel</button><button class="btn btn-primary">Save</button>'
    editBtns = document.getElementsByClassName('editBtn')
    for(let i = 0;i < editBtns.length;i++){
        editBtns[i].disabled = true;
    }
    rowColumns = parentN.parentNode.getElementsByTagName('td');
    rowColumns[0].innerHTML = '<input type="text" name="updateName" value="' + rowColumns[0].innerHTML + '" required><input type="hidden" name="currentName" value="' + rowColumns[0].innerHTML + '">';
    rowColumns[1].innerHTML = '<input type="email" name="updateEmail" value="' + rowColumns[1].innerHTML + '" required><input type="hidden" name="currentEmail" value="' + rowColumns[1].innerHTML + '">'; 
    virticalLists = document.getElementsByClassName('vertical-list');
    subjectList = virticalLists[0].cloneNode(true);
    qualifiedList = virticalLists[1].cloneNode(true);
    clearForm = document.createElement('form')
    clearForm.appendChild(subjectList);
    clearForm.reset();
    clearForm.appendChild(qualifiedList);
    clearForm.reset();
    subjects = rowColumns[2].innerHTML.split(',');
    console.log(subjectList)
    subjectListInputs = subjectList.getElementsByTagName('input')
    for(let j=0;j < subjectListInputs.length;j++){
        subjectListInputs[j].name='updateSubject[]';
        if(subjects.includes(subjectListInputs[j].value)){
            subjectListInputs[j].checked = true
            subjectListInputs[j].dispatchEvent(new Event('change'))
        }
    }
    qualified = rowColumns[3].innerHTML.split(',');
    qualifiedListInputs = qualifiedList.getElementsByTagName('input')
    for(let j=0;j < qualifiedListInputs.length;j++){
        qualifiedListInputs[j].name = 'updateQualified[]';
        if(qualified.includes(qualifiedListInputs[j].value)){
           qualifiedListInputs[j].checked = true
           qualifiedListInputs[j].dispatchEvent(new Event('change'))
        }
    }
    rowColumns[2].innerHTML = '';
    rowColumns[2].appendChild(subjectList)
    rowColumns[3].innerHTML = '';
    rowColumns[3].appendChild(qualifiedList)
    if(rowColumns[4].innerHTML == 'standard'){
        rowColumns[4].innerHTML = '<select name="updatePrivilige"><option selected>standard</option><option>higher</option></select>';
    }
    else{
        rowColumns[4].innerHTML = '<select name="updatePrivilige"><option>standard</option><option selected>higher</option></select>'
    }
    console.log(rowColumns)
}

function getTimetable(search = false,liveOn = false,liveValue=false){
    if (window.XMLHttpRequest) {
        xmlhttp=new XMLHttpRequest();
      }
      else{ 
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            tableBody = document.getElementsByTagName("tbody")[0];
            tableBody.innerHTML = '';
            x = $(this.responseText);
            for(let i =0;i < x.length;i++){
                tableBody.appendChild(x[i]);   
            }
        }
      }
      if(liveOn != false){
          xmlhttp.open("GET","timetableTable.php?&s="+search + "&lo="+liveOn + "&lv="+liveValue ,true);
      }

      else{
        xmlhttp.open("GET","timetableTable.php?&s="+search ,true);
      }
      xmlhttp.send();
}

function getSubjectCodes(subject,code){
    console.log('subject',subject)
    if (window.XMLHttpRequest) {
        xmlhttp=new XMLHttpRequest();
      } else { 
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            console.log('response',this.responseText)
            document.getElementsByName('updateSubjectCode')[0].innerHTML = this.responseText
            document.getElementsByName('updateYear')[0].innerHTML =  document.getElementsByName('updateSubjectCode')[0].value.substring(3,4)
            }
        }
    xmlhttp.open("GET","timetableTable.php?sc="+subject+"&c="+code ,true);
    xmlhttp.send();
}

function timetableLiveUpdate(e){
    console.log(e)
    updateOn = document.getElementById('updateOn');
    updateCheck = document.getElementById('liveUpdate');
    if(updateCheck.checked == true && updateOn.value.toLowerCase() == e.name.toLowerCase()){
        
        if(updateOn.value.toLowerCase() == 'year'){
            getTimetable(false,'subjectCode','___' + e.value + '%')
        }
        else if(updateOn.value.toLowerCase() == 'day'){
            getTimetable(false,e.name.toLowerCase(),e.value.toLowerCase())
        }
        else{
            getTimetable(false,e.name.toLowerCase(),e.value)
        }
    }
    
}

subjectList = [];
function addTimetableItem(e,item){
    parentN = e.parentNode;
    if(item == 'Room'){
        roomList= parentN.getElementsByTagName('select')[0]
    }
    else{
        subjectList = parentN.getElementsByTagName('select')[0]
    }
        
    parentN.innerHTML = ''
    parentN.innerHTML = item + ':<input type="text"><button type="button" class="btn btn-primary p-1" onclick="insertTimetableItem(event.target,\'' + item.toLowerCase() + '\')">add</button><button type="button" class="btn btn-secondary ml-1 pl-2 pr-2" onclick="cancelTimetableItem(event.target,\'' + item.toLowerCase() + '\')">x</button>';
}


function insertTimetableItem(e,item){
    parentN = e.parentNode;
    textInput = parentN.getElementsByTagName('input')[0];
    currentSubjects = subjectList
    for(let i = 0;i < currentSubjects.length;i++){
        console.log(currentSubjects[i].innerHTML);
        if(currentSubjects[i].innerHTML.toLowerCase() == textInput.value.toLowerCase()){
            document.getElementById('status').innerHTML = '<span class="text-danger">This subject has already exists</span>';
            parentN.replaceChild(eval(item + 'List'),textInput);
            return
        }
    }
    subjectValue = document.createElement('option')
    subjectValue.setAttribute('selected','selected');
    subjectValue.innerHTML =  textInput.value;
    parentN.replaceChild(eval(item + 'List'),textInput);
    let addBtn = '<button class="btn btn-primary text-white font-weight-bold" style="padding:0 5px 0 5px" title="create subject" onclick="addTimetableItem(event.target,\'' + item.charAt(0).toUpperCase() + item.slice(1) + '\')" type="button">+</button><button type="button" class="btn btn-secondary ml-1 pl-2 pr-2" onclick="cancelTimetableItem(event.target,\'' + item.toLowerCase() + '\')">x</button>';
    addBtn = $(addBtn);
    parentN.replaceChild(addBtn[0],e)
    eval(item + 'List').appendChild(subjectValue);
    subjectSelect(subjectValue.innerHTML)
}

function subjectSelect(e){
    timetableLiveUpdate(e)
    let subjects = document.getElementsByName('updateSubject[]');
    let codes = document.getElementsByName('updateCode[]');
    subjectCodeTable = document.getElementById('subjectCodeTable');
    for(let i=0;i < subjects.length;i++){
        console.log(subjects[i].innerHTML)
        if(e == subjects[i].innerHTML){
            document.getElementsByName('subjectCode')[0].value = codes[i].innerHTML;
            return;
        }
    }
    if(subjectAdded == true){
        subjectCodeTable.removeChild(subjectCodeTable.lastChild);
    }
    subjectAdded = true;
    subj = '<tr><td><span name="updateSubject[]">' + e + ' </span></td><td><span name="updateCode[]">' + e.value.substring(0,3).toLowerCase() + '</span></td></tr>';
    subj = $(subj);
    subjectCodeTable.appendChild(subj[0]);
    document.getElementsByName('subjectCode')[0].value = e.value.substring(0,3).toLowerCase();
}

function cancelTimetableItem(e,item){
    parentN = e.parentNode;
    textInput = parentN.getElementsByTagName('input')[0];
    parentN.replaceChild(eval(item + 'List'),textInput);
    let addBtn = '<button class="btn btn-primary text-white font-weight-bold" style="padding:0 5px 0 5px" title="create subject" onclick="addTimetableItem(event.target,\'' + item.charAt(0).toUpperCase() + item.slice(1) + '\')" type="button">+</button><button type="button" class="btn btn-secondary ml-1 pl-2 pr-2" onclick="cancelTimetableItem(event.target,\'' + item.toLowerCase() + '\')">x</button>';
    addBtn = $(addBtn);
    console.log(e.parentNode.getElementsByTagName('button')[0]);
    parentN.replaceChild(addBtn[0],e.parentNode.getElementsByTagName('button')[0])
    parentN.removeChild(e);
}

function editTimetable(e){
    tbody = document.getElementsByTagName('tbody')[0];
    editBtns = tbody.getElementsByTagName('button');
    console.log(subjectList)
    for(let i = 0;i < editBtns.length;i++){
        if(editBtns[i] != e){
            editBtns[i].style.display = 'none';
        }
        else{
            row = tbody.getElementsByTagName('tr')[i];
            cells = row.getElementsByTagName('td');
            rowClone = row.cloneNode(true);
            cellClone = rowClone.getElementsByTagName('td');
            console.log(cells)
            for(let j = 0;j < 8;j++){
                if(j == 5){
                    inputs = '<td><input name="updateStart" type="time" value="' + cells[j].innerHTML + '"></td>';
                }
                else if(j == 6){
                    inputs = '<td><input name="updateEnd" type="time" value="' + cells[j].innerHTML + '"></td>';
                }
                else if(j == 1){
                    inputs = '<td><input name="updatePeriod" type="number" min="1" value="' + cells[j].innerHTML + '"></td>';
                }
                else if( j== 3){
                    inputs = '<td name="updateYear">' + cells[j].innerHTML + '</td>';
                }
                else if(j == 2){
                    inputs = document.createElement('td')
                    subjects = document.getElementsByName('subject')[0].cloneNode(true)
                    subjects.setAttribute('name','updateSubj')
                    subjects.setAttribute('onchange','getSubjectCodes(this.value,"")')
                    for(let k = 0;k < subjects.childNodes.length;k++){
                        if(subjects.childNodes[k].value == cells[j].innerHTML){
                            subjects.childNodes[k].setAttribute('selected','selected')
                        }
                    }
                    inputs.appendChild(subjects)
                }
                else if(j == 0){
                    inputs = document.createElement('td')
                    days = document.getElementsByName('day')[0].cloneNode(true)
                    days.removeAttribute('onchange');
                    days.setAttribute('name','updateDay')
                    for(let k = 0;k < days.childNodes.length;k++){
                        if(days.childNodes[k].value == (cells[j].innerHTML.charAt(0).toUpperCase() + cells[j].innerHTML.slice(1))){
                            days.childNodes[k].setAttribute('selected','selected')
                        }
                    }
                    inputs.appendChild(days)
                }
                else if(j == 4){
                    let subj = document.getElementsByName('updateSubj')[0].value;
                    getSubjectCodes(subj,cells[j].innerHTML)
                    inputs = '<td><select name="updateSubjectCode" onchange="document.getElementsByName(\'updateYear\')[0].innerHTML = this.value.substring(3,4)"></select></td>';

                }
                else if(j == 7){
                    inputs = document.createElement('td')
                    rooms = document.getElementsByName('room')[0].cloneNode(true)
                    rooms.removeAttribute('onchange');
                    rooms.setAttribute('name','updateRoom')
                    for(let k = 0;k < rooms.childNodes.length;k++){
                        if(rooms.childNodes[k].value == (cells[j].innerHTML.charAt(0).toUpperCase() + cells[j].innerHTML.slice(1))){
                            rooms.childNodes[k].setAttribute('selected','selected')
                        }
                    }
                    inputs.appendChild(rooms)
                }
                else{
                    inputs = '<td><input type="text" value="' + cells[j].innerHTML + '"></td>';
                }
                inputs = $(inputs);
                row.replaceChild(inputs[0],cells[j]);
            }
            cells[8].outerHTML = '<td><input type="hidden" value="' + cellClone[7].innerHTML + '" name="updateFromRoom"><input type="hidden" value="' + cellClone[0].innerHTML + '" name="updateFromDay"><input type="hidden" value="' + cellClone[1].innerHTML + '" name="updateFromPeriod"><input type="hidden" value="' + cellClone[4].innerHTML + '" name="updateFromCode"><button class="btn btn-primary">Update</button></td><td><input class="btn btn-secondary" type="button" onclick="location.reload()" value="Cancel"><input type="button" class="text-danger" onclick="deleteTimetable(event.target)" value="Delete"></td>'
            
        }
    }

}


function editSubjectCodes(e){
    e.outerHTML = '<button class="btn btn-primary">Update</button><button class="btn btn-secondary m-3" type="button" onclick="location.reload()">Cancel</button>'
    codes = document.getElementsByName('updateCode[]');
    subjects = document.getElementsByName('updateSubject[]');
    for(let i=0;i < codes.length;i++){
        let input = '<input type="text" minlength="3" maxlength="3" name="updateCode[]" value="' + codes[i].innerHTML + '" required>';
        input = $(input)[0]
        codes[i].parentNode.replaceChild(input,codes[i])
        subjects[i].outerHTML +='<input type="hidden" value="' + subjects[i].innerHTML + '" name="updateSubject[]">'
    }


}

function deleteTimetable(e){
    row = e.parentNode.parentNode;
    day = row.firstChild.firstChild;
    period = row.childNodes[1].firstChild;
    room = row.childNodes[7].firstChild;
    dayValue = $(day).prop("defaultValue");
    periodValue = $(period).prop("defaultValue");
    roomValue = $(room).prop("defaultValue");
    console.log(roomValue)
    console.log(document.getElementById('deleteForm'))
    if(document.getElementById('deleteForm') == null){
        form = document.createElement('form');
        form.setAttribute('action','timetableManagement.php');
        form.setAttribute('method','post');
        form.setAttribute('id','deleteForm')
    }
    else{
        form = document.getElementById('deleteForm'); 
    }
    form.innerHTML = '<input type="hidden" name="deleteDay" value="' + dayValue + '"><input type="hidden" name="deleteRoom" value="' + roomValue + '"><input type="hidden" name="deletePeriod" value="' + periodValue + '">'
    document.body.appendChild(form)
    $('#deleteModal').modal('show');
}


function acceptApplication(e,id){
    acceptForm = '<form action="studentApproval.php" method="post"><input type="hidden" name="applId" value="' + id + '"><input type="hidden" name="applStatus" value="accepted"></form>';
    acceptForm = $(acceptForm);
    e.parentNode.appendChild(acceptForm[0])
    acceptForm[0].submit();
}

function rejectApplication(e,id){
    rejectForm = '<form action="studentApproval.php" method="post"><input type="hidden" name="applId" value="' + id + '"><input type="hidden" name="applStatus" value="rejected"></form>';
    rejectForm = $(rejectForm);
    e.parentNode.appendChild(rejectForm[0])
    rejectForm[0].submit();
}

function showStudents(name=false){
    if (window.XMLHttpRequest) {
        xmlhttp=new XMLHttpRequest();
      } else { 
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            console.log('response',this.responseText)
            document.getElementById('studentList').innerHTML = this.responseText
            }
        }
    xmlhttp.open("GET","studentTable.php?s="+name,true);
    xmlhttp.send();
}

function editStudent(e,id){
    row = e.parentNode.parentNode;
    columns = row.childNodes
    columns[0].innerHTML = '<input name="name" type="text" value="' + columns[0].innerHTML + '" required>';
    columns[1].innerHTML = '<input name="year" type="number" min="1" value="' + columns[1].innerHTML + '" required>';
    columns[2].setAttribute('class','virtical-list')
    subjectOutput = '<ul class="vertical-list" id="vertical-list">';
    studentSubjectList = columns[2].innerHTML.split(',')
    for(let i =0; i < subjectList.length;i++){
        if(studentSubjectList.includes(subjectList[i])){
            subjectOutput += '<li style="background-color:#4da3ff"><input type="checkbox" onchange="staffSubjectSelect(event.target)" name="subject[]" value="' + subjectList[i] + '" checked>' + subjectList[i] + '</li>';
        }
        else{
            subjectOutput += '<li><input type="checkbox" onchange="staffSubjectSelect(event.target)" value="' + subjectList[i] + '" name="subject[]">' + subjectList[i] + '</li>';
        }
    }
    subjectOutput + '</ul>';
    columns[2].innerHTML = subjectOutput;
    rows = document.getElementById('studentList').childNodes
    console.log(rows)
    for(let i = 0;i < rows.length;i++){
        if(rows[i].childNodes[3] != e.parentNode){
            rows[i].childNodes[3].style.display = 'none'
        }
        else{
            rows[i].childNodes[3].innerHTML = '<input type="hidden" name="id" value="' + id +  '"><button class="btn btn-primary">Submit</button><button type="button" data-toggle="modal" data-target="#deleteModal" class="text-danger" onclick="deleteStudent(event.target,\'' + id + '\')">Delete</button><button type="button" onclick="location.reload()" class="btn btn-secondary">Cancel</button>'
        }
    }
}

function deleteStudent(e,id){
    document.getElementsByClassName('modal-body')[0].innerHTML = 'Are you sure you want to remove this student?<br><b>Remove:</b>' + e.parentNode.parentNode.childNodes[0].childNodes[0].value + '<input type="hidden" value="' + id + '" name="deleteId">'
}


function showStudentSetup(search='false'){
    if (window.XMLHttpRequest) {
        xmlhttp=new XMLHttpRequest();
      } else { 
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            document.getElementById('studentSetupList').innerHTML = this.responseText
            }
        }
    xmlhttp.open("GET","studentSetupTable.php?s="+search,true);
    xmlhttp.send();
}

function studentImage(){
    console.log(document.getElementById('confirmFooter').childNodes)
    if(document.getElementById('confirmFooter').childNodes.length > 3){
        document.getElementById('confirmFooter').removeChild(document.getElementById('confirmFooter').childNodes[3])
    }
    confirmBtn = document.getElementById('confirmCredBtn');
    confirmBtn.innerHTML = 'Upload';
    confirmBtn.disabled = 'true';
    document.getElementById('confirmBody').innerHTML = `
        <button type="button" onclick="takeImage()">Take Image</button>
        <button type="button" onclick="uploadImage()">Upload Image</button>
        <button type="button" onclick="window.location.href = 'http://localhost/2020-ca400-harkine4-smythd32/src/project/studentManagement.php'">Use Default</button>`
}

function takeImage(){
    prevBtn = document.createElement('button')
    prevBtn.setAttribute('type','button')
    prevBtn.innerHTML = 'Previous'
    prevBtn.setAttribute('onclick','studentImage()')
    document.getElementById('confirmFooter').appendChild(prevBtn)
    document.getElementById('confirmBody').innerHTML = `
    <div id="camera">
    <video id="video">Video stream not available.</video>
    <button id="startbutton" class="btn btn-primary">Take photo</button>
    </div>
    </div>
    <canvas id="canvas" style="display:none">
    </canvas><div class="output">
    <img id="photo" alt="The screen capture will appear in this box.">`;

    var width = 320; 
    var height = 0;
    
    var streaming = false;
    
    var video = null;
    var canvas = null;
    var photo = null;
    var startbutton = null;
    var confirmBtn = document.getElementById('confirmCredBtn');
    confirmBtn.innerHTML = 'Upload';
    confirmBtn.disabled = 'true';
    
    video = document.getElementById('video');
    canvas = document.getElementById('canvas');
    photo = document.getElementById('photo');
    startbutton = document.getElementById('startbutton');
    camera = document.getElementById('camera');
    navigator.mediaDevices.getUserMedia({video: true, audio: false})
    .then(function(stream) {
        video.srcObject = stream;
        video.play();
    })
    .catch(function(err) {
        console.log("An error occurred: " + err);
    });

    video.addEventListener('canplay', function(ev){
        if (!streaming) {
        height = video.videoHeight / (video.videoWidth/width);
        
        if (isNaN(height)) {
            height = width / (4/3);
        }
        x = (height / 4) * 3;
        console.log(x)
        video.setAttribute('width', width);
        video.setAttribute('height', height);
        canvas.setAttribute('width', width);
        canvas.setAttribute('height', height);
        camera.setAttribute('style', 'width:' + x + 'px;overflow:hidden');
        streaming = true;
        }
    }, false);

    startbutton.addEventListener('click', function(ev){
        takepicture();
        ev.preventDefault();
    }, false);


    function takepicture() {
        confirmBtn.removeAttribute('disabled')
        confirmBtn.setAttribute('onclick','uploadCamera()')
        var context = canvas.getContext('2d');
        if (width && height) {
            x = (height / 4) * 3;
            offset = (width - x) / 2
            canvas.width = x;
            canvas.height = height;
            context.drawImage(video,0, 0, width, height);
            var data = canvas.toDataURL('image/png');
            photo.setAttribute('src', data);
        } 
        startbutton.innerHTML = 'Retake'
    }

}

function uploadCamera(){
    form = document.createElement('form')
    form.setAttribute('method','post')
    form.setAttribute('action','studentManagement.php');
    input = document.createElement('input');
    input.setAttribute('name','image');
    id = document.createElement('input');
    id.setAttribute('name','imageId');
    id.setAttribute('value',confId);
    imgData = document.getElementById('photo').getAttribute('src')
    input.setAttribute('value',imgData);
    form.appendChild(input)
    form.appendChild(id)
    document.body.appendChild(form)
    form.submit()
}

function uploadImage(){
    confirmBtn.removeAttribute('disabled')
    prevBtn = document.createElement('button')
    prevBtn.setAttribute('type','button')
    prevBtn.innerHTML = 'Previous'
    prevBtn.setAttribute('onclick','studentImage()')
    document.getElementById('confirmFooter').appendChild(prevBtn)
    var cnt = $("#studentForm").contents();
    $("#studentForm").replaceWith(cnt);
    document.getElementById('confirmBody').innerHTML = `
    <form action="studentUpload.php" method="post" enctype="multipart/form-data" id="uploadForm">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload"  accept="image/x-png,image/jpeg"  required>
    <input type="hidden" value="` +  confId + `" name="imageId">
    `
    document.getElementById('confirmCredBtn').setAttribute('type','submit');
    document.getElementById('confirmCredBtn').setAttribute('onclick','if( document.getElementById("fileToUpload").files.length != 0 ){document.getElementById("uploadForm").submit()}')
}


function forgottenCred(name,address,id){
    if(document.getElementById('forgotModal') != null){
        document.getElementById('studentForm').removeChild(document.getElementById('forgotModal'))
    }
    forgotModal = ` 
    <div class="modal fade" id="forgotModal" tabindex="-1" role="dialog" aria-labelledby="forgotModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotModalLabel">Forgotten Student Credentials</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                To recover a students login ability new credentials are generated for the student. The next page will show the students new login details so it is advised that the student is present.
                <br>
                Name:<b>` + name + `</b>
                <br>
                Address:<b>` + address + `</b>
            </div>
            <div class="modal-footer">
            <input type="hidden" id="confirmId" name="confirmId" value="` + id +  `">
            <input type="hidden" name="forgotCred" value="true">
                <button class="btn btn-primary">Confirm</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            </div>
        </div>
    </div>`;
    forgotModal = $(forgotModal);
    document.getElementById('studentForm').appendChild(forgotModal[0]);
    $('#forgotModal').modal('show');
}

function decryptFile(filename,id){
    if (window.XMLHttpRequest) {
      xmlhttp=new XMLHttpRequest();
    } else { 
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            data = JSON.parse(this.responseText);
            ext = data['extention'];
            type = '';

            if(ext == 'jpg' || ext == 'jpeg' || ext ==  'png' || ext == 'gif'){
                type = 'image/' + ext;
                document.getElementById(id).src='data:' + type + ';base64,' + data['data'];
            }
            else{
                document.getElementById(id).href='data:application/octet-stream;base64,' + data['data'];
                document.getElementById(id).download=document.getElementById(id).innerHTML;
            }
            document.getElementById(id).removeAttribute('id');
        }
    }
    xmlhttp.open("GET",'decrypter.php?f=' + filename ,true);
    xmlhttp.send();
}

function showLogin(e = false){

    document.getElementsByClassName('login-details')[0].style.display='block';
    document.getElementsByClassName('btn-group')[0].style.display='none';
    document.getElementById('back-arrow').setAttribute('class','mb-2 d-inline');
    if(e == false){
        return;
    }
    else{
        forgotPassword = '<span class="d-block" style="cursor:pointer" id="forgotPassword" data-type="' + e + '" onclick="forgotPwModal()"><u>forgot password</u></span>';
        forgotPassword = $(forgotPassword);
        console.log(document.getElementsByClassName('login-details')[0]);
        console.log(forgotPassword[0],document.getElementsByClassName('btn')[0])
        document.getElementsByClassName('login-details')[0].insertBefore(forgotPassword[0],document.getElementsByClassName('btn')[3]);
    }
}

function hideLogin(){
    document.getElementsByClassName('login-details')[0].style.display='none';
    document.getElementsByClassName('btn-group')[0].style.display='block';
    document.getElementById('back-arrow').setAttribute('class','mb-2 d-none');
    document.getElementsByClassName('login-details')[0].removeChild(document.getElementById('forgotPassword'));
    document.getElementsByClassName('login-modal')[0].removeChild(document.getElementById('recover-password'));
}

function forgotPwModal(){
    document.getElementsByClassName('login-details')[0].style = 'none';
    pwDetails = `
    <div class="mt-3" id="recover-password">
        <form action='login.php' method='post' id='pw-form'>
            <p>Please enter your email to the account you wish to reser the password on. An email with a recovery link will then be sent you.</p>
            Email:  <input type='email' name='email' required>
            <button class='btn btn-primary mt-2'>Submit</button>
        </form>
    </div>`;
   pwDetails = $(pwDetails);
   document.getElementsByClassName('login-modal')[0].appendChild(pwDetails[0]);
   accountType = document.getElementById('forgotPassword').getAttribute('data-type');
   aType = document.createElement('input');
   aType.setAttribute('name','recoverAccount');
   aType.setAttribute('value',accountType);
   aType.setAttribute('type','hidden');
   document.getElementById('pw-form').appendChild(aType);

}

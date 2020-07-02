function addChat(){
    document.getElementById('add-chat-container').style.display='block'
}

function addChatMember(userId,name,update=false){
    userIdList = [];
    if(update == false){
        var members = document.getElementById('members');
        submitId = 'submit-new-chat';
        naming = '';
    }
    else{
        var members = document.getElementById('updateMembers');
        submitId = 'submit-updated-members';
        naming = 'update';
        idList = document.getElementsByClassName('modal-body')[1].getElementsByTagName('td');
        for(let i=0;i < idList.length;i++){
            id = idList[i].getAttribute('data-id')
            userIdList.push(id)
        }
    }
    document.getElementById(submitId).removeAttribute('disabled')
    currentMembers = document.getElementsByName('chatMemberId[]');
    newMember = true;
    for(i=0;i < userId.length;i++){
        for(j = 0;j < currentMembers.length;j++){
            if(currentMembers[j].value == userId[i]){
                break
            }
        }
        if(j != currentMembers.length || userIdList.includes(userId[i])){
            continue;
        }
        var member = document.createElement('div');
        member.setAttribute('class','member');
        member.setAttribute('id','member'+i)
        member.innerText = name[i];
        memberInput = document.createElement('input');
        memberInput.setAttribute('type','hidden');
        memberInput.setAttribute('value',userId[i])
        memberInput.setAttribute('name',naming + 'chatMemberId[]');
        member.appendChild(memberInput);
        memberNameInput = document.createElement('input');
        memberNameInput.setAttribute('type','hidden');
        memberNameInput.setAttribute('value',name[i])
        memberNameInput.setAttribute('name',naming + 'chatMemberName[]');
        member.appendChild(memberNameInput);
        removeMember = document.createElement('input');
        removeMember.setAttribute('type','button');
        removeMember.setAttribute('onclick','removeChatMember("member' + i + '")');
        removeMember.setAttribute('value','x')
        member.appendChild(removeMember)
        members.appendChild(member);
    }
    
    if(members.childElementCount > 2){
        nameInput = document.createElement('input');
        nameInput.setAttribute('type','text');
        nameInput.required = true;
        nameInput.setAttribute('name','chatName')
        document.getElementById('chat-name').innerText = 'Group Chat Name: ';
        document.getElementById('chat-name').appendChild(nameInput);
        
    }
}

function removeChatMember(member){
    member = document.getElementById(member);
    member.parentNode.removeChild(member);
    if(members.childElementCount >= 2){
        chatName = document.getElementById('chat-name');
        chatName.removeChild(chatName.getElementsByTagName('input')[0])
    }
    if(members.childElementCount == 1){
        document.getElementById('submit-new-chat').setAttribute('disabled','')
    }
}

function clearChatMembers(update=false){
    if(update==true){
        containerId = 'updateMembers';
    }
    else{
        containerId = 'members';
    }
    memberContainer = document.getElementById(containerId);
    members = document.getElementsByClassName('member');
    numMember = members.length;
    document.getElementById('submit-new-chat').setAttribute('disabled','')
    for(let i = 0;i<numMember;i++){
        memberContainer.removeChild(members[0]);
    }
}

function cancelNewChat(){
    chatContainer = document.getElementById('add-chat-container');
    chatContainer.getElementsByTagName('input')[0].value='';
    clearChatMembers();
    document.getElementById('chatsearch').innerHTML = '';
    chatName = document.getElementById('chat-name');
    chatName.removeChild(chatName.getElementsByTagName('input')[0])
}


function showChatTime(hour,minute){
    hour = hour + "";
    minute = minute + ""
    if(hour.length == 1){
        hour = "0" + hour
    }
    if(minute.length == 1){
        minute = "0" + minute
    }
    return hour + ":" + minute;
}


function showDeleteChat(id){
    deleteIcon = document.getElementById('delete' + id);
    deleteIcon.style.display = 'inline-block';
    document.getElementById('message-cont' + id).setAttribute('onmouseleave','hideDeleteChat("' + id + '")')
}

function hideDeleteChat(id){
    deleteIcon = document.getElementById('delete' + id);
    deleteIcon.style.display = 'none';
}

function deleteMessage(id,remover){
    socket.emit('remove',{id:id,remover:remover})
}

function showChatGroupSettingIcon(event){
    if(!!event.target.parentNode.getElementsByTagName('input')[0]){
        event.target.parentNode.getElementsByTagName('input')[0].style.display = 'inline-block'        
        event.target.parentNode.setAttribute('onmouseleave','event.target.getElementsByTagName(\'input\')[0].style.display = \'none\'')
    }
}

function showChatGroupSettings(chatId){
    showchatGroupMembers(chatId);

    console.log('hit')
    document.getElementById('convIdValue').value = chatId;
}

function updateChatGroupMembers(){
    document.getElementsByClassName('modal-footer')[1].style.display = 'block';
    document.getElementById('addMemberBtn').style.display='none';
    document.getElementById('updateChatMembersContainer').style.display='block';
}

function clearUpdateModal(){
    document.getElementsByClassName('modal-footer')[1].style.display = 'none';
    document.getElementById('addMemberBtn').style.display='block';
    document.getElementById('updateChatMembersContainer').style.display='none';
    modal =  document.getElementById("chatGroupSettingModal");
    modal.getElementsByTagName("table")[0].remove();
    document.getElementById('updateSearch').value='';
    document.getElementById('updateChatMembers').innerHTML = '';
    clearChatMembers(true);

}
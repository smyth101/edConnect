<?php
    session_start();
    if(!isset($_SESSION['userId'])){
        header('location:login.php');
    }
    require('common-functions.php');
    require('connection.php');
    date_default_timezone_set("Europe/Dublin");
    $bulk = new MongoDB\Driver\BulkWrite;
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    $query = new MongoDB\Driver\Query(['uid' => $_SESSION['accountId']]);
    $rows = $manager->executeQuery('chat.token',$query);
    $token;
    // FIXME find better solution to identifying unreturned lists
    foreach($rows as $row){
        $token = $row->token;
        $status = true;
    }
    if(!isset($status)){
        $token = bin2hex(random_bytes(11));
        $value = ['token'=>$token,'uid'=>$_SESSION['accountId']];
        $bulk->insert($value);
        $manager->executeBulkWrite('chat.token', $bulk);
        
    }

    if(isset($_POST['fileFolderId'])){
        require('upload.php');
    }




    if(isset($_SESSION['imageName']) && isset($_SESSION['convId'])){
        $convId = new MongoDB\BSON\ObjectId($_SESSION['convId']);
        $timestamp = time();
        $value=['conversationId' => $_SESSION['convId'],'message'=>$_SESSION['imageName'],'name'=>$_SESSION['name'],'uid'=>$_SESSION['accountId'],'timestamp'=>$timestamp,'type'=>$_SESSION['fileType']];
        $result = $bulk->insert($value);
        $manager->executeBulkWrite('chat.message', $bulk);
        $bulk2 = new MongoDB\Driver\BulkWrite;
        $bulk2->update(['_id' => $convId], ['$set' => ['last_used' => $timestamp]]);
        $manager->executeBulkWrite('chat.conversation', $bulk2);
        unset($_SESSION['imageName']);
        unset($_SESSION['convId']);
        unset($_SESSION['fileType']);
    }

    if(isset($_POST['chatMemberId'])){
        if(isset($_POST['chatName'])){
            $chatName = $_POST['chatName'];
        }
        else{
            $chatName = '<direct-conversation>';
        }
        $members =  implode('","',$_POST['chatMemberId']);
        $timestamp = time();
        $value = ['members' => $_POST['chatMemberId'],'name' => $chatName,'memberNames' => $_POST['chatMemberName'],'last_used'=>$timestamp];
        $result = $bulk->insert($value);
        $manager->executeBulkWrite('chat.conversation', $bulk);
        mkdir('./conversation_files/' . $result);   
    }

    if(isset($_POST['updatechatMemberId'])){
        $convId = new MongoDB\BSON\ObjectId($_POST['updateConvId']);
        foreach($_POST['updatechatMemberId'] as $key=>$value){
            $bulk->update(['_id' => $convId], ['$push' => ['members' => $value,'memberNames' => $_POST['updatechatMemberName'][$key]]]);
        }
        $manager->executeBulkWrite('chat.conversation', $bulk);
    }
    
?>
<!DOCTYPE html>
<head>
<title>edConnect | Chat</title>
    <link rel='stylesheet' type='text/css' href='style.css?<?php echo time(); ?>'>
    <script src='scripts.js?<?php echo time(); ?>'></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src='chat-functions.js?<?php echo time(); ?>'></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<?php
require('navbar.php');
?>
<div class='row ml-0'>
<div id='chat-sidebar' class='col-3'><h2>Chats</h2>
<input type='button' value='new' class="btn btn-primary" data-toggle="modal" data-target="#add-chat-modal">
</div>
<div id='chat-main' class='col-8'>
    <br>
        <div class="modal fade" id="add-chat-modal" tabindex="-1" role="dialog" aria-labelledby="addChatModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Chat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='cancelNewChat()'>
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <div id='add-chat-container'>
            Search by name <?php echo ($_SESSION['accountType'] == 'staff')?'or subject code':'of teacher';?><br>
            <input type="text" size="20" onkeyup="showResult(this.value,'chatsearch')" placeholder='search'>
            <div id="chatsearch"></div>
            <form action='chat.php' method='post'>
            <div id=chat-name></div>
            members
            <input type='button' value='clear' onclick='clearChatMembers()'>
            <div id='members'>
                <div id='creator'>
                    <input type='hidden' value='<?php echo $_SESSION['accountId'] ?>' name='chatMemberId[]'>
                    <input type='hidden' value='<?php echo $_SESSION['name']?>' name='chatMemberName[]'>
                </div>
            </div><br>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick='cancelNewChat()'>Cancel</button>
        <button id='submit-new-chat' disabled='true' class='btn btn-primary'>Create Chat</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
    </form>
    </div>
</div>
</div>
            </div>

    <div id='messages'>
    </div>
    <div id='new-mes' onclick='scrollBottom()'>New messages^</div>
    <!-- <br><textarea id='textarea' placeholder='enter messages'></textarea> -->
</div>
<div id='error_box'><?php
if(isset($_SESSION['errorMessage'])){
    echo $_SESSION['errorMessage'];
    unset($_SESSION['errorMessage']);
}
?>
</div>
</div>
<div class='row ml-0'>
    <div  class='col-lg-3 offset-lg-7 col-10 offset-2'>
        <form action="chat.php" method="post" enctype="multipart/form-data" class='d-inline' id='myForm'>
        <label for="fileToUpload">
            <img src='./images/file-upload.png' width='30px' class='mb-5'/>
        </label>
        <input type="file" name="fileToUpload" id="fileToUpload" class='d-none' onchange='(fileAttached == true)?fileAttached = false:fileAttached = true;'>
        <!-- <input type="submit" value="Upload" name="submit"> -->
        <input type='hidden' name='fileFolderId'>
    </form>
<textarea id='textarea' placeholder='enter messages'></textarea>
<input type='button'  class='btn btn-primary mb-5' onclick='sendMessage()' value='Send'>
</div>
</div>
    <div class="modal fade" id="chatGroupSettingModal" tabindex="-1" role="dialog" aria-labelledby="chatGroupSettingModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="chatGroupSettingModal">Chat Group Settings</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='clearUpdateModal()'>
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div id='updateChatMembersContainer' style='display:none'>
                      <h4>Add Members</h4>
                      Search by name <?php echo ($_SESSION['accountType'] == 'staff')?'or subject code':'of teacher';?><br>
                      <input type="text" size="20" onkeyup="showResult(this.value,'chatsearch','updateChatMembers')" placeholder='search' id='updateSearch'>
                      <div id='updateChatMembers'></div>
                        
                      <form action='chat.php' method='post'>
                      <div id=chat-name></div>
                      <input id='convIdValue' type='hidden' name='updateConvId'>
                      members
                      <input type='button' value='clear' class='btn btn-secondary' onclick='clearChatMembers(true)'>
                      <div id='updateMembers'>
                      </div><br>
                </div>
      <?php echo ($_SESSION['accountType'] == 'staff')?"<input class='btn btn-primary' id='addMemberBtn' type='button' value='add member' onclick='updateChatGroupMembers()'>":"";?><h3>Members</h3>
</div>
      <div class="modal-footer" style='display:none'>
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
        <button type='button' class="btn btn-secondary"  data-dismiss="modal" value='cancel' onclick='clearUpdateModal()'>Cancel</button>
        <!-- <button id='submit-updated-members' class="btn btn-primary" disabled='true'>Save changes</button> -->
        <button id='submit-updated-members' class="btn btn-primary" disabled='true'>Add members</button></form>
      </div>
    </div>
  </div>
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.dev.js'></script>
<script>
var fileAttached = false;
    var element = function(id){
        return document.getElementById(id);
    }
    var convDetails = {initial:true,sender:false};
    
    var messages = element('messages');
    var textarea = element('textarea');
    var sidebar = element('chat-sidebar');
    var uid = <?php echo "'" . $_SESSION['accountId'] . "'"?>;
    const token = '<?php echo $token?>';
    var convId;
    var currentDay = ''
    var currentMonth=''
    var currentYear=''
    // var socket = io.connect('http://127.0.0.1:4001');
    var socket = io.connect('https://edconnect.ie:4001');
    var roomName;
    if(socket !== undefined){
        console.log('connected');

        socket.emit('verification',{uid:uid,token:token});
        
        socket.on('conversations',function(data){
            console.log('hit conversation')
            for(let convIndex = 0;convIndex < data.length;convIndex++){
                conversation = document.createElement('div');
                convContainer = document.createElement('div');
                settingBtn = document.createElement('input');
                conversation.setAttribute('onmouseover','showChatGroupSettingIcon(event)');
                settingBtn.setAttribute('type','image');
                settingBtn.setAttribute('src','images/settings_icon.png');
                settingBtn.setAttribute('data-toggle','modal');
                settingBtn.setAttribute('width','20px');
                settingBtn.setAttribute('data-target','#chatGroupSettingModal');
                settingBtn.setAttribute('onclick','showChatGroupSettings("' + data[convIndex]._id + '")')
                settingBtn.style.display = 'none';
                if(data[convIndex].name == '<direct-conversation>'){
                    convType = 'direct';
                    if(data[convIndex].memberNames[0] == '<?php echo $_SESSION['name']?>'){
                        conversation.innerText = data[convIndex].memberNames[1];
                    }
                    else{
                        conversation.innerText = data[convIndex].memberNames[0]; 
                    }
                }
                else{
                    convType = 'group';
                    conversation.innerText = data[convIndex].name; 
                }
                conversation.setAttribute('onclick',' getConversation(this,"' + data[convIndex]._id + '")')
                
                if(convIndex == 0){
                    roomName = data[convIndex]._id;
                    conversation.setAttribute('class', 'conversation-active conversation d-inline')
                    console.log(roomName)
                    socket.emit('create', roomName);
                }
                else{
                    conversation.setAttribute('class', 'conversation d-inline')
                }
                convContainer.appendChild(conversation)
                if(convType == 'group'){
                    convContainer.appendChild(settingBtn)
                }
                sidebar.appendChild(convContainer);
            }
        })

        socket.on('removed',function(id){
            console.log('hit removed');
            messageContainer = document.getElementById('message-cont' + id);
            messageContainer.innerHTML = '<div class="message-container"><span class="removed-message">Message removed</span></div>';
            messageContainer.removeAttribute('id')
            messageContainer.removeAttribute('onmouseleave');
        })
        
        socket.on('output',function(data){
            console.log('hit output');
            if(data.create == true){
                messages.innerHTML = ''
            }
            convId = data.convId
            document.getElementsByName('fileFolderId')[0].value = convId;
            currentScrollHeight = Math.floor((document.getElementById('chat-main').scrollTop + document.getElementById('chat-main').clientHeight)/10);
            fullScrollHeight = Math.floor(document.getElementById('chat-main').scrollHeight/10);
            for(var x= (data.messages.length -1);x >=0;x--){
                mes = document.createElement('div')
                date = document.createElement('div')
                timestamp = new Date(data.messages[x].timestamp * 1000)
                today = new Date();
                timestampDay = timestamp.getDate();
                timestampMonth = timestamp.getMonth();
                timestampYear = timestamp.getFullYear();
                if(timestampYear != currentYear || timestampMonth != currentMonth || timestampDay != currentDay){
                    if(timestampYear == today.getFullYear() && timestampMonth == today.getMonth() && timestampDay == today.getDate()){
                        date.innerHTML = '<div class="chat-date">Today</div>';
                        console.log('<div class="chat-date">Today</div>')
                    }
                    else{
                        date.innerHTML = '<div class="chat-date">' + timestampDay + '/' + timestampMonth + '/' + timestampMonth + '</div>';
                    }
                    currentDay = timestampDay
                    currentMonth = timestampMonth
                    currentYear = timestampYear
                    messages.appendChild(date)
                }
                if(data.messages[x].hasOwnProperty('removed')){
                    mes.innerHTML = '<div class="message-container"><span class="removed-message">Message removed</span></div>'
                    messages.appendChild(mes)
                    continue
                }
                if(data.messages[x].type == 'img'){
                    message = '<img class="file" id="file' + x + '" data-file="conversation_files/' + data.messages[x].conversationId + '/' + data.messages[x].message + '" style="width:100%">';
                }
                else if(data.messages[x].type == 'file'){
                    message = '<a class="file" id="file' + x + '" data-file="conversation_files/' + data.messages[x].conversationId + '/' + data.messages[x].message + '" target="_blank">' + data.messages[x].message + '</a>';
                }
                else{
                    message = data.messages[x].message;
                }
                if(uid == data.messages[x].uid){
                    convDetails.sender = true;
                    mes.innerHTML = '<div class="message-container" id="message-cont' + data.messages[x]._id + '"><span class="message-sender-active">' + data.messages[x].name + ':</span><br><?php echo ($_SESSION['accountType'] == 'staff')?'<span class="chat-delete-icon-active chat-delete-icon" id="delete\' + data.messages[x]._id + \'" onclick=\\\'deleteMessage("\' + data.messages[x]._id + \'","' .  $_SESSION['accountId'] . '")\\\'><img src="images/delete.svg"></span>':'';?><span class="message-active message" onmouseover=showDeleteChat("' + data.messages[x]._id + '")>' + message + '</span><span class="chat-time-active chat-time">'+ showChatTime(timestamp.getHours() ,timestamp.getMinutes())
                     + '</span></div>';
                }
                else{
                    mes.innerHTML = '<div class="message-container" id="message-cont' + data.messages[x]._id + '"><span class="message-sender">' + data.messages[x].name + ':</span><br><?php echo ($_SESSION['accountType'] == 'staff')?'<span class="chat-delete-icon" id="delete\' + data.messages[x]._id + \'" onclick=\\\'deleteMessage("\' + data.messages[x]._id + \'","' .  $_SESSION['accountId'] . '")\\\'><img src="images/delete.svg"></span>':'';?><span class="message" onmouseover=showDeleteChat("' + data.messages[x]._id + '")>' + message + '</span><span class="chat-time">'+ showChatTime(timestamp.getHours() ,timestamp.getMinutes()) + '</span></div>';
                }
                messages.appendChild(mes)
            }
            if(convDetails.initial == true || convDetails.sender == true || currentScrollHeight == fullScrollHeight){
            document.getElementById('chat-main').scrollTo(0,document.getElementById('chat-main').scrollHeight);
            console.log(convDetails.initial)
            console.log("scrolled")
            }
            if(convDetails.initial == false && convDetails.sender == false && currentScrollHeight != fullScrollHeight){
                document.getElementById('new-mes').style.display = 'inline-block';
            }
            console.log('current:' + Math.floor((document.getElementById('chat-main').scrollTop + document.getElementById('chat-main').clientHeight)/10))
            console.log('height:' + Math.floor(document.getElementById('chat-main').scrollHeight/10))
            convDetails.initial = false;
            convDetails.sender = false;
            decryptFiles();
        })

        function sendMessage(){
            if(textarea.value!=''){
            socket.emit('input',{
                    uid:'<?php echo $_SESSION['accountId'] ?>',
                    name:'<?php echo $_SESSION['name'] ?>',
                    message:textarea.value,
                    convId:convId,
                    token:'<?php echo $token ?>',
                    timestamp:Math.round((new Date().getTime())/1000),
                    type:'text'
                }) 
            textarea.value='';
            }
            console.log(document.getElementById('fileForm'))
            if(fileAttached == true){
                document.getElementById("myForm").submit();
                $('#fileForm').submit()
            }
        }

        textarea.addEventListener('keydown',function(event){
            if(event.which === 13){
                sendMessage();
                event.preventDefault();
            }
        })

        function showchatGroupMembers(chatId){
            socket.emit('reqGroupMemberList',{
                uid:'<?php echo $_SESSION['accountId'] ?>',
                convId:chatId,
                token:'<?php echo $token ?>'
            })
        }
        socket.on('groupMemberList',function(data,message = false){
            console.log(data.members[0].members);
            members = data.members[0].members;
            memberNames = data.members[0].memberNames;
            memberTable = '';
            for(i in members){
                console.log('MEMBER:',  memberNames[i].replace("'","\'"))
                memberTable += '<tr><td class="text-left" data-id="' + members[i] + '">' + memberNames[i] + '</td><?php echo ($_SESSION['accountType'] == 'staff')?"<td><button type=\"button\" onclick=\'deleteChatMember(\"' + memberNames[i].replace(\"'\",\"&apos;\") + '\", \"' + members[i] + '\")\'>remove</button></td>":"";?></tr>';
            }
            table = document.createElement('table')
            table.setAttribute('class','currentMemberTable')
            table.innerHTML = memberTable;
            console.log(table)
            if(message != false){
                table.prepend(message[0])
            }
            document.getElementsByClassName('modal-body')[1].appendChild(table)
            console.log(document.getElementsByClassName('modal-body')[1])
        })

        function confirmRemoveChatMember(id){
            messageCheck = document.getElementById('removeUsersMessage').checked
            socket.emit('removeMember',{convId:document.getElementById('convIdValue').value,removeMessage:messageCheck,id:id});
            socket.on('removeStatus',function(data){
                console.log('nodes:', document.getElementsByClassName('modal-body')[1].childNodes)
                pNode = document.getElementsByClassName('modal-body')[1];
                messageBox = pNode.getElementsByTagName('span')[0];
                memberList = pNode.getElementsByTagName('table')[0];
                pNode.removeChild(messageBox);
                pNode.removeChild(memberList);
                showchatGroupMembers(document.getElementById('convIdValue').value)
            })
        }


        
    }
    function deleteChatMember(name,id){
        memberList = document.getElementsByTagName('table')[0]
        memberModal = document.getElementsByClassName('modal-body')[1]
        memberList.style.display = 'none';
        deleteMemberBody = '<span>Are you sure you want to remove <b>' + name + '</b> access to this chat<br><input type="checkbox" id="removeUsersMessage"> check to remove all messages sent by this user also<br><button class="btn btn-primary" type="button" onclick="confirmRemoveChatMember(\'' + id + '\')">Remove</button><button type="button" class="btn btn-secondary" onclick="document.getElementsByTagName(\'table\')[0].style.display = \'block\';document.getElementsByClassName(\'modal-body\')[1].removeChild(document.getElementsByClassName(\'modal-body\')[1].lastChild)">cancel</button></span>'
        deleteMemberBody = $(deleteMemberBody)
        memberModal.appendChild(deleteMemberBody[0])
        console.log('NODES:', document.getElementsByClassName('modal-body')[1].childNodes)
    }

    function scrollBottom(){
        document.getElementById('chat-main').scrollTo(0,document.getElementById('chat-main').scrollHeight);
        document.getElementById('new-mes').style.display = 'none';
    }

    document.getElementById('chat-main').addEventListener("scroll", function(){
        let currentScrollHeight = Math.floor((document.getElementById('chat-main').scrollTop + document.getElementById('chat-main').clientHeight)/10);
        let fullScrollHeight = Math.floor(document.getElementById('chat-main').scrollHeight/10);
        if(currentScrollHeight == fullScrollHeight){
            document.getElementById('new-mes').style.display = 'none';
        }
            
    });

    function getConversation(element,newConvId){
        currentActive = document.getElementsByClassName('conversation-active')[0]
        currentActive.setAttribute('class', 'conversation');
        element.setAttribute('class','conversation-active conversation')
       console.log('room:',roomName)
        convDetails.initial = true;
        socket.emit('change', {oldRoom:roomName,newRoom:newConvId});
        convId = newConvId;
        roomName = convId;
        messages.innerHTML = "";
        document.getElementsByName('fileFolderId')[0].value = newConvId;
    }
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    console.log($(window).height())
    chatHeight = ($(window).height() / 100) * 80
    $("#chat-main").css({
         height:chatHeight +'px'
    });

    $('#chatGroupSettingModal').on('hidden.bs.modal', function () {
        modalBody = document.getElementsByClassName('modal-body')[1]
        if(modalBody.lastChild.nodeName == 'SPAN'){
            modalBody.removeChild(modalBody.lastChild)
        }
        clearUpdateModal()
});



function decryptFiles(){
    let files = document.getElementsByClassName('file');
    for(let i = 0;i < files.length;i++){
        let id = files[i].getAttribute('id');
        let file = files[i].getAttribute('data-file');
        decryptFile(file,id);
    }
}

</script>
</body>
</html>

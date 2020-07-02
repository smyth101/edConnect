const mongo = require('mongodb').MongoClient;
var ObjectId = require('mongodb').ObjectID;
var http = require('http')
// var express = require('express')
var https = require('https')
var fs = require('fs')

var app = express();

//var server = http.createServer(app).listen(4001)

var server = https.createServer({
    key: fs.readFileSync('/etc/letsencrypt/live/edconnect.ie/privkey.pem'),
    cert: fs.readFileSync('/etc/letsencrypt/live/edconnect.ie/cert.pem')
},app).listen(4001)

const client = require('socket.io').listen(server).sockets;

var currentConnections = {};

mongo.connect('mongodb://127.0.0.1/chat',function(err, db){
    if(err){
        throw err;
    }
    console.log('connected..');
    client.on('connection',function(socket){
        let chatDb = db.db('chat');
        let conversations = chatDb.collection('conversation'); 
        let chat = chatDb.collection('message');
        let tokens = chatDb.collection('token');


        socket.on('reqGroupMemberList',function(data){
            tokens.find({uid:data.uid,token:data.token}).toArray(function(err,res){
                if(res.length != 1){
                    console.log(res);
                    console.log('invalid users');
                }
                else{
                    conversations.find({members:data.uid,_id:new ObjectId(data.convId)}).toArray(function(err,res){
                        if(err){
                            throw err;
                        }
                        if(res.length != 1){
                            console.log('invalid user');
                        }
                        else{
                            socket.emit('groupMemberList',{members:res,convId:data.convId});
                        }
                    })
                }
            })
   
        })


        socket.on('verification',function(data){
            tokens.find({uid:data.uid,token:data.token}).toArray(function(err,res){
                if(res.length != 1){
                    console.log('invalid user');
                }
                else{
                    currentConnections[socket.id] = {convList : []}
                    console.log( JSON.stringify(currentConnections) );
                    conversations.find({members:data.uid}).limit(50).sort({last_used:-1}).toArray(function(err,res){
                        if(err){
                            throw err;
                        }
                        if(res.length != 0){
                            socket.emit('conversations',res);
                            convId = res[0]._id;
                            res.forEach(e => {
                                currentConnections[socket.id].convList.push(e._id.toString())
                            })
                        }
                    })
                }
            })
            socket.on('disconnect', function() {
                delete currentConnections[socket.id];
                console.log( JSON.stringify(currentConnections) );
            });
            socket.on('create', function(room) {
                if(currentConnections.hasOwnProperty(socket.id) && currentConnections[socket.id].convList.includes(room)){
                    console.log('created:',room)
                    socket.join(room);
                    chat.find({conversationId:convId.toString()}).limit(100).sort({timestamp:-1}).toArray(function(err,res){
                        if(err){
                            throw err;
                        }
                        client.in(room).emit('output',{messages:res,convId:room,create:true});
                    })
                }
            
                socket.on('change',function(data){
                    if(currentConnections.hasOwnProperty(socket.id) && currentConnections[socket.id].convList.includes(room)){
                        if(data.oldRoom == data.newRoom){
                            console.log('same room');
                            room = data.newRoom
                            chat.find({conversationId:room.toString()}).limit(100).sort({timestamp:-1}).toArray(function(err,res){
                                if(err){
                                    throw err;
                                }
                                client.in(room).emit('output',{messages:res,convId:room,create:true});
                                console.log('ouptput emitted')
                            })
                        }
                        else{
                            console.log('left:',data.oldRoom)
                            console.log('joined:',data.newRoom)
                            socket.leave(data.oldRoom);
                            socket.join(data.newRoom)
                            room = data.newRoom
                            chat.find({conversationId:room.toString()}).limit(100).sort({timestamp:-1}).toArray(function(err,res){
                                if(err){
                                    throw err;
                                }
                                client.in(room).emit('output',{messages:res,convId:room,create:true});
                                console.log('ouptput emitted')
                            })
                        }
                    }
                    
                })

                socket.on('input',function(data){
                    let message = data.message;
                    let convId = data.convId;
                    let name = data.name;
                    let uid = data.uid;
                    let timestamp = data.timestamp;
                    let type = data.type;
                    tokens.find({uid:uid,token:data.token}).toArray(function(err,res){
                        if(res.length != 1){
                            console.log('invalid user here');
                        }
                        else{
                            x = data.convId;
                            conversations.find({_id:new ObjectId(x)}).toArray(function(err,res){
                                if(err){
                                    throw err;
                                }
                                console.log('this:',res)
                                if(res.length != 1 || !currentConnections[socket.id].convList.includes(convId)){
                                    console.log('invalid user there');
                                }
                                else{
                                    chat.insertOne({name: name,uid: uid,message: message, conversationId: convId,timestamp:timestamp,type: type},function(){
                                        conversations.updateOne({_id:new ObjectId(convId)},{$set:{last_used:timestamp}})
                                        client.in(room).emit('output',{messages:[data],convId:convId,create:false});
                                    })
                                }
                            })
                        }
                    })
                })
            });
        })

        socket.on('remove',function(data){
            chat.updateOne({_id:new ObjectId(data.id)},{$set:{removed:true,removed_by:data.remover}},function(err,res){
                if(err){
                    throw err
                }
                client.emit('removed',data.id)
            });

        })

        socket.on('removeMember',function(data){
            if(currentConnections[socket.id].convList.includes(data.convId)){
                conversations.find({_id:new ObjectId(data.convId)}).toArray(function(err,res){
                    index = res[0].members.indexOf(data.id)
                    currentMembers = res[0].members
                    if(currentMembers.length > 2){
                        currentMembers.splice(index,1)
                        currentNames = res[0].memberNames
                        currentNames.splice(index,1)
                        conversations.updateOne({_id:new ObjectId(data.convId)},{$set:{members:currentMembers,memberNames:currentNames}},function(err,res){
                            if(err){
                                throw err
                            }
                            console.log('checked',data.messageCheck)
                            if(data.removeMessage == false){
                                client.emit('removeStatus',{message : 'Member successfully removed.',status:'success'})
                            }
                            else{
                                chat.updateMany({conversationId:data.convId,uid:data.id},{$set:{removed:true}},function(err,res){
                                    if(err){
                                        throw err
                                    }
                                    client.emit('removeStatus',{message : 'Member and messages successfully removed.',status:'success'})
                                })
                            }
                        });
                    }
                    else{
                        client.emit('removeStatus',{message : 'minimimum 2 members required in a group chat.',status:'danger'})
                    }
                })
    
            }
            else{
                console.log('invalid user');
            }
        })
      
    })
});

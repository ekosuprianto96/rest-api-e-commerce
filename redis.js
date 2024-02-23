var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');
var redisAdapter = require('socket.io-redis');
console.log("server being working to connect.. please be patient!!");
 
server.listen(8890);
io.adapter(redisAdapter({ host: '10.101.1.2', port: 6379 }));
io.on('connection', function (socket) {
 
  console.log("new client connected");
  var redisClient = redis.createClient();
  redisClient.subscribe('message');
 
  redisClient.on("message", function(channel, message) {
    console.log("mew message in queue "+ message + "channel");
    socket.emit(channel, message);
  });
 
  socket.on('disconnect', function() {
    redisClient.quit();
  });
 
});

/*
const io = require('socket.io')(3000);
const redisAdapter = require('socket.io-redis');
io.adapter(redisAdapter({ host: 'localhost', port: 6379 }));

const io = require('socket.io')(3000);
const redisAdapter = require('socket.io-redis');
io.adapter(redisAdapter({ host: 'localhost', port: 6379 }));
 */
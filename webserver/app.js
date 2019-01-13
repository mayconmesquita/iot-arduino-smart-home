function getDateTime() {
    var date = new Date();

    var hour = date.getHours();
    hour = (hour < 10 ? "0" : "") + hour;

    var min  = date.getMinutes();
    min = (min < 10 ? "0" : "") + min;

    var sec  = date.getSeconds();
    sec = (sec < 10 ? "0" : "") + sec;

    var year = date.getFullYear();

    var month = date.getMonth() + 1;
    month = (month < 10 ? "0" : "") + month;

    var day  = date.getDate();
    day = (day < 10 ? "0" : "") + day;

    return hour + ":" + min + ":" + sec + ":" + day + ":" + month + ":" + year;
}

var WebSocketServer = require('ws').Server, 
wss = new WebSocketServer({ port: 80, host: "put_your_vps_ip_here" });

wss.on('connection', function(ws) {
    ws.on('message', function(message) {
        if(message == 'time') ws.send(getDateTime());

        console.log('%s', message);
        ws.send(message);
        for(var i in wss.clients) wss.clients[i].send(message);
    });
});
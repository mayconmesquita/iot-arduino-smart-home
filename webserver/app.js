const getServerTime = () => {
    let date = new Date();

    let hour = date.getHours();
    hour = (hour < 10 ? '0' : "") + hour;

    let min  = date.getMinutes();
    min = (min < 10 ? '0' : "") + min;

    let sec  = date.getSeconds();
    sec = (sec < 10 ? '0' : "") + sec;

    let year = date.getFullYear();

    let month = date.getMonth() + 1;
    month = (month < 10 ? '0' : "") + month;

    let day  = date.getDate();
    day = (day < 10 ? '0' : '') + day;

    return hour + ':' + min + ':' + sec + ':' + day + ':' + month + ':' + year;
};

let WebSocketServer = require('ws').Server;
let wss = new WebSocketServer({ port: 8080, host: '127.0.0.1' });

wss.on('connection', (ws) => {
    ws.on('message', (message) => {
        if (message == 'server_time') ws.send(getServerTime());

        console.log('%s', message); // debug
        
        ws.send(message);
        
        for (var i in wss.clients) {
            wss.clients[i].send(message);
        }
    });
});

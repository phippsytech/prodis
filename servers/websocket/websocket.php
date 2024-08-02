
<?php

require '/var/www/prodis/init.php';



/*
    TODO: When we hit the concurrent connection limit, look at this article https://github.com/ratchetphp/Ratchet/issues/300
*/

/**
 * NOTE: Port 8443 needs to be open on the server!  
 * sudo ufw allow 8443/tcp
 */


$DOMAIN = WEBSOCKET_DOMAIN;
$WEBSOCKET_PORT = 443;
$unix_socket_file = '/tmp/ndismate.sock'; 

## WEB SOCKET
$websocketServer = new \NDISmate\WebsocketServer\Server();


// Sets up the websocket server
$webSock = new \React\Socket\Server('0.0.0.0:'.$WEBSOCKET_PORT);

// # This doesn't need ssl any more because that gets handled by traefik #
// $webSock = new \React\Socket\SecureServer($webSock, null, [
//     'local_cert' => '/etc/letsencrypt/live/'.$DOMAIN.'/fullchain.pem',
//     'local_pk' => '/etc/letsencrypt/live/'.$DOMAIN.'/privkey.pem',
//     'allow_self_signed' => FALSE,
//     'verify_peer' => FALSE
// ]);



$webServer = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            $websocketServer
        )
    ),
    $webSock
);

## UNIX SOCKET
unlink($unix_socket_file); // Delete the socket file if it exists

// Sets up the unix socket server for sending data to the websocket server
$unix_socket = new \React\Socket\UnixServer($unix_socket_file);

// Set file permissions using chmod() after the socket is created
$fileMode = 0666;
chmod($unix_socket_file, $fileMode);

// If the server crashes or is stopped, delete the socket file
// src: https://stackoverflow.com/questions/9523240/php-cli-in-windows-handling-ctrl-c-commands
pcntl_signal(SIGINT, function() use ($unix_socket, $unix_socket_file) {
    $unix_socket->close();
    unlink($unix_socket_file);
    exit;
});

$unix_socket->on('connection', function (\React\Socket\ConnectionInterface $connection) use ($websocketServer) {
    $connection->on('data', function ($json) use ($connection, $websocketServer) {

        //print($json);
        $data = json_decode($json, true);

        //print_r($data);
        if ($data !== null) {
            $websocketServer->handleSocketData($json);
            $response = "OK";
        } else {
            $response = "ERR";
        }
        $connection->write($response);
    });
});


## HEARTBEAT
\React\EventLoop\Loop::addPeriodicTimer(60, function() use ($websocketServer) {
    $websocketServer->multicast(["action"=>"hb"]);
});

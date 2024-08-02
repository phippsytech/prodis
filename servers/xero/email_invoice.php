<?php
require '/var/www/prodis/init.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use \NDISmate\CORE\KeyValue;
use \NDISmate\Xero\Session;

$connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASSWORD, RABBITMQ_VHOST);
$channel = $connection->channel();

$channel->queue_declare('ndismate_xero_email_invoice', false, true, false, false);

$channel->basic_consume('ndismate_xero_email_invoice', '', false, false, false, false, function ($message) use ($channel) {
    // Process the message here (e.g., execute some task)
    echo 'Received message: ' . $message->body . "\n";
    $data = unserialize($message->body);

    $results = (new \NDISmate\Models\Invoice\EmailInvoice)($data);

    // Acknowledge the message to remove it from the queue
    $channel->basic_ack($message->delivery_info['delivery_tag']);
    sleep(1); // throttle to 1 message per second
});

echo "Waiting for messages...\n";

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

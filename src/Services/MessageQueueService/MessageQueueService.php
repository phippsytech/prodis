<?php

namespace NDISmate\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageQueueService
{
    static public function sendToQueue($queue, $data)
    {
        $serializedData = serialize($data);

        $connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASSWORD, RABBITMQ_VHOST);
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);

        $message = new AMQPMessage($serializedData);
        $channel->basic_publish($message, '', $queue);

        $channel->close();
        $connection->close();

        return ['http_code' => 200];
    }
}

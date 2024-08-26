<?php

require '/var/www/prodis/init.php';
use RedBeanPHP\R as R;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


$connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASSWORD, RABBITMQ_VHOST);

$channel = $connection->channel();

$channel->queue_declare('service_agreements', false, true, false, false);


function produce($channel) {

    try {
       
        $serviceAgreements = R::getAll(
            'SELECT *
            FROM clientplans
            WHERE is_active = :is_active',
            [':is_active' => 1]
        );

        echo json_encode($serviceAgreements);

        if (is_null($serviceAgreements)) {
            echo 'No service agreements found';
        }

        // loop through the beans and check the end date

        foreach ($serviceAgreements as $serviceAgreement) {
            
            $serializedData = serialize($serviceAgreement);
            $message =  new AMQPMessage($serializedData);

            $channel->basic_publish($message, '', 'service_agreements');

            echo " [x] Sent client with ID " . $serviceAgreement['id'] . " to the queue.\n";
        }

  
    } catch (\Exception $e) {
        print_r($e->getMessage());
    }
}

function consume($channel) {
    $channel->basic_consume('service_agreements', '', false, false, false, false, function ($message) use ($channel) {
        print_r('paolo');
        $data = unserialize($message->body);
        echo $message->body;
        $id = $data['id'];
        $endDate = $data['service_agreement_end_date'];
        $today = date('Y-m-d');

        if ($endDate < $today) {
            // set the service agreement to inactive
            $clientPlan = R::load('clientplans', $serviceAgreement['id']);
            $clientPlan->is_active = 0;
            R::store($clientPlan);
            echo " [x] Updated client with ID " . $clientPlan->id . " to active.\n";
        }

        $channel->basic_ack($message->delivery_info['delivery_tag']);
    });

    while ($channel->is_consuming()) {
        $channel->wait();
    }
}


$action = $argv[1] ?? 'produce';

if ($action === 'produce') {
    produce($channel);
} else if ($action === 'consume') {
    consume($channel);
} else {
    echo "Invalid argument. Use 'produce' or 'consume'.\n";
}

$channel->close();
$connection->close();
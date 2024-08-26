<?php

namespace NDISmate\Models\Participant\ServiceAgreement;

require '/var/www/prodis/init.php';
use RedBeanPHP\R as R;
use PhpAmqpLib\Connection\AMQPStreamConnection;


$connection = new AMQPStreamConnection(RABBITMQ_HOST, RABBITMQ_PORT, RABBITMQ_USER, RABBITMQ_PASSWORD, RABBITMQ_VHOST);

$channel = $connection->channel();

$channel->queue_declare('service_agreements', false, true, false, false);

$channel->basic_consume('service_agreements', '', false, false, false, false, function ($message) use ($channel) {
    
    try {

        $data = unserialize($message->body);
        // echo $message->body;
        $id = $data['id'];
        $endDate = $data['service_agreement_end_date'];
        $today = date('Y-m-d');

        echo($data['service_agreement_end_date']);

        if ($endDate >= $today) {

            R::begin();
            // set the service agreement to inactive
            $clientPlan = R::load('clientplans', $data['id']);
            $clientPlan->is_active = 0;
            R::store($clientPlan);
            echo " [x] Updated client with ID " . $clientPlan['id'] . " to active.\n";

            R::rollback();
        }

        $channel->basic_ack($message->delivery_info['delivery_tag']);
    

    } catch (\Exception $e) {
        print_r($e->getMessage());
    }

});
        

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
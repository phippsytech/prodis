<?php

require '/var/www/prodis/init.php';
use RedBeanPHP\R as R;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use NDISmate\Services\MessageQueueService;


function produce() {

    try {
       
        $serviceAgreements = R::getAll(
            'SELECT *
            FROM clientplans
            WHERE is_active = :is_active',
            [':is_active' => 1]
        );

        
        if (is_null($serviceAgreements)) {
            echo 'No service agreements found';
        }

        // loop through the beans and check the end date

        foreach ($serviceAgreements as $serviceAgreement) {
            
            $serializedData = serialize($serviceAgreement);

            $results[] = MessageQueueService::sendToQueue('service_agreements',$serviceAgreement);

            echo " [x] Sent client with ID " . $serviceAgreement['id'] . " to the queue.\n";

        }

  
    } catch (\Exception $e) {
        print_r($e->getMessage());
    }
}


$action = $argv[1] ?? 'produce';

if ($action === 'produce') {
    produce();
} else {
    echo "Invalid argument. Use 'produce' or 'consume'.\n";
}

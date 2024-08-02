<?php

require __DIR__ . '/../../init.php';

use React\EventLoop\Factory;
use React\ChildProcess\Process;
use React\Promise\Promise;
use RedBeanPHP\R;

$loop = Factory::create();


/*
    CREATE TABLE textmessages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        to_phone VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        send_at DATETIME NOT NULL,
        sent_at DATETIME NULL DEFAULT NULL
    );
 */


function storeMessage(){

/*
    This is the approach that should be taken to store messages in the database 
    and send them at the correct time.
*/

    // Assume this is the user's selected time in their local timezone (e.g., received from a frontend application)
    $userTime = '2023-08-06 10:00:00';
    $userTimezone = 'Australia/Sydney';  // This should be fetched from the user's profile or settings

    // Convert the user's time to UTC
    $datetime = new DateTime($userTime, new DateTimeZone($userTimezone));
    $datetime->setTimezone(new DateTimeZone('UTC'));
    $utcTime = $datetime->format('Y-m-d H:i:s');

    // Store $utcTime in the database
}



// This function checks for messages that are due to be sent
function checkForMessagesToSend($loop) {
    return new Promise(function ($resolve, $reject) use ($loop) {
        $now = new DateTime();

        // Using RedBeanPHP to fetch the messages
        $messages = R::find('textmessages', ' send_at <= ? AND sent_at IS NULL', [$now->format('Y-m-d H:i:s')]);
        foreach ($messages as $message) {
            // Send the SMS using your sendSMS function
            sendSMS(["to" => $message->to_phone, "message" => $message->message]);

            // Update the sent_at column after sending
            $message->sent_at = $now->format('Y-m-d H:i:s');
            R::store($message);
        }

        $resolve();
    });
}

// Set a timer to check for messages to send every minute
$loop->addPeriodicTimer(60, function () use ($loop) {
    checkForMessagesToSend($loop)->otherwise(function ($e) {
        echo "Error: " . $e->getMessage() . PHP_EOL;
    });
});

$loop->run();

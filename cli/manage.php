<?php

require ('../init.php');

use Symfony\Component\Console\Application;

// Create a new Symfony Console Application instance
$application = new Application();

// Register your commands
$commands = [
    NDISmate\Console\Commands\SayHelloCommand::class,
    // Add more commands here
];

foreach ($commands as $command) {
    $application->add(new $command());
}

$application->run();

<?php

require ('../vendor/autoload.php');

use Hashids\Hashids;

define('HASH_SALT', 'NDISmate');

$hashids = new Hashids(HASH_SALT, 8);

$participant_hash = $hashids->encode([1, 332]);

$path = ltrim($_SERVER['REQUEST_URI'], '/');

$decoded_array = $hashids->decode($path);

if (!empty($decoded_array) && is_array($decoded_array) && count($decoded_array) == 2) {
    $provider_id = $decoded_array[0];
    $participant_id = $decoded_array[1];

    $link = sprintf('https://%s/#/clients/%d/forms', 'crm.crystelcare.com.au', $participant_id);

    // Redirect to the actual link
    header('Location: ' . $link);
    exit;
} else {
    header('HTTP/1.0 404 Not Found');
}
?>
<h1>404 Not Found</h1>
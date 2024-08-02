<?php

/**
 * Mapbox SearchAddress Proxy
 *
 * https://account.mapbox.com/
 */

namespace NDISmate\Services\GeoService;

class SearchAddress
{
    function __invoke($data)
    {
        // Get the current date and time to the hour in YYYYMMDDHH format as seed
        $currentDateAndTime = date('YmdH');

        // Generate a UUID using the current date and time as the seed
        $session_token = $this->generateSeededUuid($currentDateAndTime);

        // Mapbox Geocoding API URL
        // $url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/';
        $url = 'https://api.mapbox.com/search/searchbox/v1/suggest?';
        $params = [
            'q' => urlencode($data['query']),
            'country' => 'AU',
            'types' => 'address',
            'limit' => 5,
            'access_token' => MAPBOX_ACCESS_TOKEN,
            'session_token' => $session_token,  // uniqid(), // generate a uuid
        ];

        // URL-encode the query
        $parameters = http_build_query($params);

        // Construct the full URL
        $fullUrl = $url . $parameters;  // . '.json?access_token=' . MAPBOX_ACCESS_TOKEN;

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Execute cURL session
        $result = curl_exec($ch);

        // Close cURL session
        curl_close($ch);

        // Return the response
        return json_decode($result, true);
    }

    // Function to generate a pseudo UUID influenced by a seed value
    function generateSeededUuid($seed)
    {
        // Generate a hash of the seed
        $hash = hash('sha256', $seed);

        // Format the hash as a UUID (version 4 like format, not compliant)
        $uuid = substr($hash, 0, 8) . '-'
            . substr($hash, 8, 4) . '-'
            . '4' . substr($hash, 12, 3) . '-'
            . substr($hash, 15, 4) . '-'
            . substr($hash, 19, 12);

        return $uuid;
    }
}

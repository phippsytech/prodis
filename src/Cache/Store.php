<?php
namespace NDISmate\Cache;


class Store {


    function __invoke($cache_type, $data, $expirySeconds = 2592000){ //default 30 days

        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host' => REDIS_HOST,
            'port' => REDIS_PORT
        ]);

        // Prepare cache entry
        $cacheEntry = [
            "cacheType" => $cache_type, // or other types of cache data
            "data" => $data
        ];


        $cacheEntrySerialized = json_encode($cacheEntry);
        $cacheKey = $cache_type; 

        $existingCache = $redis->get($cacheKey);
        
        if (!is_null($existingCache)) {
            $existingCacheData = json_decode($existingCache, true);
            // Update the data as needed
            $existingCacheData['data'] = $data;

            $redis->setex($cacheKey, $expirySeconds, json_encode($existingCacheData));
            
        } else {
            $redis->setex($cacheKey, $expirySeconds, $cacheEntrySerialized);
        }       
    }
}
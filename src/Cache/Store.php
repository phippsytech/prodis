<?php
namespace NDISmate\Cache;


class Store {


    function __invoke($cache_type, $data){

        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host' => REDIS_HOST,
            'port' => REDIS_PORT
        ]);


        // Prepare cache entry
        $cacheEntry = [
            "cacheType" => $cache_type, // or other types of cache data
            "data" => $data,
            "createdAt" => time(),
        ];


        $cacheEntrySerialized = json_encode($cacheEntry);
        $cacheKey = $cache_type; 

        $existingCache = $redis->get($cacheKey);
        
        if (!is_null($existingCache)) {
            $existingCacheData = json_decode($existingCache, true);
            // Update the data as needed
            $existingCacheData['data'] = $data;
            $existingCacheData['createdAt'] = time();
            
            $redis->set($cacheKey, json_encode($existingCacheData));
            $redis->expire($cacheKey, 3600); // Reset the expiration time
        } else {
            $redis->set($cacheKey, $cacheEntrySerialized);
            $redis->expire($cacheKey, 3600); 
        }       
    }
}
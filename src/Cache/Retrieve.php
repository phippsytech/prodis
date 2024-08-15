<?php
namespace NDISmate\Cache;

use \RedBeanPHP\R as R;

class Retrieve {

    function __invoke($cache_type){

        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host' => REDIS_HOST,
            'port' => REDIS_PORT
        ]);

        $cacheEntry = $redis->get($cache_type);
         
        if (!empty($cacheEntry)) {
            $cacheEntry = json_decode($cacheEntry, true);
            return $cacheEntry["data"];
            
        } else {
            return false;
        }
    }


}
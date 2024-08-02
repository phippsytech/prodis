<?php
namespace NDISmate\Cache;

use \RedBeanPHP\R as R;

class Retrieve {

    function __invoke($cache_type, \DateTime $expiry = null){

        $client = new \MongoDB\Client("mongodb://localhost:27017", [
            "username" => MONGODB_USER,
            "password" => MONGODB_PASSWORD,
            "authSource"  => MONGODB_DATABASE
        ]);

        $db = $client->selectDatabase(MONGODB_DATABASE);
        
        $cacheCollection = $db->cache;

        $filter = ['cacheType' => $cache_type];
        $cacheEntry = $cacheCollection->findOne($filter);
        
        if ($cacheEntry) {

            if ($expiry) {

                $cacheEntryDate = $cacheEntry["createdAt"]->toDateTime();

                if ($cacheEntryDate < $expiry) {

                    // Cache entry is older than expiry date
                    return false;

                }

            }

            return $cacheEntry["data"];

        } else {

            // Cache doesn't exist, fetch data
            return false;
            
        }
            
    }


}
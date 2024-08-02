<?php
namespace NDISmate\Cache;


class Store {


    function __invoke($cache_type, $data){

        $client = new \MongoDB\Client("mongodb://localhost:27017", [
            "username" => MONGODB_USER,
            "password" => MONGODB_PASSWORD,
            "authSource"  => MONGODB_DATABASE
        ]);

        $db = $client->selectDatabase(MONGODB_DATABASE);
        
        $cacheCollection = $db->cache;
        
        $cacheEntry = [
            "cacheType" => $cache_type, // or other types of cache data
            "data" => $data,
            "createdAt" => new \MongoDB\BSON\UTCDateTime(),
            // "expiry" => new MongoDB\BSON\UTCDateTime((new DateTime())->add(new DateInterval('PT1H'))->getTimestamp() * 1000) // 1 hour from now
        ];
        
        // Insert or update cache in MongoDB
        $filter = ['cacheType' => $cache_type];
        $options = ['upsert' => true];
        $update = ['$set' => $cacheEntry];
        $cacheCollection->updateOne($filter, $update, $options);
            
    }


}
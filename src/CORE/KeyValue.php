<?php
namespace NDISmate\CORE;

use \RedBeanPHP\R as R;

/**
 * Manage a simple key/value pair store in the database
 */
class KeyValue
{
    static function set($key, $value)
    {
        $keyvalue = R::findOrCreate('keyvalue', ['k' => $key]);
        $keyvalue->v = $value;
        R::store($keyvalue);
    }

    static function get($key)
    {
        $keyvalue = R::findOne('keyvalue', 'k=:key', [':key' => $key]);
        if (is_null($keyvalue))
            return null;
        return $keyvalue->v;
    }

    static function delete($key)
    {
        $keyvalue = R::findOne('keyvalue', 'k=:key', [':key' => $key]);
        R::trash($keyvalue);
    }
}

<?php
namespace NDISmate\CORE;

use \RedBeanPHP\R as R;

class TransactionalDatabase
{
    public static function storeFields($bean, $fields, $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, array_keys($fields))) {
                $bean->{$key} = $value;
            }
        }

        return self::store($bean);
    }

    public static function store($bean)
    {
        R::begin();

        try {
            R::store($bean);
            R::commit();
            return ['http_code' => 200, 'id' => $bean->id];
        } catch (\Exception $e) {
            R::rollback();
            return ['http_code' => 400, 'error_message' => $e->getMessage()];
        }
    }

    public static function trash($bean)
    {
        R::begin();

        try {
            R::trash($bean);
            R::commit();
            return ['http_code' => 200];
        } catch (\Exception $e) {
            R::rollback();
            return ['http_code' => 400, 'error_message' => $e->getMessage()];
        }
    }
}

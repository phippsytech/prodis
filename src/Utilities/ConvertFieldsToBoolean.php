<?php

namespace NDISmate\Utilities;

class ConvertFieldsToBoolean
{
    function __invoke($beans, $fields)
    {
        if (is_array($beans)) {
            foreach ($beans as $key => $bean) {
                $beans[$key] = $this->convertBeanFieldsToBoolean($bean, $fields);
            }
        } else {
            $beans = $this->convertBeanFieldsToBoolean($beans, $fields);
        }

        return $beans;
    }

    function convertBeanFieldsToBoolean($bean, $fields)
    {
        foreach ($fields as $field) {
            if ($bean && $field && isset($bean[$field])) {
                $bean[$field] = ($bean[$field] === null) ? null : (bool) $bean[$field];
            }
        }
        return $bean;
    }
}

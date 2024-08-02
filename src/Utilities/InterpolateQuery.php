<?php

namespace NDISmate\Utilities;

class InterpolateQuery
{
    function __invoke($query, $params)
    {
        $keys = array();

        // build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/' . preg_quote($key) . '/';
            } else {
                $keys[] = '/[?]/';
            }
        }

        $query = preg_replace($keys, $params, $query, 1, $count);

        // check if there were fewer items than expected
        if ($count != count($keys)) {
            return false;
        }

        return $query;
    }
}

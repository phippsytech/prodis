<?php
namespace NDISmate\Models\Service;

use RedBeanPHP\R as R;

class ListServices
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            'SELECT 
                *
            FROM services 
            ORDER BY code'
        );
        if (!count($beans)) {
            throw new \Exception('Not Found', 404);
        } else {
            return $beans;
        }
    }
}

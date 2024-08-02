<?php
namespace NDISmate\Models\Service;

use RedBeanPHP\R as R;

class GetService
{
    public function __invoke($data)
    {
        $service = R::load('services', $data['id']);

        if (!$service) {
            throw new \Exception('Service Not Found', 404);
        } else {
            return $service;
        }
    }
}

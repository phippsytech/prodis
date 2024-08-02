<?php
namespace NDISmate\Models\SupportItem;

use \RedBeanPHP\R as R;

class GetSupportItem
{
    public function __invoke($data)
    {
        $bean = R::load('supportitems', $data['id']);
        return $bean;
    }
}

<?php
namespace NDISmate\Models\Client\Note;

use \RedBeanPHP\R as R;

class GetNote
{
    public function __invoke($data)
    {
        $bean = R::load('clientnotes', $data['id']);
        return $bean;
    }
}

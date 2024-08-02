<?php
namespace NDISmate\Models\Client\Note;

use \RedBeanPHP\R as R;

class ListNotes
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            "SELECT 
                client_id,
                note,
                user_id,
                archived,
                DATE_FORMAT(created, '%Y-%m-%d %H:%i:%s') as created
            FROM clientnotes"
        );
        return $beans;
    }
}

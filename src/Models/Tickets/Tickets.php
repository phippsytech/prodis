<?php

namespace NDISmate\Models;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RedBeanPHP\R;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;

class Tickets
{
    public static function list($page = 1, $itemsPerPage = 10)
    {
        $offset = ($page - 1) * $itemsPerPage;
        $ticketBeans = R::getAll('select * from tickets LIMIT :itemsPerPage OFFSET :offset ', [':itemsPerPage' => $itemsPerPage, ':offset' => $offset]);
        return $ticketBeans;
    }
}

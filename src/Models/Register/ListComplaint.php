<?php

namespace NDISmate\Models\Register;
use \RedBeanPHP\R as R;

class ListComplaint {

    function __invoke($data) {

       $complaints =  R::findAll('feedbacks', 'type = ?', ['complaint']);

       return array_values($complaints);
    }
}
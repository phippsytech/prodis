<?php

namespace NDISmate\Models\Register;
use \RedBeanPHP\R as R;

class ListComplaint {

    function __invoke($data) {

       return  R::findAll('feedbacks', 'type = ?', ['complaint']);
    }
}
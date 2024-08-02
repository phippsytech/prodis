<?php
namespace NDISmate\Models\NDIA;

use \RedBeanPHP\R as R;

class GetNDIAPlanManagerID {

    public function __invoke() {
        $name_exists = R::findOne( 'planmanagers', ' name=:name ',[":name"=>"National Disability Insurance Agency"] );
        return $name_exists->id;
    }
}

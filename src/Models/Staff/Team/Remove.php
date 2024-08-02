<?php
namespace NDISmate\Models\Staff\Team;

use \RedBeanPHP\R as R;


class Remove {

    public function __invoke($data){
     
        try {
            $bean = R::findOne('teams', ' staff_id=:staff_id AND supervisor_id=:supervisor_id ', [
                ":staff_id" => $data['staff_id'],
                ":supervisor_id"=> $data['supervisor_id']
            ]);
            if($bean)R::trash($bean);

            return true;
            
        } catch (\Exception $e) {

            // Throw the exception
            throw new \Exception($e->getMessage());
        }

    }
    
}
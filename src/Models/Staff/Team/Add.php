<?php
namespace NDISmate\Models\Staff\Team;

use \RedBeanPHP\R as R;


class Add {

    public function __invoke($data){
        try {
            $bean = R::findOrCreate('teams', ['staff_id' => $data['staff_id'], 'supervisor_id' => $data['supervisor_id']]);
            R::store($bean);

            return true;

        } catch (\Exception $e) {

            // Throw the exception
            throw new \Exception($e->getMessage());
            
        }
    }
      
}
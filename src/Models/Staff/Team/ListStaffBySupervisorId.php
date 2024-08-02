<?php
namespace NDISmate\Models\Staff\Team;

use \RedBeanPHP\R as R;


class ListStaffBySupervisorId {

    public function __invoke($data){

        try {

            $beans = R::getAll("SELECT staff_id as id, staffs.name from teams join staffs on (staffs.id = teams.staff_id) where teams.supervisor_id=:supervisor_id", [":supervisor_id"=>$data['supervisor_id']]);

            return $beans;

        } catch (\Exception $e) {

            // Throw the exception
            throw new \Exception($e->getMessage());
            
        }

    }
      
}
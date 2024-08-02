<?php
namespace NDISmate\Models\Staff\Team;

use \RedBeanPHP\R as R;


class ListSupervisorsByStaffId {

    public function __invoke($data){

        try {

            $beans = R::getAll("SELECT supervisor_id as id, staffs.name from teams join staffs on (staffs.id = teams.supervisor_id) where teams.staff_id=:staff_id", [":staff_id"=>$data['staff_id']]);

            return $beans;

        } catch (\Exception $e) {

            // Throw the exception
            throw new \Exception($e->getMessage());
            
        }

    }
      
}
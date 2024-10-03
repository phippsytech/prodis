<?php

namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class GetComplaint {

    function __invoke($data)
    {
    
        // $complaint = R::load('complaints', $data['id']);

        // return $complaint;

        return R::getAll(
            "SELECT 
            complaints.id,
            complaint_notified_staffs.staff_id
            FROM complaints 
            LEFT JOIN complaint_notified_staffs cn on (complaint_id = complaints.id )
            WHERE complaints.id = :id",
            [
                ':id' => $data['id']
            ]
            );
    }
}
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
            cns.staff_id as notified_staff_id 
            FROM complaints 
            LEFT JOIN complaintnotifiedstaffs cns ON complaints.id = cns.complaint_id
            WHERE complaints.id = :id
            GROUP BY complaints.id",
            [
                ':id' => $data['id']
            ]
            );
    }
}
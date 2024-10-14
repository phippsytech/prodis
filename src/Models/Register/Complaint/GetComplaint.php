<?php

namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class GetComplaint {

    function __invoke($data)
    {
    
        // $complaint = R::load('complaints', $data['id']);

        // return $complaint;

        $record = R::getRow(
            "SELECT 
                complaints.*,
                GROUP_CONCAT(cns.staff_id) as notified_staffs_id,
                MAX(ccd.name) AS name,
                MAX(ccd.email) AS email,
                MAX(ccd.phone) AS phone
            FROM complaints 
            LEFT JOIN complaintnotifiedstaffs cns ON complaints.id = cns.complaint_id
            LEFT JOIN complainantcontactdetails ccd ON complaints.id = ccd.complaint_id
            WHERE complaints.id = :id
            GROUP BY complaints.id",
            [
                ':id' => $data['id']
            ]
        );

        if ($record) {
            $record['notified_staffs_id'] = $record['notified_staffs_id'] ? explode(',', $record['notified_staffs_id']) : [];
        }

        return $record;
    }
}
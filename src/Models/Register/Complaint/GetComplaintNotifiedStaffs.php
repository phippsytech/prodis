<?php
namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class GetComplaintNotifiedStaffs {
        
    function __invoke($data)
    {
          $record = R::getRow(
            "SELECT 
                cn.complaint_id,
                GROUP_CONCAT(cn.staff_id) as notified_staffs_id
            FROM complaintnotifiedstaffs cn
            WHERE  cn.complaint_id = :id
            GROUP BY cn.complaint_id",
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
<?php

namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class UpdateComplaint {

    function __invoke($data) {

        $complaint = R::load('complaints', $data['id']);

        if (!$complaint) {
            throw new \Exception('Complaint Not Found', 404);
        }

        $notifiedStaffids = $data['notified_staffs_id'];
        unset($data['notified_staffs_id']);

        $complaint->complainant_client_id = $data['complainant_client_id'];
        $complaint->complainant_contact_details_id = $data['complainant_contact_details'];
        $complaint->department = $data['department'];
        $complaint->complaint_type = $data['complaint_type'];
        $complaint->received_via = $data['received_via'];
        $complaint->received_by = $data['received_by'];
        $complaint->details = $data['details'];
        $complaint->outcome_wanted = $data['outcome_wanted'];
        $complaint->is_staff_notified = $data['is_staff_notified'];
        $complaint->complainant_feedback = $data['complainant_feedback'];
        $complaint->continuous_improvement = $data['continuous_improvement'];
        $complaint->date_actioned = $data['date_actioned'];
        $complaint->notified_of_outcome = $data['notified_of_outcome'];
        $complaint->ndis_commission_notified = $data['nids_commission_notified'];
        $complaint->ndis_commission_id = $data['ndis_commission_id'];
        $complaint->recommended_actions = $data['recommended_actions'];
        $complaint->recommended_actions = $data['recommended_actions'];
        $complaint->status = $data['status'];
        $complaint->updated = new \DateTime();


        $complaintId = R::store($complaint);


        
        if (!empty($notifiedStaffids)) {

            foreach ($notifiedStaffids as $notifiedStaffid) {
                $complaintNotifiedStaffs = R::dispense('complaintnotifiedstaffs');

                $complaintNotifiedStaffs->complaint_id = $complaintId;
                $complaintNotifiedStaffs->staff_id = $notifiedStaffid;
    
                R::store($complaintNotifiedStaffs);
               
            }
    
        }


        return $complaintId;
    }
}
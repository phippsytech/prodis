<?php

namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class AddComplaint {

    function __invoke($data) {

        $complaint = R::dispense('complaints');

        $notifiedStaffids = $data['notified_staffs_id'];
        unset($data['notified_staffs_id']);



        $complaintNotifiedStaffs = R::dispense('complaint_notified_staffs');

      

        $complaint->complaint_client_id = $data['complaint_client_id'];
        $complaint->complaint_contact_details_id = $data['complaint_contact_details_id'];
        $complaint->department = $data['department'];
        $complaint->complaint_type = $data['complaint_type'];
        $complaint->received_via = $data['received_via'];
        $complaint->received_by = $data['received_by'];
        $complaint->details = $data['details'];
        $complaint->outcome_wanted = $data['outcome_wanted'];
        $complaint->is_staff_notified = $data['is_staff_notified'];
        $complaint->complainant_feedback = $data['complainant_feedback'];
        $complaint->continuous_improvement = $data['continuous_improvement'];
        $complaint->ndis_commission_notified = $data['nids_commission_notified'];
        $complaint->ndis_commission_id = $data['ndis_commission_id'];
        $complaint->recommended_actions = $data['recommended_actions'];
        $complaint->recommended_actions = $data['recommended_actions'];
        $complaint->created = new \DateTime();
        $complaint->updated = new \DateTime();


        $complaintId = R::store($complaint);


        foreach ($notifiedStaffids as $notifiedStaffid) {

            $complaintNotifiedStaffs->complaint_id = $complaintId;
            $complaintNotifiedStaffs->staff_id = $notifiedStaffid;

            R::store($complaintNotifiedStaffs);
           
        }

      

        return R::find('complaints', $complaintId);
        
    }
}
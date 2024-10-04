<?php

namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class UpdateComplaint {

    function __invoke($data) {

        $id = $data['id'];
        $complaint = R::findOne('complaints', 'id = ?', [$id]);
        
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
        
            $existingStaffs = R::find('complaintnotifiedstaffs', 'complaint_id = ?', [$complaintId]);

            $existingStaffsIds = array_map(function($staff) {
                return $staff->staff_id;
            }, $existingStaffs);
            
            foreach ($existingStaffs as $staff) {
                if (!in_array($staff->staff_id, $notifiedStaffids)) {
                    R::trash($staff);
                }
            }

            foreach ($notifiedStaffids as $newStaffId) {
                if (is_numeric($newStaffId) && !in_array($newStaffId, $existingStaffsIds)) {
                    $complaintnotifiedstaff = R::dispense('complaintnotifiedstaffs');
                    $complaintnotifiedstaff->complaint_id = $complaintId;
                    $complaintnotifiedstaff->staff_id = $newStaffId;
                    R::store($complaintnotifiedstaff); 
                }
            }
            
        }

        


        return $complaintId;
    }
}
<?php

namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class AddComplaint {

    function __invoke($data) {

       try {
            $complaint = R::dispense('complaints');

            $notifiedStaffids = $data['notified_staffs_id'];
            unset($data['notified_staffs_id']);

                  
            $complaint->complainant_client_id = $data['complainant_client_id'];
            $complaint->department = $data['department'];
            $complaint->complaint_type = $data['complaint_type'];
            $complaint->received_via = $data['received_via'];
            $complaint->received_by = $data['received_by'];
            $complaint->details = $data['details'];
            $complaint->outcome_wanted = $data['outcome_wanted'];
            $complaint->is_staff_notified = $data['is_staff_notified'];
            $complaint->complainant_feedback = $data['complainant_feedback'];
            $complaint->complainant_name = $data['complainant_name'];
            $complaint->continuous_improvement = $data['continuous_improvement'];
            $complaint->date_actioned = $data['date_actioned'];
            $complaint->notified_of_outcome = $data['notified_of_outcome'];
            $complaint->ndis_commission_notified = $data['nids_commission_notified'];
            $complaint->ndis_commission_id = $data['ndis_commission_id'];
            $complaint->recommended_actions = $data['recommended_actions'];
            $complaint->investigation_result = $data['investigation_result'];
            $complaint->status = $data['status'];
            $complaint->description = $data['description'];
            $complaint->actions_taken = $data['actions_taken'];
            $complaint->created = new \DateTime();
            $complaint->date_complaint =  $data['date_complaint'];
            $complaint->resolution_date =  $data['resolution_date'];
            $complaint->updated = new \DateTime();


            $complaintId = R::store($complaint);

            if (is_array($notifiedStaffids) && !empty($notifiedStaffids)) {
               
                foreach ($notifiedStaffids as $notifiedStaffid) {
                    $complaintNotifiedStaffs = R::dispense('complaintnotifiedstaffs');

                    $complaintNotifiedStaffs->complaint_id = $complaintId;
                    $complaintNotifiedStaffs->staff_id = (int) $notifiedStaffid;
                 
        
                    R::store($complaintNotifiedStaffs);
                
                }
        
            }

            $contactDetails = R::dispense('complainantcontactdetails');

            $contactDetails->name = $data['name'];
            $contactDetails->email = $data['email'];
            $contactDetails->phone = $data['phone'];
            $contactDetails->complaint_id = $complaintId;
            $contactDetails->created = new \DateTime();
            $contactDetails->updated = new \DateTime();
            
            R::store($contactDetails);  



            return $complaintId;
       } catch (\Exception $e) {
            throw $e;
       }
        
    }
}
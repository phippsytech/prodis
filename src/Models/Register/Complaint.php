<?php
namespace NDISmate\Models\Register;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Complaint extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('complaints'));
        $this->fields = [
            // Used fields
            'complainant_client_id' => [v::optional(v::numericVal())],
            'complaint_type' => [v::optional(v::stringType())],
            'details' => [v::optional(v::stringType())],
            'date_complaint' => [v::dateTime('Y-m-d')],
            'date_actioned' => [v::optional(v::dateTime('Y-m-d'))],
            'complainant_name' => [v::optional(v::stringType())],
            'notified_of_outcome' => [v::optional(v::boolVal())],
            'recommended_actions' => [v::optional(v::stringType())],
            'investigation_result' => [v::optional(v::stringType())],
            'status' => [v::stringType()],
            'resolution_date' => [v::optional(v::dateTime('Y-m-d'))],
            'user_id' => [v::numericVal()],

            // Commented-out fields (unused)
            // 'department' => [v::optional(v::stringType())],
            // 'received_via' => [v::optional(v::stringType())],
            // 'received_by' => [v::optional(v::stringType())],
            // 'outcome_wanted' => [v::optional(v::stringType())],
            // 'is_staff_notified' => [v::optional(v::boolVal())],
            // 'complainant_feedback' => [v::optional(v::stringType())],
            // 'continuous_improvement' => [v::optional(v::stringType())],
            // 'ndis_commission_notified' => [v::optional(v::boolVal())],
            // 'ndis_commission_id' => [v::optional(v::numericVal())],
            // 'description' => [v::stringType()],
            // 'actions_taken' => [v::optional(v::stringType())],
            // 'staffs_id' => [v::optional(v::numericVal())],
            // 'acknowledgement_date' => [v::optional(v::dateTime('Y-m-d'))],
        ];   
    }
    
    public function afterDispense()
    {
        // Set indexes after dispensing the bean
        R::exec("ALTER TABLE complaints ADD INDEX (complainant_client_id)");
        R::exec("ALTER TABLE complaints ADD INDEX (staffs_id)");
        R::exec("ALTER TABLE complaints ADD INDEX (ndis_commission_id)");
    }

    public function create($data) 
    {
        try {
            $complaint = parent::create($data);

            $notifiedStaffids = $data['notified_staffs_id'];
            unset($data['notified_staffs_id']);
                  
            $complaint->complainant_client_id = $data['complainant_client_id'];
            $complaint->complaint_type = $data['complaint_type'];
            $complaint->details = $data['details'];
            $complaint->complainant_name = $data['complainant_name'];
            $complaint->is_staff_notified = $data['is_staff_notified'];
            $complaint->date_actioned = $data['date_actioned'];
            $complaint->notified_of_outcome = $data['notified_of_outcome'];
            $complaint->recommended_actions = $data['recommended_actions'];
            $complaint->investigation_result = $data['investigation_result'];
            $complaint->status = $data['status'];
            $complaint->date_complaint =  $data['date_complaint'];
            $complaint->resolution_date =  $data['resolution_date'];

            $complaintId = R::store($complaint);

            if (isset($notifiedStaffids) && is_array($notifiedStaffids) && !empty($notifiedStaffids)) {
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

    public function update($data) 
    {
        $id = $data['id'];
        $complaint = R::findOne('complaints', 'id = ?', [$id]);
        
        if (!$complaint) {
            throw new \Exception('Complaint Not Found', 404);
        }

        $notifiedStaffids = $data['notified_staffs_id'];
        unset($data['notified_staffs_id']);

        try {
            $complaint->complainant_client_id = $data['complainant_client_id'];
            $complaint->complaint_type = $data['complaint_type'];
            $complaint->details = $data['details'];
            $complaint->complainant_name = $data['complainant_name'];
            $complaint->is_staff_notified = $data['is_staff_notified'];
            $complaint->date_actioned = $data['date_actioned'];
            $complaint->notified_of_outcome = $data['notified_of_outcome'];
            $complaint->recommended_actions = $data['recommended_actions'];
            $complaint->investigation_result = $data['investigation_result'];
            $complaint->status = $data['status'];
            $complaint->date_complaint =  $data['date_complaint'];
            $complaint->resolution_date =  $data['resolution_date'];
            $complaint->actions_taken = $data['actions_taken'];
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

            $existingContact = R::findOne('complainantcontactdetails', 'complaint_id = ?', [$complaintId]);

            if (!$existingContact) {
                $contactDetails = R::dispense('complainantcontactdetails');
                $contactDetails->name = $data['name'];
                $contactDetails->email = $data['email'];
                $contactDetails->phone = $data['phone'];
                $contactDetails->complaint_id = $complaintId;
                $contactDetails->created = new \DateTime();
                $contactDetails->updated = new \DateTime();
                R::store($contactDetails);  
            } else {
                $existingContact->name = $data['name'];
                $existingContact->email = $data['email'];
                $existingContact->phone = $data['phone'];
                $existingContact->updated = new \DateTime();
                R::store($existingContact);  
            }
          
            return $complaintId;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

<?php

namespace NDISmate\Controllers\Register;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\Complaint;
use NDISmate\Models\Register\Complaint\GetComplaint;
use NDISmate\Models\Register\Complaint\ListComplaint;
use NDISmate\Models\Register\Complaint\GetComplaintNotifiedStaffs;

class ComplaintController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'addComplaint' => [new Complaint, 'create', true, []],
            'updateComplaint' => [new Complaint, 'update', true, []],
            'deleteComplaint' => [new Complaint, 'delete', true, []],
            'listComplaints' => [new ListComplaint, null, true, []],
            'getComplaint' => [new GetComplaint, null, true, []],
            'getComplaintNotifiedStaffs' => [new GetComplaintNotifiedStaffs, null, true, []]
        ];
    }
}

<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\Complaint;
use NDISmate\Models\Register\Complaint\GetComplaint;
use NDISmate\Models\Register\Complaint\ListComplaint;
use NDISmate\Models\Register\Complaint\GetComplaintNotifiedStaffs;
// use NDISmate\Models\Register\Complaint\DeleteComplaint;
// use NDISmate\Models\Register\Complaint\AddComplaint;
// use NDISmate\Models\Register\Complaint\UpdateComplaint;
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
            'getComplaintNotifiedStaffs' => [new GetComplaintNotifiedStaffs,null, true, []]

            // 'listComplaints' => [new ListComplaint, null, true, []],
            // 'addComplaint' => [new AddComplaint, null, true, []],
            // 'getComplaint' => [new GetComplaint, null, true, []],
            // 'updateComplaint' => [new UpdateComplaint, null, true, []],
            // 'getComplaintNotifiedStaffs' => [new GetComplaintNotifiedStaffs, null, true, []],
            // 'deleteComplaint' => [new DeleteComplaint, null, true, []],
        ];
    }
}
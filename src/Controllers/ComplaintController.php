<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\Complaint\AddComplaint;
use NDISmate\Models\Register\Complaint\GetComplaint;
use NDISmate\Models\Register\Complaint\ListComplaint;

class ComplaintController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'listComplaints' => [new ListComplaint, null, true, []],
            'addComplaint' => [new AddComplaint, null, true, []],
            'getComplaint' => [new GetComplaint, null, true, []]
        ];
    }
}
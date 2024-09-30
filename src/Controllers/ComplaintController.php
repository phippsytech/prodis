<?php

namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Models\Register\ListComplaint;

class ComplaintController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            'listComplaints' => [new ListComplaint, null, false, []]
        ];
    }
}
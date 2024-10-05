<?php

namespace NDISmate\Models\Register\Complaint;
use \RedBeanPHP\R as R;

class DeleteComplaint {

    function __invoke($data)
    {
        try {
            $complaint = R::findOne('complaints', ' id = :id ', [':id' => $data['id']]);

            if ($complaint) {
                R::trash($complaint);
            }

            return true;

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
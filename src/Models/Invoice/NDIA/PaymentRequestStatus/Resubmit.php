<?php

namespace NDISmate\Models\Invoice\NDIA\PaymentRequestStatus;

use \RedBeanPHP\R as R;

class Resubmit
{
    public function __invoke($data)
    {
        // Query to get all errors for a specific invoice batch
        $query = 'payment_request_id=:payment_request_id';
        $params = [':payment_request_id' => $data['payment_request_id']];

        $beans = R::find('billablendiaerrors', $query, $params);

        foreach ($beans as $bean) {
            R::trash($bean);
        }

        return ['http_code' => 200];
    }
}

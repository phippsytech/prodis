<?php
namespace NDISmate\Models\Invoice;

use \RedBeanPHP\R as R;

class GenerateInvoiceBatch
{
    public function __invoke($obj, $data)
    {
        // create a new invoice batch
        $data['generation_date'] = date('Y-m-d');

        $errors = null;

        if (empty($data['session_ids']))
            $errors[] = 'At least one session id is required';

        if ($errors)
            return ['http_code' => 400, 'error' => $errors];

        $result = $obj->CRUD->create($data);

        $data['invoice_batch'] = $result['result']['id'];

        $query = 'UPDATE timetrackings
        JOIN clients on (clients.id=timetrackings.client_id)
        SET timetrackings.invoice_batch=:invoice_batch
        WHERE timetrackings.invoice_batch is null '
            . (isset($data['session_ids']) ? ' AND timetrackings.id in (' . implode(',', $data['session_ids']) . ')' : '');

        $bean = R::exec(
            $query,
            [
                ':invoice_batch' => $data['invoice_batch']
            ]
        );

        return ['http_code' => 200, 'invoice_batch' => $data['invoice_batch']];
    }
}

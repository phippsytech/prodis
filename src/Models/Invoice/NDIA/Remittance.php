<?php
namespace NDISmate\Models\Invoice\NDIA;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Remittance extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('ndiapaymentremittance', [
            'id' => [v::numericVal()],
            // 'payee_bp' => [v::stringType()],
            // 'z4_no' => [v::stringType()],
            // 'fin_yrs' => [v::stringType()],
            // 'pay_req_num' => [v::stringType()],
            // 'pay_req_doc_date' => [v::stringType()],
            // 'prov_claim_ref' => [v::stringType()],
            // 'item_ref' => [v::stringType()],
            // 'item_qty' => [v::stringType()],
            // 'unit_price' => [v::stringType()],
            // 'amount_claimed' => [v::stringType()],
            // 'amount_paid' => [v::stringType()],
            // 'participant_bp' => [v::stringType()],
            // 'participant_name' => [v::stringType()],
            // 'support_start_date' => [v::stringType()],
            // 'support_end_date' => [v::stringType()],
            // 'service_booking_num' => [v::stringType()],
            // 'bulk_clm_ref' => [v::stringType()],
            // 'claim_type' => [v::stringType()],
            // 'cancel_rsn' => [v::stringType()]
            'payment_request_number' => [v::stringType()],
            'claim_date' => [v::stringType()],
            'supports_delivered_from' => [v::stringType()],
            'supports_delivered_to' => [v::stringType()],
            'invoice_number' => [v::stringType()],
            'billing_code' => [v::stringType()],
            'quantity' => [v::stringType()],
            'unit_price' => [v::stringType()],
            'amount_paid' => [v::stringType()],
            'ndis_number' => [v::stringType()],
            'claim_type' => [v::stringType()],
            'cancel_reason' => [v::stringType()],
            'upload_csv_name' => [v::stringType()],
            'remittance_csv_name' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['uploadRemittance', 'upload', false, ['admin']],
            ['generateInvoicesAndBatchPayments', 'generateInvoicesAndBatchPayments', false, ['admin']],
            ['checkInvoiceNumbers', 'checkInvoiceNumbers', false, ['admin']],
            ['getRemittanceBatchPaymentList', 'getRemittanceBatchPaymentList', false, ['admin']],
            ['updateInvoice', 'updateInvoice', false, ['admin']],
            ['listRemittances', 'getAll', true, []],
            ['getPrototype', 'getPrototype', true, []],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function upload($data)
    {
        return (new \NDISmate\Models\Invoice\NDIA\Remittance\UploadRemittance)($this, $data);
    }

    function generateInvoicesAndBatchPayments($data)
    {
        return (new \NDISmate\Models\Invoice\NDIA\Remittance\GenerateInvoicesAndBatchPayments)();
    }

    function updateInvoice($data)
    {
        return (new \NDISmate\Models\Invoice\NDIA\Remittance\UpdateInvoice)($data);
    }

    function getRemittanceBatchPaymentList()
    {
        $result = (new \NDISmate\Models\Invoice\NDIA\Remittance\GetRemittanceBatchPaymentList)();
        $formattedResult = array_map(function ($item) {
            $date = \DateTime::createFromFormat('Ymd', $item['claim_date']);
            $item['claim_date'] = $date->format('Y-m-d');
            return $item;
        }, $result);
        return ['http_code' => 200, 'result' => $formattedResult];
    }

    function checkInvoiceNumbers($data)
    {
        return (new \NDISmate\Models\Invoice\NDIA\Remittance\CheckInvoiceNumbers)($data);
    }

    function getPrototype($data)
    {
        return (new \NDISmate\Models\Invoice\NDIA\Remittance\GetPrototype)($data);
    }
}

<?php
namespace NDISmate\Models\Invoice\NDIA;

use NDISmate\CORE\BaseModel;
use NDISmate\CORE\CRUD;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class PaymentRequestStatus extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('ndiapaymentrequeststatus', [
            'id' => [v::numericVal()],
            'registration_number' => [v::stringType()],
            'ndis_number' => [v::stringType()],
            'supports_delivered_from' => [v::stringType()],
            'supports_delivered_to' => [v::stringType()],
            'support_number' => [v::stringType()],
            'claim_reference' => [v::stringType()],
            'quantity' => [v::stringType()],
            'unit_price' => [v::stringType()],
            'gst_code' => [v::stringType()],
            'paid_total_amount' => [v::stringType()],
            'payment_request_number' => [v::stringType()],
            'participant_name' => [v::stringType()],
            'capped_price' => [v::stringType()],
            'payment_request_status' => [v::stringType()],
            'error_message' => [v::stringType()],
            'claim_type' => [v::stringType()],
            'cancellation_reason' => [v::stringType()],
            'abn_of_support_provider' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['getPaymentRequestStatus', 'getOne', true, ['accounts']],
            ['listPaymentRequestStatus', 'getAll', true, ['accounts']],
            ['uploadPaymentRequestStatus', 'upload', true, ['accounts']],
            ['getErrorsByInvoiceBatch', 'getErrorsByInvoiceBatch', true, ['accounts']],
            ['processErrors', 'processErrors', true, ['accounts']],
            ['getErrors', 'getErrors', true, ['accounts']],
            ['resubmit', 'resubmit', true, ['accounts']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    /*
     * There are 3 statuses for a payment request:
     *     OPEN
     *     SUCCESSFUL
     *     ERROR
     */

    function upload($data)
    {
        return (new \NDISmate\Models\Invoice\NDIA\PaymentRequestStatus\Upload)($this, $data);
    }

    function getErrorsByInvoiceBatch($data)
    {
        return (new \NDISmate\Models\Invoice\NDIA\PaymentRequestStatus\GetErrorsByInvoiceBatch)($data);
    }

    function processErrors()
    {
        return (new \NDISmate\Models\Invoice\NDIA\PaymentRequestStatus\ProcessErrors)();
    }

    function getErrors()
    {
        return (new \NDISmate\Models\Invoice\NDIA\PaymentRequestStatus\GetErrors)();
    }

    function resubmit($data)
    {
        return (new \NDISmate\Models\Invoice\NDIA\PaymentRequestStatus\Resubmit)($data);
    }
}

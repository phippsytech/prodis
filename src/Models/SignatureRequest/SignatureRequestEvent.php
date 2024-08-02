<?php
namespace NDISmate\Models\SignatureRequest;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class SignatureRequestEvent extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('signaturerequestevents'));
        $this->fields = [
            'signature_request_id' => [v::numericVal()],
            'event' => [v::stringType()],  // pending, sent, viewed, signed
        ];
    }
}

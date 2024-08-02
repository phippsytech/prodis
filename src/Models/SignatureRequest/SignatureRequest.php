<?php
namespace NDISmate\Models;

use NDISmate\CORE\NewCustomModel;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;

class SignatureRequest extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('signaturerequests'));
        $this->fields = [
            'template_id' => [v::numericVal()],
            'title' => [v::stringType()],
            'type' => [v::stringType()],
            'representative_name' => [v::stringType()],
            'participant_name' => [v::stringType()],
            'email' => [v::optional(v::stringType())],
            'url' => [v::optional(v::stringType())],  // signing url
            'signed_document_url' => [v::optional(v::stringType())],  // signed document
            'related_id' => [v::numericVal()],
            'action' => [v::stringType()],
        ];
    }
}

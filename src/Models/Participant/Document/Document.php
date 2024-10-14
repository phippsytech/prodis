<?php

namespace NDISmate\Models\Participant\Document;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Document extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('participantdocuments'));
        $this->fields = [
            'participant_id' => [v::numericVal()],
            'document_id' => [v::numericVal()],
            'details' => [v::optional(v::stringType())],
            'vultr_storage_ref' => [v::optional(v::stringType())],
            'document_date' => [v::optional(v::stringType())]
        ];
    }
}

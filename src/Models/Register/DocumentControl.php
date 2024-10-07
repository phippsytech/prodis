<?php
namespace NDISmate\Models\Register;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class DocumentControl extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('documentcontrols'));
        $this->fields = [
            'document_title' => [v::stringType()],
            'document_number' => [v::stringType()],
            'revision_date' => [v::dateTime('Y-m-d H:i:s')],  
            'version' => [v::stringType()],
            'approver' => [v::optional(v::stringType())],
            'next_review_date' => [v::optional(v::dateTime('Y-m-d H:i:s'))],
            'staffs_id' => [v::numericVal()], //document owner
        ];
    }

    public function afterDispense()
    {
        // Set indexes after dispensing the bean
        R::exec("ALTER TABLE documentcontrols ADD INDEX (staffs_id)");
    }
}
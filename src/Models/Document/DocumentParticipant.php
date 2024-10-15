<?php

namespace NDISmate\Models\Document;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class DocumentParticipant extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('documentparticipants'));
        $this->fields = [
            'participant_id' => [v::numericVal()],
            'document_id' => [v::numericVal()],
        ];
    }


    public function createDocumentParticipant($data) {
        
        $partcipantIds = $data['participant_ids'];
        $created = [];
        foreach ($partcipantIds as $id) {
           $created[] =  $this->create([
                'participant_id' => $id,
                'document_id' => $data['document_id']
            ]);
        }

        return $created;

    }

    public function listByDocumentId($data) {

        $query = "SELECT
                    *
                    FROM
                    documentparticipants dp
                    WHERE dp.document_id = :document_id;
                    ";
        $params = [':document_id' => $data['document_id']];

        $result = R::getAll($query, $params);

        return $result;
    }
}

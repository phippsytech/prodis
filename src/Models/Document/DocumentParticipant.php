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
        $params = ['document_id' => $data['document_id']];

        // Fetch existing records for the given document_id
        $existingRec = R::getAll('SELECT participant_id FROM documentparticipants WHERE document_id = :document_id', $params);

        // Get the list of participant_ids that need to be deleted
        $existingParticipantIds = array_column($existingRec, 'participant_id');
        $toDelete = array_diff($existingParticipantIds, $data['participant_ids']);

        // 1. Delete participants that are not in the new list
        if ($toDelete) {
            foreach ($toDelete as $participantId) {
                $record = R::findOne('documentparticipants', 'document_id = ? AND participant_id = ?', [$data['document_id'], $participantId]);
                if ($record) {
                    R::trash($record); 
                }
            }
        }

        // 2. Add new participants that don't already exist
        $toAdd = array_diff($data['participant_ids'], $existingParticipantIds);
        $created = [];
        foreach ($toAdd as $id) {
        $created[] = $this->create([
                'participant_id' => $id,
                'document_id' => $data['document_id']
            ]);
        }

        // Fetch and return the updated list of participant_ids
        $result = R::getAll('SELECT participant_id FROM documentparticipants WHERE document_id = :document_id', $params);

        return array_column($result, 'participant_id');


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

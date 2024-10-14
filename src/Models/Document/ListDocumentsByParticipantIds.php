<?php

namespace NDISmate\Models\Document;

use Respect\Validation\Validator as v;
use \NDISmate\CORE\CRUD;
use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class ListDocumentsByParticipantIds
{
    function __invoke($data)
    {
        if (!isset($data['participant_ids']) || count($data['participant_ids']) == 0) {
            return ['http_code' => 400, 'error' => 'Invalid participant IDs'];
        }

        // Prepare the participant IDs for the SQL query
        // $participantIds = implode(',', array_fill(0, count($data['participant_ids']), '?'));

        // Create the placeholders for participant IDs
        $placeholders = implode(',', array_fill(0, count($data['participant_ids']), '?'));

        // Prepare the participant IDs as parameters for binding
        $params = $data['participant_ids'];

        $sql = "SELECT 
        s.participant_id,
        s.participant_name,
        c.id AS document_id,
        c.name AS document_name,
        c.years_until_expiry,
        c.collect_from_sil AS requirement_status,
        CASE 
            WHEN c.date_collection_option = 'do_not_collect' THEN 'do_not_collect'
            WHEN c.date_collection_option = 'expires' THEN 'expires'
            ELSE 'issued'
        END AS date_type,
        CASE 
            WHEN c.date_collection_option IN ('do_not_collect', 'expires') THEN NULL
            ELSE sc.document_date
        END AS issue_date,
        CASE 
            WHEN c.date_collection_option = 'expires' THEN sc.document_date
            WHEN c.collect_expiry = 1 AND sc.document_date IS NOT NULL THEN DATE_ADD(sc.document_date, INTERVAL c.years_until_expiry YEAR)
            WHEN c.date_collection_option = 'issued' AND c.years_until_expiry IS NOT NULL AND sc.document_date IS NOT NULL THEN DATE_ADD(sc.document_date, INTERVAL c.years_until_expiry YEAR)
            ELSE NULL
        END AS expiry_date,
        sc.details AS document_details,
        sc.vultr_storage_ref AS file_reference,
        IF(sc.id IS NULL AND c.collect_from_sil = 'required', 'Missing', 'Provided') AS document_status
    FROM 
        (SELECT id as participant_id, name as participant_name FROM clients WHERE id IN ($placeholders)) s
    LEFT JOIN 
    documents c ON c.collect_from_sil = 'required'
        
    LEFT JOIN 
        participantdocuments sc ON c.id = sc.document_id AND sc.participant_id = s.participant_id
    ORDER BY 
        s.participant_name, c.id";

        // Execute the prepared statement with the participant IDs as parameters
        $beans = R::getAll($sql, $params);

        return ['http_code' => 200, 'result' => $beans];
    }
}
// If you want to include optional documents, replace "documents c ON c.collect_from_sil = 'required'" with:
// documents c ON (c.collect_from_sil = 'required' OR (c.collect_from_sil = 'optional' AND EXISTS (SELECT 1 FROM participantdocuments WHERE document_id = c.id AND participant_id = s.participant_id)))

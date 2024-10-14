<?php

namespace NDISmate\Models\Participant\Document;

use \RedBeanPHP\R as R;

class ListMissingDocuments
{
    function __invoke($data)
    {
        $query = <<<HEREDOC
SELECT 
    s.id as participant_id,
    c.id as document_id,
    s.name as name,
    c.name as document
FROM clients s
CROSS JOIN documents c
WHERE NOT EXISTS (
    SELECT 1
    FROM participantdocuments sc
    WHERE sc.participant_id = s.id AND sc.document_id = c.id
)
AND (
    c.collect_from_therapist = 'required'
)
AND s.archived IS NOT TRUE
HEREDOC;

        $beans = R::getAll($query);

        return $beans;
    }
}

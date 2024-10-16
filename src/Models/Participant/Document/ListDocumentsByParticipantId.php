<?php

namespace NDISmate\Models\Participant\Document;

use \RedBeanPHP\R as R;


class ListDocumentsByParticipantId
{

    function __invoke($data)
    {

        
        // collect_from_therapist,
        // collect_from_sil,

        // required, optional, do_not_collect


        try {
            $participanter = R::load("clients", $data['participant_id']);
        
            $participant_groups = json_decode($participanter->groups, true);
            $participant_groups = [];
            // if ($participant_groups == null  || $participant_groups == []) {
            //     return ["http_code" => 200, "result" => []];
            // }


            $newQuery = "
                SELECT 
                    d.id, 
                    d.name, 
                    d.description,
                    d.date_collection_option,
                    pd.expired_at
                FROM documents d
                JOIN documentparticipants dp ON d.id = dp.document_id
                LEFT JOIN participantdocuments pd ON d.id = pd.document_id AND dp.participant_id = pd.participant_id
                WHERE dp.participant_id = :participant_id
                ORDER BY d.name desc

            ";
            
            // $therapist_query = <<<HEREDOC
            //     select 
            //             documents.id,
            //             documents.name,
            //             documents.description,
            //             documents.date_collection_option,
            //             IF(
            //                 documents.collect_from_therapist = 'required',
            //                 TRUE,
            //                 FALSE
            //             ) AS required
            //             from documents
            //             left join participantdocuments on (documents.id = participantdocuments.document_id AND participantdocuments.participant_id = :participant_id)
            //         where 
            //         (documents.collect_from_therapist in ('required', 'optional') and :set_member_therapist = 1)
                    
            //         ORDER BY required desc, documents.name
            //     HEREDOC;
    
            // $sil_query = <<<HEREDOC
            //     select 
            //             documents.id,
            //             documents.name,
            //             documents.description,
            //             documents.date_collection_option,

            //             IF(
            //                 documents.collect_from_sil = 'required',
            //                 TRUE,
            //                 FALSE
            //             ) AS required
            //             from documents
            //             left join participantdocuments on (documents.id = participantdocuments.document_id AND participantdocuments.participant_id = :participant_id)
            //         where 
            //         (documents.collect_from_sil in ('required', 'optional') and :set_member_sil = 1)
                    
            //         ORDER BY required desc, documents.name
            //     HEREDOC;
    
            // $both_query = <<<HEREDOC
            //     select 
            //         documents.id,
            //         documents.name,
            //         documents.description,
            //         documents.date_collection_option,
                  
            //         IF(
            //             documents.collect_from_sil = 'required' OR documents.collect_from_therapist = 'required',
            //             TRUE,
            //             FALSE
            //         ) AS required
            //     from documents
            //     left join participantdocuments on documents.id = participantdocuments.document_id AND participantdocuments.participant_id = :participant_id
            //     where 
            //         (documents.collect_from_sil in ('required', 'optional') and :set_member_sil = 1)
            //         OR (documents.collect_from_therapist in ('required', 'optional') and :set_member_therapist = 1)
                    
            //     ORDER BY required desc, documents.name
            //     HEREDOC;
    
    
            $params = [
                ":participant_id" => $data['participant_id'],
    
            ];
            
            // switch (true) {
            //     case (in_array("sil", $participant_groups) && !in_array("therapist", $participant_groups)):
            //         $params[":set_member_sil"] = in_array("sil", $participant_groups) ? 1 : 0;
            //         $query = $sil_query;
            //         break;
            //     case (!in_array("sil", $participant_groups) && in_array("therapist", $participant_groups)):
            //         $params[":set_member_therapist"] = in_array("therapist", $participant_groups) ? 1 : 0;
            //         $query = $therapist_query;
            //         break;
            //     case (in_array("sil", $participant_groups) && in_array("therapist", $participant_groups)):
            //         $params[":set_member_sil"] = in_array("sil", $participant_groups) ? 1 : 0;
            //         $params[":set_member_therapist"] = in_array("therapist", $participant_groups) ? 1 : 0;
            //         $query = $both_query;
            //         break;
            //     default:
                    
            //         $params[":set_member_sil"] = 1;
            //         $params[":set_member_therapist"] = 1;
            //         $query = $both_query;
            //         break;
            // }
        

            $bean = R::getAll($newQuery, $params);

            return $bean;
    
            foreach ($bean as &$row) {
                $row['required'] = ($row['required'] == 1) ? true : false;
            }
    
            return $bean;
        } catch (\Exception $e) {
            throw $e;
        }        
    }
}

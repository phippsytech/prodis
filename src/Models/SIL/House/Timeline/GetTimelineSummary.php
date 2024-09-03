<?php

namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetTimelineSummary {
    //TODO: test this properly
    function __invoke($data) {

        $sql = "
            SELECT 
                c.participant_id,
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        'date', f.date,
                        'reports', f.reports
                    )
                ) AS dates
            FROM (
                SELECT 
                    t.participant_id,
                    t.created,
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'report_type', JSON_UNQUOTE(JSON_EXTRACT(t.form_data, '$.report_type')),
                            'count', t.count
                        )
                    ) AS reports
                FROM timelines t
                GROUP BY t.participant_id, t.created, JSON_UNQUOTE(JSON_EXTRACT(t.form_data, '$.report_type'))
            ) AS subquery
            JOIN clients c ON c.id = subquery.participant_id
            GROUP BY c.participant_id
            ORDER BY c.participant_id ASC, t.created DESC, JSON_UNQUOTE(JSON_EXTRACT(t.form_data, '$.report_type')) ASC
        ";

        // Execute the query
        $rows = R::getAll($sql);

        // Process the results
        $cursor = [];
        foreach ($rows as $row) {
            $cursor[] = [
                'participant_id' => $row['participant_id'],
                'dates' => json_decode($row['dates'], true)
            ];
        }

        // Return the result
        return JsonResponse::success($cursor);
    }
}
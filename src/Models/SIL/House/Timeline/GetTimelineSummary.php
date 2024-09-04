<?php
namespace NDISmate\Models\SIL\House\Timeline;

use \RedBeanPHP\R as R;

class GetTimelineSummary {

    //TODO: test this properly
    function __invoke($data) {
        
        try {
            // Step 1: Extract report_type from form_data JSON and group by participant_id, created, and report_type, then count the occurrences
            $sql = "
                SELECT 
                    t.participant_id, 
                    c.participant_name,
                    t.created, 
                    JSON_UNQUOTE(JSON_EXTRACT(t.form_data, '$.report_type')) as report_type, 
                    COUNT(*) as count
                FROM timelines t
                JOIN clients c ON t.participant_id = c.id
                GROUP BY t.participant_id, c.participant_name, t.created, report_type
                ORDER BY t.participant_id ASC, t.created DESC
            ";

            $results = R::getAll($sql);

            // Step 2: Process the results to format the final output
            $finalOutput = [];
            $currentParticipantId = null;
            $currentParticipantName = null;
            $currentDates = [];

            foreach ($results as $row) {
                if ($currentParticipantId !== $row['participant_id']) {
                    if ($currentParticipantId !== null) {
                        $finalOutput[] = [
                            'participant_id' => $currentParticipantId,
                            'participant_name' => $currentParticipantName,
                            'dates' => $currentDates
                        ];
                    }
                    $currentParticipantId = $row['participant_id'];
                    $currentParticipantName = $row['participant_name'];
                    $currentDates = [];
                }

                $currentDates[] = [
                    'date' => $row['created'],
                    'reports' => [
                        [
                            'report_type' => $row['report_type'],
                            'count' => $row['count']
                        ]
                    ]
                ];
            }

            // Add the last participant's data
            if ($currentParticipantId !== null) {
                $finalOutput[] = [
                    'participant_id' => $currentParticipantId,
                    'participant_name' => $currentParticipantName,
                    'dates' => $currentDates
                ];
            }
            
            // Output the final grouped data
            return $finalOutput;
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
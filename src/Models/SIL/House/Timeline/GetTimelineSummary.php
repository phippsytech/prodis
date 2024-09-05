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
                c.name as participant_name,
                t.created, 
                JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.report_type')) as report_type, 
                COUNT(*) as count 
            FROM timelines t
            JOIN clients c ON t.participant_id = c.id
            GROUP BY participant_id, t.created, report_type
            ";

            // Step 2: Sort the results by participant_id (ascending) and created (descending)
            $sql .= "
            ORDER BY participant_id ASC, created DESC
            ";

            $results = R::getAll($sql);
            
            // Step 3: Group by participant_id and created to aggregate the report_type and count
            $groupedByDate = [];

            foreach ($results as $row) {
                $participantId = $row['participant_id'];
                $created = $row['created'];
                if (!isset($groupedByDate[$participantId])) {
                    $groupedByDate[$participantId] = [];
                }
                if (!isset($groupedByDate[$participantId][$created])) {
                    $groupedByDate[$participantId][$created] = [];
                }
                $groupedByDate[$participantId][$created][] = [
                    'report_type' => $row['report_type'],
                    'count' => $row['count']
                ];
            }

            // Step 4: Sort again by participant_id (ascending) and created (descending)
            ksort($groupedByDate);
            foreach ($groupedByDate as &$dates) {
                krsort($dates);
            }

            // Step 5: Group by participant_id to aggregate the dates and their reports
            $finalOutput = [];
            foreach ($groupedByDate as $participantId => $dates) {
                $dateReports = [];
                foreach ($dates as $created => $reports) {
                    $dateReports[] = [
                        'date' => $created,
                        'reports' => $reports
                    ];
                }
                $finalOutput[] = [
                    'participant_id' => $participantId,
                    'participant_name' => $results[0]['participant_name'],
                    'dates' => $dateReports
                ];
        }
            
            // Output the final grouped data
            return $finalOutput;
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
<?php
namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetAvailableReportTypes{

    function __invoke($data){

        try {
            
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            $participantId = $data['participant_id'];

            // if date is before 3 OCT 2023, make the date 3 OCT 2023
            if ($startDate < "2023-10-03"){ $startDate = "2023-10-03"; }
            if ($endDate < "2023-10-03"){ $endDate = "2023-10-03"; }


            // Define the SQL query
            $sql = "
                SELECT JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.report_type')) AS report_type, COUNT(*) as count
                FROM timelines
                WHERE created >= :start_date
                    AND created <= :end_date
                    AND participant_id = :participant_id
                GROUP BY report_type
            ";

            // Execute the query and fetch the results
            $rows = R::getAll($sql, [
                ':start_date' => $startDate,
                ':end_date' => $endDate,
                ':participant_id' => $participantId
            ]);

            return $rows;

            // Process the results as needed
            $result = [];
            foreach ($rows as $row) {
                $result[] = [
                    'report_type' => $row['report_type'],
                    'count' => $row['count']
                ];
            }

            return $result;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
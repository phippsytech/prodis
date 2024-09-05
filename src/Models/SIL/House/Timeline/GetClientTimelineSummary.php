<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \RedBeanPHP\R as R;

class GetClientTimelineSummary {

    function __invoke($data) {
        
       try {
         // Step 1: Filter by participant_id and report_type within JSON colum
        // Define the participant_id and report_type for filtering
        $participantId = 580;
        $reportTypes = ['Incident'];

        $sql = "
            SELECT 
                participant_id, 
                DATE(created) as date, 
                REPLACE(JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.report_type')), '\"', '') as report_type, 
                COUNT(*) as count
            FROM 
                timelines
            WHERE 
                participant_id = ? 
                AND REPLACE(JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.report_type')), '\"', '') IN (".R::genSlots($reportTypes).")
            GROUP BY 
                participant_id, 
                date, 
                report_type
            ORDER BY 
                date DESC
        ";

        // return $sql;

        // Execute the query with the parameters
        $rows = R::getAll($sql, array_merge([$participantId], $reportTypes));
         return $rows;
         // Step 3: Further group by participant_id and date if needed
         $finalGroupedReports = [];
         foreach ($groupedReports as $report) {
             $key = $report['participant_id'] . '_' . $report['created'];
             if (!isset($finalGroupedReports[$key])) {
                 $finalGroupedReports[$key] = [
                     'participant_id' => $report['participant_id'],
                     'created' => $report['created'],
                     'reports' => []
                 ];
             }
             $finalGroupedReports[$key]['reports'][] = [
                 'report_type' => $report['report_type'],
                 'count' => $report['count']
             ];
         }
         
         // Convert finalGroupedReports to array values
         $finalGroupedReports = array_values($finalGroupedReports);
         
         // Output or further process $finalGroupedReports as needed
 
         return $finalGroupedReports;
       } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
       }
    } 
    
}
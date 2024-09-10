<?php

namespace NDISmate\Models\SIL\House\Timeline;

class GetCSVExport {

    function __invoke($data) {
        // Initialize RedBeanPHP
      
        $startDate = $data['start_date'];
        $endDate = $data['end_date'];

        // if date is before 3 OCT 2023, make the date 3 OCT 2023
        if ($startDate < "2023-10-03"){ $startDate = "2023-10-03"; }
        if ($endDate < "2023-10-03"){ $endDate = "2023-10-03"; }

        $reportType = $data['report_type'];
        $participantId = $data['participant_id'];

        // Build the SQL query
        $sql = 'SELECT * FROM timelines WHERE date >= ? AND date <= ? AND report_type = ? AND participant_id = ?';
        $params = [$startDate, $endDate, $reportType, $participantId];

        // Execute the query
        $forms = R::getAll($sql, $params);

        // Process the results as needed
        foreach($cursor as $key => $value){
            if (isset($cursor[$key]['edit_history'])){
                unset($cursor[$key]['edit_history']);
            }
        }

        // Close the RedBeanPHP connection

        $firstRow = reset($cursor);
        $firstRowArray = (array) $firstRow;

        // Open a file in write mode ('w')
        $csvFile = fopen('php://temp', 'w');

        // Output the column headings
        fputcsv($csvFile, array_keys($firstRowArray));

        // Function to convert non-scalar values (like arrays or objects) to strings
        $stringify = function($item) {
            if (is_array($item) || is_object($item)) {
                return json_encode($item);
            } else {
                return $item;
            }
        };

        // Loop through the cursor and output each row
        foreach ($cursor as $row) {
            $rowArray = (array) $row;
            $rowArray = array_map($stringify, $rowArray);
            fputcsv($csvFile, $rowArray);
        }

        // Rewind the file
        rewind($csvFile);

        // Read the contents of the file into a string
        $csvString = stream_get_contents($csvFile);

        // Close the file
        fclose($csvFile);
       
    }
}
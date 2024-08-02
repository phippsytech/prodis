<?php
namespace NDISmate\Models\Staff\Schedule;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class GetScheduleByStaffId
{
    function __invoke($data)
    {
        if (!isset($data['editable']))
            $data['editable'] = false;

        $start_date = date('Y-m-d', strtotime($data['info']['startStr'] . ' -1 day'));
        $end_date = date('Y-m-d', strtotime($data['info']['endStr'] . ' +1 day'));

        if (!empty($data['staff_id'])) {
            $roster = (new \NDISmate\Models\SIL\House\Roster\GetShiftsByStaffId)([
                'staff_id' => $data['staff_id'],
                'start_date' => $start_date,
                // 'start_time'=>"00:00",
                'end_date' => $end_date,
                // 'start_time'=>"00:00"
            ]);
        }

        // return ["http_code"=>200, "result"=>$roster];

        // src of colours: https://sashamaps.net/docs/resources/20-colors/
        // and this search result got me good hits.  https://search.brave.com/search?q=I+need+a+list+of+as+many+visually+different+colours&source=desktop

        $colors = array('#e6194B', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990', '#dcbeff', '#9A6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#a9a9a9');

        // shuffle($colors);

        $bw_colors = array(
            '#666666',
            '#777777',
            '#888888',
            '#999999',
            '#aaaaaa',
            '#bbbbbb',
            '#cccccc',
            '#dddddd',
            '#eeeeee',
            '#ffffff',
        );

        // same colours as the KPI report.
        $colors = [
            '#7c3aed',
            // '#8b5cf6',
            '#4f46e5',
            '#2563eb',
            '#22b8cf',
            '#0891b2',
            '#047857',
            '#10b981',
            '#84cc16',
            '#f59e0b',
            '#fb923c',
            '#ea580c',
            '#ec4899',
            '#d53f8c',
            '#be185d',
        ];

        // Create a mapping between staff members and colors
        $color_map = array();
        foreach ($roster as $bean) {
            $staff_name = $bean['title'];
            if (!array_key_exists($staff_name, $color_map)) {
                $color_map[$staff_name] = array_shift($colors);
            }
        }

        $bw_color_map = array();
        foreach ($roster as $bean) {
            $staff_name = $bean['title'];
            if (!array_key_exists($staff_name, $bw_color_map)) {
                $bw_color_map[$staff_name] = array_shift($bw_colors);
            }
        }

        // Loop through the shift beans and assign a color to each staff member

        foreach ($roster as &$bean) {
            $editable = $data['editable'];
            $bean['backgroundColor'] = $bw_color_map[$bean['title']] . '33';
            $bean['backgroundColor'] = $color_map[$bean['title']] . '33';

            // if( $bean['start_date'] >= $billing_period_start_date && $editable){
            //     $editable = true;
            // }else{
            //     $editable = false;
            // }

            if ($editable) {
                $bean['backgroundColor'] = $color_map[$bean['title']] . 'cc';
            }

            if (is_null($bean['title'])) {
                $bean['title'] = 'Unassigned';
                $bean['backgroundColor'] = '#ff0000';
            }

            $duration = (strtotime($bean['end']) - strtotime($bean['start'])) / 60 / 60;

            $bean['titleHTML'] = $this->convertNameFormat($bean['title']);
            if ($bean['passive']) {
                $bean['titleHTML'] .= "<br /><span style='font-weight:normal'>Passive</span>";
            } else {
                $bean['titleHTML'] .= "<br /><span style='font-weight:normal'>" . $duration . 'hrs</span>';
            }

            if ($bean['do_not_bill'] == 1)
                $bean['titleHTML'] .= "<span style='font-weight:normal; font-style:italic'> (No Bill)</span>";

            $bean['editable'] = $editable;
        }

        return ['http_code' => 200, 'result' => $roster];
    }

    function convertNameFormat($name)
    {
        // Split the name into first name and last name
        $nameParts = explode(' ', $name);

        // Extract the first character of the first name and convert it to uppercase
        $first_name = $nameParts[0];

        // Get the last name intial
        $last_name_initital = strtoupper(substr($nameParts[1], 0, 1));

        // Concatenate the first name and last name initial with a space in between
        $convertedName = $first_name . ' ' . $last_name_initital;

        // Return the converted name
        return $convertedName;
    }
}

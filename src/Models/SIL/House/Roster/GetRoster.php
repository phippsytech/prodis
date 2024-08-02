<?php
namespace NDISmate\Models\SIL\House\Roster;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class GetRoster
{
    function __invoke($data)
    {
        $billing_period_start_date = (string) KeyValue::get('sil_billing_period_start_date');
        if (!$billing_period_start_date) {
            return ['http_code' => 400, 'error_message' => 'Billing period start date is not set.  Cannot retrieve roster.'];
        }

        if (!isset($data['editable']))
            $data['editable'] = false;

        $start_date = date('Y-m-d', strtotime($data['info']['startStr'] . ' -1 day'));
        $end_date = date('Y-m-d', strtotime($data['info']['endStr'] . ' +1 day'));

        if (!empty($data['house_id'])) {
            $roster = (new \NDISmate\Models\SIL\House\Roster\GetShifts)([
                'house_id' => $data['house_id'],
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);
        }

        if (!empty($data['client_id'])) {
            $roster = (new \NDISmate\Models\SIL\House\Roster\GetShifts)([
                'client_id' => $data['client_id'],
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);
        }

        // Define the colors array
        // src of colours: https://sashamaps.net/docs/resources/20-colors/
        // and this search result got me good hits.  https://search.brave.com/search?q=I+need+a+list+of+as+many+visually+different+colours&source=desktop

        $colors = array('#e6194B', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990', '#dcbeff', '#9A6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#a9a9a9');

        // shuffle($colors);

        // Create a mapping between staff members and colors
        $color_map = array();
        foreach ($roster as $bean) {
            $staff_name = $bean['title'];
            if (!array_key_exists($staff_name, $color_map)) {
                $color_map[$staff_name] = array_shift($colors);
            }
        }

        // Loop through the shift beans and assign a color to each staff member
        foreach ($roster as &$bean) {
            $editable = $data['editable'];
            // $bean['backgroundColor'] = $bw_color_map[$bean['title']]."33";
            $bean['backgroundColor'] = $color_map[$bean['title']] . '33';

            if ($bean['start_date'] >= $billing_period_start_date && $editable) {
                $editable = true;
            } else {
                $editable = false;
            }

            if ($editable) {
                $bean['backgroundColor'] = $color_map[$bean['title']] . 'cc';
            }

            if (is_null($bean['title'])) {
                $bean['title'] = 'Unassigned';
                $bean['backgroundColor'] = '#ff0000';
            }

            $duration = (strtotime($bean['end']) - strtotime($bean['start'])) / 60 / 60;

            $bean['title'] = $this->convertNameFormat($bean['title']);

            if ($bean['passive']) {
                $bean['title'] .= "<br /><span style='font-weight:normal'>Passive</span>";
            } else {
                $bean['title'] .= "<br /><span style='font-weight:normal'>" . $duration . 'hrs</span>';
            }

            if ($bean['do_not_bill'] == 1)
                $bean['title'] .= "<span style='font-weight:normal; font-style:italic'> (No Bill)</span>";

            $bean['editable'] = $editable;

            $bean['title'] = ['html' => $bean['title']];
        }

        return ['http_code' => 200, 'result' => $roster];
    }

    function convertNameFormat($name)
    {
        // Split the name into first name and last name
        $nameParts = explode(' ', $name);

        // Extract the first character of the first name and convert it to uppercase
        $firstName = $nameParts[0];

        // Get the last name initial
        $lastNameInitial = '';
        if (isset($nameParts[1])) {
            $lastNameInitial = strtoupper(substr($nameParts[1], 0, 1));
        }

        // Concatenate the first name and last name initial with a space in between
        $convertedName = $firstName . ' ' . $lastNameInitial;

        // Return the converted name
        return $convertedName;
    }
}

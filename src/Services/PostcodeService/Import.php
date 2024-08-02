<?php

namespace NDISmate\Services\PostcodeService;

use \RedBeanPHP\R as R;

class Import
{
    private array $postcodes;
    private array $isolated_towns;

    function __construct()
    {
        $this->postcodes = [];
        $this->isolated_towns = [];

        // I don't really want to pull data this way, but if it works, great.
        $postcode_json = file_get_contents('https://matthewproctor.com/Content/postcodes/australian_postcodes.json');

        $this->postcodes = json_decode($postcode_json, true);

        // Specify the fields you want to keep
        $keepFields = ['postcode', 'locality', 'state', 'MMM_2019'];  // replace with your actual field names

        // Use array_map to create a new array that only includes the fields you want to keep
        $this->postcodes = array_map(function ($item) use ($keepFields) {
            return array_intersect_key($item, array_flip($keepFields));
        }, $this->postcodes);
        unset($postcode_json);

        // load isoloated_towns_modification.json and search for record matching postcode and suburb
        // Load and decode the JSON file
        $isolated_towns_json = file_get_contents('isolated_towns_modification.json');
        $this->$isolated_towns = json_decode($isolated_towns_json, true);
        unset($isolated_towns_json);
    }

    function __invoke()
    {
        R::selectDatabase('ndismate');  // lets see what happens here.

        foreach ($this->postcodes as $_postcode) {
            $this->add($_postcode);
        }

        return;
    }

    public function add($_postcode)
    {
        // This regex only skips 3 records.  18272 vs 18269.  More likely the source data is wrong.
        // Also proves that using the regex should cause almost no impact to end users.
        if (preg_match('/^(0[289][0-9]{2})|([1-9][0-9]{3})$/', $_postcode['postcode'])) {
            // add function here to check unique record
            // $postcodes = R::dispense('postcodes');
            // $postcodes->postcode = $_postcode['postcode'];
            // $postcodes->suburb = $_postcode['locality'];
            // $postcodes->state = $_postcode['state'];
            // R::store($postcodes);

            $postcodes = R::findOrCreate('postcodes', ['postcode' => $_postcode['postcode'], 'suburb' => $_postcode['locality'], 'state' => $_postcode['state']]);

            $postcodes->ndis_zone = $this->getNDISZone(
                postcode: $post_code['postcode'],
                suburb: $_postcode['locality'],
                mmm_2019: $_postcode['MMM_2019']
            );
            R::store($postcodes);
        }

        return;
    }

    private function getNDISZone($postcode, $suburb, $mmm_2019)
    {
        // Search isolated_towns for a matching record
        foreach ($this->isolated_towns as $record) {
            if ($record['postcode'] == $postcode && $record['suburb'] == $suburb) {
                // If a match is found, return the modified ndis_zone
                return $record['ndis_zone'];
            }
        }

        // If no match is found, return $mmm_2019
        return $mmm_2019;
    }
}

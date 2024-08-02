<?php
namespace NDISmate\Services\SupportItemService;

use NDISmate\Models\SupportItem;
use RedBeanPHP\R as R;

class ImportSupportItem
{
    public function __invoke($data)
    {
        try {
            // Convert the base64_file data received into something usable
            list($mime_type_array, $base64_array) = explode(';', $data['base64_file']);
            $mime_type_array = explode(':', $mime_type_array);
            $mime_type = $mime_type_array[1];
            $base64_array = explode(',', $base64_array);
            $base64_file = $base64_array[1];

            $file_name = $data['file_name'];
            $binary = base64_decode($base64_file);  // this should contain the csv data from the file

            $csv = array_map('str_getcsv', explode("\n", $binary));
            $header = array_shift($csv);

            // this is an older version of the header that was used before the PACE changes
            // $compare = ['Support Item Number', 'Support Item Name', 'Registration Group Number', 'Registration Group Name', 'Support Category Number', 'Support Category Name', 'Unit', 'Quote', 'Start date', 'End Date', 'ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'Remote', 'Very Remote', 'Non-Face-to-Face Support Provision', 'Provider Travel', 'Short Notice Cancellations.', 'NDIA Requested Reports', 'Irregular SIL Supports', 'Type'];

            $compare = ['Support Item Number', 'Support Item Name', 'Registration Group Number', 'Registration Group Name', 'Support Category Number', 'Support Category Number (PACE)', 'Support Category Name', 'Support Category Name (PACE)', 'Unit', 'Quote', 'Start date', 'End Date', 'ACT', 'NSW', 'NT', 'QLD', 'SA', 'TAS', 'VIC', 'WA', 'Remote', 'Very Remote', 'Non-Face-to-Face Support Provision', 'Provider Travel', 'Short Notice Cancellations.', 'NDIA Requested Reports', 'Irregular SIL Supports', 'Type'];

            if (array_diff($header, $compare) != []) {
                throw new \Exception('CSV file does not match expected format');
            }

            foreach ($csv as $row) {
                if (count($row) == 1 && $row[0] == '') {  // skip blank lines
                    continue;
                }
                unset($data);
                $data = array_combine($header, $row);

                // checking for existing record

                // TODO: I want to update this so if it does find an existing record it updates it with the new data
                $beans = R::find(
                    'supportitems',
                    'support_item_number=:support_item_number AND start_date=:start_date',
                    [
                        ':support_item_number' => $data['Support Item Number'],
                        ':start_date' => substr_replace(substr_replace($data['Start date'], '-', 4, 0), '-', 7, 0)
                    ]
                );

                if (count($beans) == 0) {  // no existing record.  Create.

                    $support_item = new SupportItem;

                    $result = $support_item->create([
                        'support_item_number' => $data['Support Item Number'],
                        'support_item_name' => $data['Support Item Name'],
                        'registration_group_number' => $data['Registration Group Number'],
                        'registration_group_name' => $data['Registration Group Name'],
                        'support_category_number' => $data['Support Category Number'],
                        'support_category_number_pace' => $data['Support Category Number (PACE)'],
                        'support_category_name' => $data['Support Category Name'],
                        'support_category_name_pace' => $data['Support Category Name (PACE)'],
                        'unit' => $data['Unit'],
                        'quote' => $data['Quote'],
                        'start_date' => substr_replace(substr_replace($data['Start date'], '-', 4, 0), '-', 7, 0),
                        'end_date' => substr_replace(substr_replace($data['End Date'], '-', 4, 0), '-', 7, 0),
                        'act' => (float) str_replace(',', '', str_replace('$', '', $data['ACT'])),
                        'nsw' => (float) str_replace(',', '', str_replace('$', '', $data['NSW'])),
                        'nt' => (float) str_replace(',', '', str_replace('$', '', $data['NT'])),
                        'qld' => (float) str_replace(',', '', str_replace('$', '', $data['QLD'])),
                        'sa' => (float) str_replace(',', '', str_replace('$', '', $data['SA'])),
                        'tas' => (float) str_replace(',', '', str_replace('$', '', $data['TAS'])),
                        'vic' => (float) str_replace(',', '', str_replace('$', '', $data['VIC'])),
                        'wa' => (float) str_replace(',', '', str_replace('$', '', $data['WA'])),
                        'remote' => (float) str_replace(',', '', str_replace('$', '', $data['Remote'])),
                        'very_remote' => (float) str_replace(',', '', str_replace('$', '', $data['Very Remote'])),
                        'non_face_to_face_support_provision' => $data['Non-Face-to-Face Support Provision'],
                        'provider_travel' => $data['Provider Travel'],
                        'short_notice_cancellations' => $data['Short Notice Cancellations.'],
                        'ndia_requested_reports' => $data['NDIA Requested Reports'],
                        'irregular_sil_supports' => $data['Irregular SIL Supports'],
                        'type' => $data['Type']
                    ]);

                    $results[] = ['number' => $data['Support Item Number'],
                        'data' => $result];
                }
            }

            return $results;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}

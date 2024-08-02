<?php
namespace NDISmate\Models\SupportItem;

use RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class GetSupportItemByNumber
{
    public function __invoke($data)
    {
        try {
            // Determine the date for the query. Use the provided date or the current date if not specified.
            $date = isset($data['for_date']) ? $data['for_date'] : date('Y-m-d');
            $query = 'support_item_number = :support_item_number AND :date BETWEEN start_date AND end_date';
            $params = [
                ':support_item_number' => $data['support_item_number'],
                ':date' => $date
            ];

            // Execute the query using RedBeanPHP's findOne method to retrieve a single record
            $bean = R::findOne('supportitems', $query, $params);

            // Return the found bean (record) or null if not found
            return $bean;
        } catch (RedException $e) {
            // Catch and rethrow RedBeanPHP specific exceptions with a custom message
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Catch and rethrow any other exceptions
            throw $e;
        }
    }
}

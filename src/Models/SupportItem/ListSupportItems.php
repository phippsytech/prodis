<?php
namespace NDISmate\Models\SupportItem;

use RedBeanPHP\R as R;

class ListSupportItems
{
    public function __invoke($data)
    {
        try {
            // $result=R::find("supportitems", "support_item_number=?", [$data["support_item_number"]]);
            // return ["http_code"=>200, "result"=>$result];

            // NOTE: Crystel Care does not currently qualify for 0104 which is the STA SCA HI support series
            $idArray = ['0101', '0103', '0106', '0107', '0108', '0110', '0112', '0115', '0116', '0117', '0118', '0120', '0123', '0124', '0125', '0126', '0128', '0131', '0132', '0136'];  // Replace with your array of IDs

            // Convert the array of IDs to a comma-separated string
            $idList = implode(',', $idArray);

            $sql = "SELECT si.*
                    FROM supportitems si
                    WHERE si.registration_group_number IN ($idList)
                    AND CURRENT_DATE BETWEEN si.start_date AND si.end_date
                    ORDER BY si.support_item_number ASC";

            // Fetch records using the custom query
            $beans = R::getAll($sql);

            return $beans;
        } catch (RedException $e) {
            // Handle RedBeanPHP specific exceptions
            throw new \Exception('Error executing query: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other exceptions
            throw $e;
        }
    }
}

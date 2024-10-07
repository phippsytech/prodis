<?php 
namespace NDISmate\Models\Register\DocumentControl;

use \RedBeanPHP\R as R;

class ListDocumentControl
{
    public function __invoke($data)
    {
        // Initialize an empty array for the result
        $result = [];

        // SQL query to select document controls along with staff names
        $sql = "SELECT dc.*, s.name AS staff_name 
                FROM documentcontrols dc 
                LEFT JOIN staffs s ON dc.staffs_id = s.id"; // Join to get staff names

        // Optional parameters array for filtering
        $params = [];
        $conditions = [];

        // Example: Filtering by 'archived' status if provided
        if (isset($data['archived'])) {
            $conditions[] = 'dc.archived = ?';
            $params[] = $data['archived'];
        }

        // Example: Filtering by document owner (staffs_id) if provided
        if (isset($data['staffs_id'])) {
            $conditions[] = 'dc.staffs_id = ?';
            $params[] = $data['staffs_id'];
        }

        // Append conditions to SQL query if there are any
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Optionally order by revision_date (or any other field)
        $sql .= ' ORDER BY dc.revision_date DESC';

        // Execute the query using RedBeanPHP
        $result = R::getAll($sql, $params);

        return $result;
    }
}

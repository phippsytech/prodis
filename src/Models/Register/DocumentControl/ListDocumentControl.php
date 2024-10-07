<?php 
namespace NDISmate\Models\Register\DocumentControl;


use \RedBeanPHP\R as R;

class ListDocumentControl
{
    public function __invoke($data)
    {
       // Initialize an empty array for the result
        $result = [];

        // Basic SQL query to select all from the documentcontrols table
        $sql = "SELECT * FROM documentcontrols";
        
        $params = [];
        $conditions = [];

        // Example: Filtering by document owner (staffs_id) if provided
        if (isset($data['staffs_id'])) {
            $conditions[] = 'staffs_id = ?';
            $params[] = $data['staffs_id'];
        }

        // Append conditions to SQL query if there are any
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Optionally order by revision_date (or any other field)
        $sql .= ' ORDER BY revision_date DESC';

        // Execute the query using RedBeanPHP
        $result = R::getAll($sql, $params);

        return $result;
    }
}
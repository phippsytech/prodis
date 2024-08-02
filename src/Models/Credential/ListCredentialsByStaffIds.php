<?php
namespace NDISmate\Models\Credential;

use Respect\Validation\Validator as v;
use \NDISmate\CORE\CRUD;
use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class ListCredentialsByStaffIds
{
    function __invoke($data)
    {
        if (!isset($data['staff_ids']) || count($data['staff_ids']) == 0) {
            return ['http_code' => 400, 'error' => 'Invalid staff IDs'];
        }

        // Prepare the staff IDs for the SQL query
        // $staffIds = implode(',', array_fill(0, count($data['staff_ids']), '?'));

        // Create the placeholders for staff IDs
        $placeholders = implode(',', array_fill(0, count($data['staff_ids']), '?'));

        // Prepare the staff IDs as parameters for binding
        $params = $data['staff_ids'];

        $sql = "SELECT 
        s.staff_id,
        s.staff_name,
        c.id AS credential_id,
        c.name AS credential_name,
        c.years_until_expiry,
        c.collect_from_sil AS requirement_status,
        CASE 
            WHEN c.date_collection_option = 'do_not_collect' THEN 'do_not_collect'
            WHEN c.date_collection_option = 'expires' THEN 'expires'
            ELSE 'issued'
        END AS date_type,
        CASE 
            WHEN c.date_collection_option IN ('do_not_collect', 'expires') THEN NULL
            ELSE sc.credential_date
        END AS issue_date,
        CASE 
            WHEN c.date_collection_option = 'expires' THEN sc.credential_date
            WHEN c.collect_expiry = 1 AND sc.credential_date IS NOT NULL THEN DATE_ADD(sc.credential_date, INTERVAL c.years_until_expiry YEAR)
            WHEN c.date_collection_option = 'issued' AND c.years_until_expiry IS NOT NULL AND sc.credential_date IS NOT NULL THEN DATE_ADD(sc.credential_date, INTERVAL c.years_until_expiry YEAR)
            ELSE NULL
        END AS expiry_date,
        sc.details AS credential_details,
        sc.google_drive_file_ref AS file_reference,
        IF(sc.id IS NULL AND c.collect_from_sil = 'required', 'Missing', 'Provided') AS credential_status
    FROM 
        (SELECT id as staff_id, name as staff_name FROM staffs WHERE id IN ($placeholders)) s
    LEFT JOIN 
    credentials c ON c.collect_from_sil = 'required'
        
    LEFT JOIN 
        staffcredentials sc ON c.id = sc.credential_id AND sc.staff_id = s.staff_id
    ORDER BY 
        s.staff_name, c.id";

        // Execute the prepared statement with the staff IDs as parameters
        $beans = R::getAll($sql, $params);

        return ['http_code' => 200, 'result' => $beans];
    }
}
// If you want to include optional credentials, replace "credentials c ON c.collect_from_sil = 'required'" with:
// credentials c ON (c.collect_from_sil = 'required' OR (c.collect_from_sil = 'optional' AND EXISTS (SELECT 1 FROM staffcredentials WHERE credential_id = c.id AND staff_id = s.staff_id)))

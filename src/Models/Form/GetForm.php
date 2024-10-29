<?php

namespace NDISmate\Models\Form;
use \RedBeanPHP\R as R;

class GetForm {

    public function __invoke($data)
    {


        $query = "
        SELECT
            f.*,
            CONCAT('[', GROUP_CONCAT(fs.fields), ']') AS fields
        FROM
            forms f
        INNER JOIN 
            formschemas fs ON fs.form_id = f.id
        WHERE 
            f.id = :formId 
            AND (f.current_schema_id IS NULL OR fs.id = f.current_schema_id)
        GROUP BY 
            f.id
    ";

        $params = [':formId' => $data['form_id']];
         
        
        $result = R::getRow($query, $params);

        $fields = json_decode($result['fields'], true);

        unset($result['fields']);

        $result['schema'] = ['fields' => $fields];

        return $result;

       
    }
}

<?php
namespace NDISmate\Models;

use Exception;
use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Form extends NewCustomModel
{
    public function __construct($bean = null)
    {

        parent::__construct($bean ?: R::dispense('forms'));
        $this->fields = [
            'title' => [v::stringType()],  
            'description' => [v::optional(v::stringType())],  
            'current_schema_id' => [v::optional(v::numericVal())], 
            'tenant_id' => [v::optional(v::numericVal())], 
            'created_at' => [v::optional(v::dateTime('Y-m-d H:i'))],  
        
        ];
    }

    public function createForm($data) {

       $result =  $this->create($data);

       return [
        'form_id' => $result->id,
        'title' => $result->title,
        'description' => $result->description,
        'created_at' => $result->created,
        ];
    }

    public function updateForm ($data) {
        
        if (!isset($data['form_id'])) throw new Exception('Missing Parameter.');
        
        $result = $this->update([
            'id' => $data['form_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'current_schema_id' => $data['current_schema_id'],
        ]);

        return [
            'form_id' => $result->id,
            'title' => $result->title,
            'description' => $result->description,
            'created_at' => $result->created,
            'updated_at' => $result->updated,
        ];
    }

    public function deleteForm ($data) {
        
        if (!isset($data['form_id'])) throw new Exception('Missing Parameter.');
        
        $result = $this->delete(['id' => $data['form_id']]);

        return [];
    }

    public function listForms()
    {
      
        $query = "SELECT 
                    f.id as form_id,
                    f.title,
                    f.description,
                    f.current_schema_id,
                    f.created as created_at,
                    f.updated as updated_at

                FROM
                    forms f
                ORDER BY f.updated DESC
        ";

        $result = R::getAll($query);

        return $result;
    }

    
}

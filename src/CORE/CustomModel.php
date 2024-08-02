<?php
namespace NDISmate\CORE;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Exception;
use \RedBeanPHP\R as R;
use \RedBeanPHP\SimpleModel;

class CustomModel extends SimpleModel
{
    protected $user_id;
    var $bean;

    function __construct()
    {
        // Access the user_id from the session
        $this->user_id = $_SESSION['user_id'] ?? null;
    }

    public function open()
    {
        // Automatically set the 'created' timestamp when data is created
        if (!$this->bean->id) {
            // $this->bean->created = time();
            $this->bean->archived = false;  // Set 'archived' to false when initially created
        }

        // Set default 'updated' field if it doesn't exist
        if (!isset($this->bean->updated)) {
            $this->bean->updated = null;  // or you can set it to the current timestamp if desired
        }
    }

    public function create($data)
    {
        foreach ($data as $property => $value) {
            $this->bean->$property = $value;
        }
        $this->bean->created = date('Y-m-d H:i:s');
        $this->bean->archived = false;  // Set 'archived' to false when initially created
        R::store($this->bean);
    }

    public function updateData($data)
    {
        if (isset($data['id'])) {
            $id = $data['id'];
            unset($data['id']);  // Remove 'id' from the data array to prevent setting it as a property

            // Load the bean using the provided 'id'
            $existingBean = R::load($this->bean->getMeta('type'), $id);

            if (!$existingBean->id) {
                throw new Exception("Record with ID $id does not exist.");
            }

            // Update the existing bean properties based on the provided data array
            foreach ($data as $property => $value) {
                $existingBean->$property = $value;
            }

            // Store the changes
            R::store($existingBean);
        } else {
            throw new Exception('id parameter is missing');
        }
    }

    protected function validateFields($fields, $bean)
    {
        $errors = [];

        foreach ($fields as $field => $rules) {
            try {
                // Apply optional validation to each field's rules
                v::allOf(...$rules)->assert($bean->$field);
            } catch (NestedValidationException $e) {
                // Get the error messages for the specific field
                $errorMessages = $e->findMessages([$field]);

                // Store all the error messages for the field in the errors array
                $errors[$field] = $errorMessages;
            }
        }

        return $errors;
    }

    public function delete($id)
    {
        // Load the bean using the provided 'id'
        $existingBean = R::load($this->bean->getMeta('type'), $id);

        if (!$existingBean->id) {
            throw new Exception("Record with ID $id does not exist.");
        }

        // Delete the loaded bean
        R::trash($existingBean);
    }

    public function update()
    {
        // Automatically set the 'updated' timestamp when data is updated
        $this->bean->updated = time();
    }

    public function archive()
    {
        $this->bean->archived = true;
        R::store($this->bean);
    }

    public function restore()
    {
        $this->bean->archived = false;
        R::store($this->bean);
    }

    // Custom logging behaviour
    // Modify this to keep data in browsers up to date with websockets.
    public function after_update()
    {
        parent::after_update();
        $this->logToFile('Updated', $this->bean->getMeta('type'), $this->bean->id, $this->user_id);
    }

    public function after_delete()
    {
        parent::after_delete();
        $this->logToFile('Deleted', $this->bean->getMeta('type'), $this->bean->id, $this->user_id);
    }

    public function after_create()
    {
        parent::after_create();
        $this->logToFile('Created', $this->bean->getMeta('type'), $this->bean->id, $this->user_id);
    }

    private function logToFile($action, $type, $id, $user_id)
    {
        $logMessage = date('[Y-m-d H:i:s]') . " $action $type with ID $id by $user_id\n";

        // Replace 'path/to/logfile.log' with your desired log file path
        // file_put_contents('path/to/logfile.log', $logMessage, FILE_APPEND);
    }
}

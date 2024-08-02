<?php

/**
 * I want this class to replace the CustomModel class, but I need to check that
 * it doesn't break the current way that CustomModel is implemented in my code.
 *
 * To check that, I just need to search for the code that extends custommodel
 * and replace it with the new class, and test.
 *
 * if it works, I can replace CustomModel with this class
 */

namespace NDISmate\CORE;

use RedBeanPHP\R as R;
use RedBeanPHP\SimpleModel;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Exception;

class NewCustomModel extends SimpleModel
{
    protected $user_id;
    var $bean;
    var $fields;

    function __construct($bean = null, $user_id = null)
    {
        $this->bean = $bean ?: R::dispense($this->getMeta('type'));

        // Access the user_id from the session
        $this->user_id = $user_id;
    }

    protected function validateFields($fields, $data, $validateAll = true)
    {
        $errors = [];

        foreach ($fields as $field => $rules) {
            if ($validateAll || array_key_exists($field, $data)) {
                // Check if the field is optional by looking for v::optional in the rules
                $isOptional = false;
                foreach ($rules as $rule) {
                    if ($rule instanceof \Respect\Validation\Rules\Optional) {
                        $isOptional = true;
                        break;
                    }
                }

                // If validateAll is true, proceed with validation for non-optional fields or optional fields that are present
                if ($validateAll && (!$isOptional || ($isOptional && array_key_exists($field, $data)))) {
                    try {
                        // Apply validation rules
                        v::allOf(...$rules)->setName($field)->assert($data[$field] ?? null);
                    } catch (NestedValidationException $e) {
                        // Handle validation exceptions
                        $errors[$field] = $e->getMessages();
                    }
                }
            }
        }
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function create($data)
    {
        try {
            // $errors = $this->validateFields($this->fields, $this->bean);
            $errors = $this->validateFields($this->fields, $data);

            $this->bean = R::dispense($this->bean->getMeta('type'));

            foreach ($data as $property => $value) {
                if (array_key_exists($property, $this->fields)) {
                    $this->bean->$property = $value;
                }
            }

            $this->bean->created = date('Y-m-d H:i:s');
            $this->bean->setMeta('cast.updated', 'datetime');
            $this->bean->updated = null;
            $this->bean->archived = false;  // Set 'archived' to false when initially created

            R::store($this->bean);
            return $this->bean;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function load($id)
    {
        $this->bean = R::load($this->bean->getMeta('type'), $id);
        if ($this->bean->id == 0) {
            throw new Exception('No ' . $this->bean->getMeta('type') . " found with ID $id.");
        }
        return $this->bean;
    }

    public function delete($data)
    {
        $id = $data['id'];

        // Load the bean using the provided 'id'
        $existingBean = R::load($this->bean->getMeta('type'), $id);

        if (!$existingBean->id) {
            throw new Exception("Record with ID $id does not exist.");
        }

        R::trash($existingBean);  // Delete the loaded bean
    }

    public function update($data)
    {
        try {
            $errors = $this->validateFields($this->fields, $data, false);
            $this->load($data['id']);

            foreach ($data as $property => $value) {
                if (array_key_exists($property, $this->fields)) {  // && isset($this->bean->$property)) {
                    $this->bean->$property = $value;
                }
            }
            $this->bean->updated = date('Y-m-d H:i:s');
            R::store($this->bean);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function archive($data)
    {
        $this->load($data['id']);
        $this->bean->archived = true;
        R::store($this->bean);
    }

    public function restore($data)
    {
        $this->load($data['id']);
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
    }
}

class ValidationException extends Exception
{
    private $errors;

    public function __construct(array $errors, $message = 'Validation errors occurred', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

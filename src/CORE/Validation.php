<?php
namespace NDISmate\CORE;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class Validation
{
    public static function validate($dataValidator, $data)
    {
        try {
            $dataValidator->assert($data);
        } catch (NestedValidationException $exception) {
            return self::get_validation_errors($exception, $dataValidator);
        }

        return true;
    }

    public static function validateFields($fields, $data)
    {
        $dataValidator = new v;

        foreach ($fields as $key => $value) {
            if (!empty($value[0])) {
                $rule = $value[0];

                if (isset($value[1])) {
                    $required = $value[1];
                } else {
                    $required = false;
                }

                $dataValidator->key($key, $rule, $required);
            }
        }

        return self::validate($dataValidator, $data);
    }

    public static function get_validation_errors($exception, $validator)
    {
        // $validator contains all the fields we are checking!
        foreach ($validator->getRules() as $rule) {
            $fields[] = $rule->reference;
        }

        $error_fields = $exception->findMessages($fields);

        foreach (array_keys($error_fields, '', true) as $key) {
            unset($error_fields[$key]);
        }

        return $error_fields;
    }

    function validate_data($dataValidator, $data)
    {
        try {
            $dataValidator->assert($data);
        } catch (NestedValidationException $exception) {
            return get_validation_errors($exception, $dataValidator);
        }

        return true;
    }
}

namespace Respect\Validation\Rules;

class containsAny extends AbstractRule
{
    public $containsArray;

    public function __construct($containsArray)
    {
        $this->containsArray = $containsArray;
    }

    public function validate($input)
    {
        return in_array(strtolower($input), $this->containsArray);
    }
}

namespace Respect\Validation\Exceptions;

class containsAnyException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be a value in the array',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not be a value in the array',
        ]
    ];
}

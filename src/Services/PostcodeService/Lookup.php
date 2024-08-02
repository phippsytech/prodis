<?php

namespace NDISmate\Services\PostcodeService;

use NDISmate\CORE\Validation;
use RedBeanPHP\R as R;
use Respect\Validation\Validator as v;
use Exception;

class Lookup
{
    function __construct()
    {
        $this->fields = [
            'suburb' => [v::stringType()],
            'postcode' => [v::stringType()],
            'state' => [v::stringType()],
            'ndis_zone' => [v::stringType()],
        ];
    }

    private function _require($fields)
    {
        // reset all required fields to false
        foreach ($this->fields as $key => $value) {
            $this->fields[$key][1] = false;
        }

        // now set the fields that are required
        foreach ($fields as $field) {
            $this->fields[$field][1] = true;
        }
    }

    function __invoke($data)
    {
        R::selectDatabase('ndismate');

        // print($data);
        // $data = json_decode($data, true);

        // Define required fields #
        $this->_require(['postcode']);

        // Validate #
        $error = Validation::validateFields($this->fields, $data);
        if ($error !== true) {
            throw new Exception($error);
        }

        $postcode = $data['postcode'];

        if (preg_match('/^(0[289][0-9]{2})|([1-9][0-9]{3})$/', $postcode)) {  // do basic check first
            $_postcodes = R::getAll('SELECT suburb, state, ndis_zone from postcodes where postcode=:postcode order by suburb', [':postcode' => $postcode]);

            if (empty($_postcodes)) {
                throw new Exception('Not found');
            }

            $state = $_postcodes[0]['state'];
            foreach ($_postcodes as $_postcode) {
                $suburbs[] = ['suburb' => $_postcode['suburb']];
            }

            return ['postcode' => $postcode, 'state' => $state, 'suburbs' => $suburbs, 'valid' => true];
        } else {
            throw new Exception('A valid Australian postcode is required');
        }
    }
}

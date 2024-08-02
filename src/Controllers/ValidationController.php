<?php
namespace NDISmate\Controllers;

use NDISmate\CORE\BaseController;
use NDISmate\Services\ValidationService\Date as ValidateDate;
use NDISmate\Services\ValidationService\Email as ValidateEmail;
use NDISmate\Services\ValidationService\FullName as ValidateFullName;
use NDISmate\Services\ValidationService\PhoneNumber as ValidatePhoneNumber;
use NDISmate\Services\ValidationService\Postcode as ValidatePostcode;
use NDISmate\Services\ValidationService\Url as ValidateUrl;

final class ValidationController extends BaseController
{
    protected function defineController()
    {
        $this->controller = [
            // 'BSB' => [new ValidateBSB, null, true, []],
            'Date' => [new ValidateDate, null, true, []],
            'Email' => [new ValidateEmail, null, true, []],
            'FullName' => [new ValidateFullName, null, true, []],
            'PhoneNumber' => [new ValidatePhoneNumber, null, true, []],
            'Postcode' => [new ValidatePostcode, null, true, []],
            'Url' => [new ValidateUrl, null, true, []],
        ];
    }
}

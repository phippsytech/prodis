<?php

namespace NDISmate\Models\Invoice\NDIA\PaymentRequestStatus;

use \RedBeanPHP\R as R;

class Upload
{
    public function __invoke($obj, $data)
    {
        // $data contains an array of files.
        // name, size, type, data (in base64)

        foreach ($data as $file) {
            $file['data'] = $this->getBinary($file['data']);
            (new \NDISmate\Models\Invoice\NDIA\PaymentRequestStatus\Store)($obj, $file);
        }

        return ['http_code' => 201];
    }

    function getBinary($data)
    {
        list($mime_type_array, $base64_array) = explode(';', $data);
        $mime_type_array = explode(':', $mime_type_array);
        $mime_type = $mime_type_array[1];
        $base64_array = explode(',', $base64_array);
        $base64_file = $base64_array[1];
        $binary = base64_decode($base64_file);
        return $binary;
    }
}

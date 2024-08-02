<?php
namespace NDISmate\Models\Invoice\NDIA\Remittance;

use \RedBeanPHP\R as R;

class UploadRemittance
{
    public function __invoke($obj, $data)
    {
        // $data contains an array of files.
        // name, size, type, data (in base64)
        $errors = [];
        foreach ($data as $file) {
            $file['data'] = $this->getBinary($file['data']);
            $result = (new \NDISmate\Models\Invoice\NDIA\Remittance\StoreRemittance)($obj, $file);
            if ($result['http_code'] == 400) {
                $errors[] = $result['error_message'];
            }
        }

        return ['http_code' => 201, 'errors' => $errors];
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

<?php

namespace NDISmate\Services\ValidationService;

use App\Core\JsonResponse;
use HumanNameParser\Parser;

final class FullName
{
    public function __invoke($data)
    {
        $post_data = $data;

        $full_name = trim($post_data['full_name']);

        if (strlen(trim($full_name)) == 0) {
            return ['http_code' => 400, 'error_message' => 'Full name is required.'];
        }

        $options['mandatory_last_name'] = true;
        $nameparser = new Parser($options);
        try {
            $name = $nameparser->parse($full_name);
            $name_array['first'] = $name->getFirstName();
            if ($name->getMiddleName())
                $name_array['middle'] = $name->getMiddleName();
            $name_array['last'] = $name->getLastName();

            foreach ($name_array as $key => $name_string) {
                $name_string = str_replace('.', '', $name_string);  // remove period from initials
                if (strlen($name_string) < 2 && $key != 'middle') {  // If first name contains a single letter, or is empty
                    return ['http_code' => 400, 'error_message' => 'Initials are not permitted'];
                } else {  // otherwise
                    $name_string = ucfirst(strtolower($name_string));  // capitalise
                }
                $name_array[$key] = $name_string;
            }

            $full_name = join(' ', $name_array);

            $full_name = preg_replace('!\s+!', ' ', $full_name);

            return JsonResponse::ok(['full_name' => $full_name]);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();

            // reword some error messages
            switch ($error_message) {
                case "Couldn't find a last name.":
                    $error_message = 'Full name is required (last name is missing).';
                    break;
                default:
                    break;
            }

            return JsonResponse::badRequest(['error_message' => $error_message]);
        }
    }
}

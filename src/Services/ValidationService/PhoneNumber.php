<?php

namespace NDISmate\Services\ValidationService;

use App\Core\JsonResponse;

final class PhoneNumber
{
    public function __invoke($data)
    {
        $post_data = json_decode($data, true);
        $number = preg_replace('/[^0-9]+/', '', $post_data['phone_number']);

        // TEST NUMBER to bypass twilio lookups to save money
        if ($number == '0411111111') {
            return JsonResponse::ok(['result' => 'Test number supplied.']);
        }

        $mobile_only = $post_data['mobile_only'];

        // check string length
        if (!in_array(strlen($number), [6, 10])) {
            return JsonResponse::badRequest(['error_message' => 'An Australian phone number is required.  (Number is wrong length).  Fake numbers are not accepted.']);
        }

        if ($mobile_only) {
            $mobile_number = substr($number, -9);
            if (!in_array(substr($mobile_number, 0, 1), [4, 5])) {
                return JsonResponse::badRequest(['error_message' => 'An Australian mobile phone number is required.  (10 digits starting with 04).  Fake numbers are not accepted.']);
            }
        }

        $required = false;
        if (isset($post_data['required']) && $post_data['required'] == true)
            $required = true;

        $is_valid = false;

        $sid = 'AC6fc01b2e3b7577807df4344e107ba6c7';
        $token = '73b6a4fbe413648c0191567fb5297979';
        $url = 'https://lookups.twilio.com/v1/PhoneNumbers/+61' . $number . '?CountryCode=AU&Type=carrier';
        $x = curl_init($url);
        curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($x, CURLOPT_USERPWD, "$sid:$token");
        $result = curl_exec($x);
        curl_close($x);
        $json_result = json_decode($result);

        if (isset($json_result->status) && $json_result->status == '404') {
            $is_valid = false;  //  echo "phone number doesn't exist";
        } else {
            if ($json_result->carrier->name == 'Australian Communications and Media Authority') {
                $is_valid = false;  // echo "phone number is not active";
            } else {
                $is_valid = true;
            }
        }

        if ($is_valid) {
            return JsonResponse::ok(['result' => $json_result]);
        } else {
            return JsonResponse::badRequest(['error_message' => 'An Australian phone number is required.  Fake numbers are not accepted.']);
        }
    }
}

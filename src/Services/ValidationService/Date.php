<?php

namespace NDISmate\Services\ValidationService;

use NDISmate\CORE\JsonResponse;

final class Date
{
    public function __invoke($data)
    {
        $is_valid = true;  // assume date is valid.  We do tests to prove it isn't

        $post_data = $data;

        if (empty($post_data['date']))
            return JsonResponse::badRequest(['error_message' => 'A valid date of birth in day/month/year is required.']);

        $date = $post_data['date'];

        // do basic format check first
        if (preg_match('/^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$/i', $date)) {
            $date_array = explode('/', $date);

            if (count($date_array) != 3)
                return JsonResponse::badRequest(['error_message' => 'A valid date of birth in day/month/year is required.']);

            list($day, $month, $year) = $date_array;

            // TODO: there is a problem with this if 01/01/200_ is passed (ie an incorrectly formatte date)
            if (checkdate($month, $day, $year)) {
                $datetime = date_create_from_format('d/m/Y', $date);

                if ($datetime) {
                    // passed initial date test.

                    // Is the age 18 or greater
                    $now = new \DateTime('today');
                    $age = $datetime->diff($now)->y;

                    if ($now < $datetime) {
                        $age = $age * -1;  // make the age negative
                    }

                    if ($age < 0) {
                        $is_valid = false;
                        return JsonResponse::badRequest(['error_message' => "It looks like you haven't been born.  Time travellers prohibited.  Please provide your real date of birth."]);
                    }

                    if ($age < 18) {
                        $is_valid = false;
                        return JsonResponse::badRequest(['error_message' => 'Must be 18 years of age or older']);
                    }

                    // Is the age older than the oldest ever recorded?
                    if ($age > 122) {
                        $is_valid = false;
                        return JsonResponse::badRequest(['error_message' => "C'mon... This makes you older than the oldest person on record.  Please provide your real date of birth"]);
                    }
                } else {
                    // date provide was invalid
                    $is_valid = false;
                }
            } else {
                $is_valid = false;
            }

            if ($is_valid) {
                return JsonResponse::ok(['date' => $datetime->format('jS F Y'), 'age' => $age]);
            } else {
                return JsonResponse::badRequest(['error_message' => 'A valid date of birth in day/month/year is required']);
            }
        } else {
            return JsonResponse::badRequest(['error_message' => 'A valid date of birth in dd/mm/yyyy format is required']);
        }
    }
}

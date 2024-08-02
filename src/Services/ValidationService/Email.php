<?php

namespace NDISmate\Services\ValidationService;

use NDISmate\CORE\JsonResponse;

final class Email
{
    public function __invoke($data)
    {
        $post_data = $data;

        $email = trim($post_data['email']);

        $is_valid = FALSE;

        // VALID EMAIL TEST
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Does domain name actually exist?
            $domain = explode('@', $email);
            if (checkdnsrr(array_pop($domain) . '.', 'MX')) {
                $is_valid = TRUE;  // email is valid
            }
        }

        $result = array('status' => $is_valid);

        if ($is_valid) {
            return JsonResponse::ok(['status' => 'ok']);
        } else {
            return JsonResponse::badRequest(['error_message' => 'Email address is required.  Check the spelling.  Fake or disposable email addresses are not accepted.']);
        }
    }
}

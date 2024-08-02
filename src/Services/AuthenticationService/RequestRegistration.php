<?php
namespace NDISmate\Services\AuthenticationService;

use NDISmate\Amazon\SES;
use NDISmate\CORE\ContentTemplate;
use NDISmate\CORE\JsonResponse;
use NDISmate\Services\AuthenticationService\IssueRegistrationToken;
use NDISmate\Services\EmailService;
use RedBeanPHP\R as R;

final class RequestRegistration
{
    public function __invoke($data)
    {
        $email = $data['email'];
        $domain = $data['domain'];

        // load the user
        $user_bean = R::findOne('users', ' email=:email', ['email' => $email]);

        if (!$user_bean) {
            // Not a user.  Not welcome.
            // We send ok, just to provide a "check your email" message that doesn't reveal anything.
            return JsonResponse::ok([]);
        }

        // If the user is an archived staff member they can't log in.
        $staff_bean = R::findOne('staffs', ' email=:email', ['email' => $email]);

        if ($staff_bean) {
            if ($staff_bean['archived'] == 1) {
                // the staff member is archived.  They can't create a user account;
                return JsonResponse::ok([]);
            }
        }

        if ($user_bean) {
            $registration_token = (new IssueRegistrationToken)(['email' => $email]);

            $login_link = 'https://' . $domain . '/#/?token=' . $registration_token;

            // Send login link to the user.
            $html = (new ContentTemplate)->render([
                'template' => 'login-link.html',
                'template_variables' => [
                    'ACTION_URL' => $login_link,
                    'PRODUCT_NAME' => 'Crystel Care',
                    'PRODUCT_URL' => 'https://crm.crystelcare.com.au',
                    'ORGANISATION_NAME' => 'Crystel Care',
                    'ORGANISATION_ADDRESS' => '',
                    'SUPPORT_URL' => 'https://crystelcare.com.au',
                    'EMAIL_ADDRESS' => $email,
                    // 'OPERATING_SYSTEM'=>"unknown",
                    // 'BROWSER_NAME'=>"unknown"
                ],
            ]);

            EmailService::send([
                'to_email' => $email,
                'from_name' => 'Crystel Care',
                'from_email' => 'crystel@crystelcare.com.au',
                'subject' => 'Your Magic Link',
                'html' => $html
            ]);
        }

        return JsonResponse::ok([]);
    }
}

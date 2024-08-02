<?php
namespace NDISmate\Services;

use Twilio\Rest\Client;

/*
 * NOTE: See the /servers/sms folder to for code that shows the server that
 * actually watches and sends sms messages.
 */

class SMSService
{
    static public function send($data)
    {
        $to = $data['to'];  // +1234567890
        $message = $data['message'];

        /*
         * Crystel Care: Therapy
         * Mon 7 Aug, 10am
         *
         * Contact therapist to change, 24hr cancellation policy applies.
         */

        $from = $data['from'];  // "+61483946347" // your Twilio number

        $twilio = new Client(TWILIO_SID, TWILIO_TOKEN);

        try {
            $sms = $twilio->messages->create($to, ['body' => $message, 'from' => $from]);
            return ['http_code' => 200, 'result' => $sms];
        } catch (\Exception $e) {
            // $error = json_decode($e->getMessage(), true);
            $error = $e->getMessage();
            return ['http_code' => 400, 'error' => $error];
        }
    }
}

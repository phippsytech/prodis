<?php
namespace NDISmate\Services;

use Aws\Ses\SesClient;

class EmailService
{
    /**
     * when you send an email using Amazon SES, you'll need to specify the From address using the custom domain you set up, such as "sender@mail.crystelcare.com.au".
     * If you do this, the "mailed-by" header in Gmail should display "mailed-by: mail.crystelcare.com.au".
     * But, if you reply...
     * If I set up a reply to address, the email is designated to the promotial tab in Gmail.
     */
    static public function send($data)
    {
        $instance = new self();

        $client = SesClient::factory(array(
            'version' => 'latest',
            'region' => 'ap-southeast-2',
            'credentials' => array(
                'key' => AMAZON_ACCESS_KEY,
                'secret' => AMAZON_SECRET_KEY,
            )
        ));

        $result = $client->sendEmail([
            'Destination' => [
                'ToAddresses' => [$data['to_email']],
            ],
            'Message' => [
                'Body' => [
                    'Text' => [
                        'Data' => $instance->convertHtmlToPlainText($data['html']),
                        'Charset' => 'UTF-8',
                    ],
                    'Html' => [
                        'Data' => $data['html'],
                        'Charset' => 'UTF-8',
                    ],
                ],
                'Subject' => [
                    'Data' => $data['subject'],
                    'Charset' => 'UTF-8',
                ],
            ],
            'Source' => '"Crystel Care" <crystel@mail.crystelcare.com.au>',
            // 'ReplyToAddresses' => [$data["from_email"]],
        ]);

        return $result;
    }

    private function convertHtmlToPlainText($html)
    {
        $instance = new self();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(TRUE);  // disable libxml errors
        $dom->loadHTML($html);
        libxml_clear_errors();  // remove errors for yucky html
        $instance->removeElementsByTagName($dom, 'style');
        $text = $dom->saveHTML();
        $text = rtrim(html_entity_decode(strip_tags($text)));
        $text = preg_replace('/ {2,}/', ' ', $text);
        return $text;
    }

    private function removeElementsByTagName($dom, $tagName)
    {
        $list = $dom->getElementsByTagName($tagName);
        while ($node = $list->item(0)) {
            $node->parentNode->removeChild($node);
        }
    }
}

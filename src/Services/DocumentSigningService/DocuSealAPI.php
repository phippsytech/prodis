<?php
namespace NDISmate\Services\DocumentSigningService;

class DocuSealAPI
{
    static public function call($params)
    {
        $api_url = 'https://sign.prodis.app/api';

        // $endpoint = '/templates/html';

        $method = isset($params['method']) ? $params['method'] : 'POST';
        $endpoint = $params['endpoint'];

        $data = $params['data'];

        try {
            $curl = curl_init();

            $json_data = json_encode($data);

            curl_setopt_array($curl, [
                CURLOPT_URL => $api_url . $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $json_data,
                CURLOPT_HTTPHEADER => [
                    'X-Auth-Token: ' . DOCUSEAL_API_KEY,
                    'content-type: application/json'
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                throw new \Exception('cURL Error #:' . $err);
            }

            return json_decode($response, true);
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage() . ' ' . __FILE__ . ' ' . __LINE__;
        }
    }
}

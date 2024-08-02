<?php

namespace NDISmate\Services\ValidationService;

use NDISmate\CORE\JsonResponse;

final class Url
{
    public function __invoke($data)
    {
        $url = trim($data['url']);

        $is_valid = FALSE;

        // VALID URL TEST
        if ($this->checkUrl($url) === true) {
            $is_valid = TRUE;  // url is valid
        }

        $result = array('status' => $is_valid);

        if ($is_valid) {
            return JsonResponse::ok(['status' => 'ok']);
        } else {
            return JsonResponse::badRequest(['error_message' => "You must provide a real Website URL.  Check the spelling.  Check the site isn't down."]);
        }
    }

    // https://www.php.net/manual/en/function.get-headers.php#119497
    function url_valid($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {  // looks like a url
            $file_headers = @get_headers($url);
            if ($file_headers === false)
                return false;
            foreach ($file_headers as $header) {
                if (preg_match('/^Location: (http.+)$/', $header, $m))
                    $url = $m[1];
                if (preg_match('/^HTTP.+\s(\d\d\d)\s/', $header, $m))
                    $code = $m[1];
            }
            if ($code == 200) {
                return true;
            } else {
                return false;
            }
        } else {  // doesn't look like a url
            return false;
        }
    }

    function noProtocol($url)
    {
        if (strpos($url, 'https://') === false &&
                strpos($url, 'http://') === false) {
            return true;
        }
        return false;
    }

    function isHttps($url)
    {
        if (strpos($url, 'https://') === true) {
            return true;
        }
        return false;
    }

    function checkUrl($supplied_url)
    {
        if ($this->noProtocol($supplied_url)) {
            if (!$this->url_valid('https://' . $supplied_url)) {
                if (!$this->url_valid('http://' . $supplied_url)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            if (!$this->url_valid($supplied_url)) {
                if ($this->isHttps($supplied_url)) {
                    if (!$this->url_valid($supplied_url)) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    if (!$this->url_valid($supplied_url)) {
                        return false;
                    } else {
                        return true;
                    }
                }
            } else {
                return true;
            }
        }
        return false;
    }
}

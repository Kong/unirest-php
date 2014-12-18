<?php

namespace Unirest;

class Response
{

    public $code;
    public $raw_body;
    public $body;
    public $headers;

    /**
     * @param int $code response code of the cURL request
     * @param string $raw_body the raw body of the cURL response
     * @param string $headers raw header string from cURL response
     */
    public function __construct($code, $raw_body, $headers)
    {
        $this->code     = $code;
        $this->headers  = http_parse_headers($headers);
        $this->raw_body = $raw_body;
        $this->body     = $raw_body;
        $json           = json_decode($raw_body);

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->body = $json;
        }
    }
}

/**
 * if PECL_HTTP is not available use a fall back function
 *
 * thanks to ricardovermeltfoort@gmail.com
 * http://php.net/manual/en/function.http-parse-headers.php#112986
 */
if (!function_exists('http_parse_headers')) {
    function http_parse_headers($raw_headers) {
        $headers = array();
        $key = '';

        foreach(explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                } else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t".trim($h[0]);
                } elseif (!$key){
                    $headers[0] = trim($h[0]);
                }
            }
        }

        return $headers;
    }
}

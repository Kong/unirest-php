<?php

namespace Unirest;

use Unirest\Method;
use Unirest\Response;

class Request
{
    private static $verifyPeer = true;
    private static $socketTimeout = null;
    private static $defaultHeaders = array();

    /**
     * Verify SSL peer
     * @param bool $enabled enable SSL verification, by default is true
     */
    public static function verifyPeer($enabled)
    {
        self::$verifyPeer = $enabled;
    }

    /**
     * Set a timeout
     * @param integer $seconds timeout value in seconds
     */
    public static function timeout($seconds)
    {
        self::$socketTimeout = $seconds;
    }

    /**
     * Set a new default header to send on every request
     * @param string $name header name
     * @param string $value header value
     */
    public static function defaultHeader($name, $value)
    {
        self::$defaultHeaders[$name] = $value;
    }

    /**
     * Clear all the default headers
     */
    public static function clearDefaultHeaders()
    {
        self::$defaultHeaders = array();
    }

    /**
     * Send a GET request to a URL
     * @param string $url URL to send the GET request to
     * @param array $headers additional headers to send
     * @param mixed $parameters parameters to send in the querystring
     * @param string $username Basic Authentication username
     * @param string $password Basic Authentication password
     * @return string|stdObj response string or stdObj if response is json-decodable
     */
    public static function get($url, $headers = array(), $parameters = null, $username = null, $password = null)
    {
        return self::send(Method::GET, $url, $parameters, $headers, $username, $password);
    }

    /**
     * Send POST request to a URL
     * @param string $url URL to send the POST request to
     * @param array $headers additional headers to send
     * @param mixed $body POST body data
     * @param string $username Basic Authentication username
     * @param string $password Basic Authentication password
     * @return string|stdObj response string or stdObj if response is json-decodable
     */
    public static function post($url, $headers = array(), $body = null, $username = null, $password = null)
    {
        return self::send(Method::POST, $url, $body, $headers, $username, $password);
    }

    /**
     * Send DELETE request to a URL
     * @param string $url URL to send the DELETE request to
     * @param array $headers additional headers to send
     * @param mixed $body DELETE body data
     * @param string $username Basic Authentication username
     * @param string $password Basic Authentication password
     * @return string|stdObj response string or stdObj if response is json-decodable
     */
    public static function delete($url, $headers = array(), $body = null, $username = null, $password = null)
    {
        return self::send(Method::DELETE, $url, $body, $headers, $username, $password);
    }

    /**
     * Send PUT request to a URL
     * @param string $url URL to send the PUT request to
     * @param array $headers additional headers to send
     * @param mixed $body PUT body data
     * @param string $username Basic Authentication username
     * @param string $password Basic Authentication password
     * @return string|stdObj response string or stdObj if response is json-decodable
     */
    public static function put($url, $headers = array(), $body = null, $username = null, $password = null)
    {
        return self::send(Method::PUT, $url, $body, $headers, $username, $password);
    }

    /**
     * Send PATCH request to a URL
     * @param string $url URL to send the PATCH request to
     * @param array $headers additional headers to send
     * @param mixed $body PATCH body data
     * @param string $username Basic Authentication username
     * @param string $password Basic Authentication password
     * @return string|stdObj response string or stdObj if response is json-decodable
     */
    public static function patch($url, $headers = array(), $body = null, $username = null, $password = null)
    {
        return self::send(Method::PATCH, $url, $body, $headers, $username, $password);
    }

    /**
     * Prepares a file for upload. To be used inside the parameters declaration for a request.
     * @param string $path The file path
     */
    public static function file($path)
    {
        if (function_exists('curl_file_create')) {
            return curl_file_create($path);
        } else {
            return '@' . $path;
        }
    }

    /**
     * This function is useful for serializing multidimensional arrays, and avoid getting
     * the 'Array to string conversion' notice
     */
    public static function buildHTTPCurlQuery($arrays, &$new = array(), $prefix = null)
    {
        if (is_object($arrays)) {
            $arrays = get_object_vars($arrays);
        }

        foreach ($arrays as $key => $value) {
            $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
            if (!$value instanceof \CURLFile and (is_array($value) or is_object($value))) {
                self::buildHTTPCurlQuery($value, $new, $k);
            } else {
                $new[$k] = $value;
            }
        }
    }

    /**
     * Send a cURL request
     * @param Unirest\Method $method HTTP method to use
     * @param string $url URL to send the request to
     * @param mixed $body request body
     * @param array $headers additional headers to send
     * @param string $username  Basic Authentication username
     * @param string $password  Basic Authentication password
     * @throws Exception if a cURL error occurs
     * @return Unirest\Response
     */
    private static function send($method, $url, $body = null, $headers = array(), $username = null, $password = null)
    {
        if ($headers == null) {
            $headers = array();
        }

        $lowercaseHeaders = array();
        $finalHeaders = array_merge($headers, self::$defaultHeaders);
        foreach ($finalHeaders as $key => $val) {
            $lowercaseHeaders[] = self::getHeader($key, $val);
        }

        $lowerCaseFinalHeaders = array_change_key_case($finalHeaders);

        if (!array_key_exists('user-agent', $lowerCaseFinalHeaders)) {
            $lowercaseHeaders[] = 'user-agent: unirest-php/2.0';
        }

        if (!array_key_exists('expect', $lowerCaseFinalHeaders)) {
            $lowercaseHeaders[] = 'expect:';
        }

        $ch = curl_init();

        if ($method != Method::GET) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            if (is_array($body) || $body instanceof \Traversable) {
                self::buildHTTPCurlQuery($body, $postBody);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        } elseif (is_array($body)) {
            if (strpos($url, '?') !== false) {
                $url .= '&';
            } else {
                $url .= '?';
            }

            self::buildHTTPCurlQuery($body, $postBody);
            $url .= urldecode(http_build_query($postBody));
        }

        curl_setopt($ch, CURLOPT_URL, self::encodeUrl($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $lowercaseHeaders);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, self::$verifyPeer);
        curl_setopt($ch, CURLOPT_ENCODING, ''); // If an empty string, '', is set, a header containing all supported encoding types is sent.

        if (self::$socketTimeout != null) {
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$socketTimeout);
        }

        if (!empty($username)) {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . ((empty($password)) ? '' : $password));
        }

        $response = curl_exec($ch);
        $error    = curl_error($ch);

        if ($error) {
            throw new \Exception($error);
        }

        // Split the full response in its headers and body
        $curl_info   = curl_getinfo($ch);
        $header_size = $curl_info['header_size'];
        $header      = substr($response, 0, $header_size);
        $body        = substr($response, $header_size);
        $httpCode    = $curl_info['http_code'];

        return new Response($httpCode, $body, $header);
    }

    private static function getArrayFromQuerystring($query)
    {
        $query = preg_replace_callback('/(?:^|(?<=&))[^=[]+/', function ($match) {
            return bin2hex(urldecode($match[0]));
        }, $query);

        parse_str($query, $values);

        return array_combine(array_map('hex2bin', array_keys($values)), $values);
    }

    /**
     * Ensure that a URL is encoded and safe to use with cURL
     * @param  string $url URL to encode
     * @return string
     */
    private static function encodeUrl($url)
    {
        $url_parsed = parse_url($url);

        $scheme = $url_parsed['scheme'] . '://';
        $host   = $url_parsed['host'];
        $port   = (isset($url_parsed['port']) ? $url_parsed['port'] : null);
        $path   = (isset($url_parsed['path']) ? $url_parsed['path'] : null);
        $query  = (isset($url_parsed['query']) ? $url_parsed['query'] : null);

        if ($query != null) {
            $query = '?' . http_build_query(self::getArrayFromQuerystring($query));
        }

        if ($port && $port[0] != ':') {
            $port = ':' . $port;
        }

        $result = $scheme . $host . $port . $path . $query;
        return $result;
    }

    private static function getHeader($key, $val)
    {
        $key = trim(strtolower($key));
        return $key . ': ' . $val;
    }
}

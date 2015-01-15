<?php

namespace Unirest;

use Unirest\Method;
use Unirest\Response;

class Request
{
    private static $jsonOpts = array();
    private static $verifyPeer = true;
    private static $socketTimeout = null;
    private static $defaultHeaders = array();

    /**
     * Set JSON decode mode
     *
     * @param bool $assoc When TRUE, returned objects will be converted into associative arrays.
     * @param bool $depth User specified recursion depth.
     * @param bool $options Bitmask of JSON decode options. Currently only JSON_BIGINT_AS_STRING is supported (default is to cast large integers as floats)
     */
    public static function jsonOpts($assoc = false, $depth = 512, $options = 0)
    {
        return self::$jsonOpts = array($assoc, $depth, $options);
    }

    /**
     * Verify SSL peer
     *
     * @param bool $enabled enable SSL verification, by default is true
     */
    public static function verifyPeer($enabled)
    {
        return self::$verifyPeer = $enabled;
    }

    /**
     * Set a timeout
     *
     * @param integer $seconds timeout value in seconds
     */
    public static function timeout($seconds)
    {
        return self::$socketTimeout = $seconds;
    }

    /**
     * Set default headers to send on every request
     *
     * @param array $headers headers array
     */
    public static function defaultHeaders($headers)
    {
        foreach ($headers as $name => $value) {
            self::$defaultHeaders[$name] = $value;
        }

        return $headers;
    }

    /**
     * Set a new default header to send on every request
     *
     * @param string $name header name
     * @param string $value header value
     */
    public static function defaultHeader($name, $value)
    {
        return self::$defaultHeaders[$name] = $value;
    }

    /**
     * Clear all the default headers
     */
    public static function clearDefaultHeaders()
    {
        return self::$defaultHeaders = array();
    }

    /**
     * Send a GET request to a URL
     *
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
     * Send a HEAD request to a URL
     * @param string $url URL to send the HEAD request to
     * @param array $headers additional headers to send
     * @param mixed $parameters parameters to send in the querystring
     * @param string $username Basic Authentication username
     * @param string $password Basic Authentication password
     * @return string|stdObj response string or stdObj if response is json-decodable
     */
    public static function head($url, $headers = array(), $parameters = null, $username = null, $password = null)
    {
        return self::send(Method::HEAD, $url, $parameters, $headers, $username, $password);
    }

    /**
     * Send a OPTIONS request to a URL
     * @param string $url URL to send the OPTIONS request to
     * @param array $headers additional headers to send
     * @param mixed $parameters parameters to send in the querystring
     * @param string $username Basic Authentication username
     * @param string $password Basic Authentication password
     * @return string|stdObj response string or stdObj if response is json-decodable
     */
    public static function options($url, $headers = array(), $parameters = null, $username = null, $password = null)
    {
        return self::send(Method::OPTIONS, $url, $parameters, $headers, $username, $password);
    }

    /**
     * Send a CONNECT request to a URL
     * @param string $url URL to send the CONNECT request to
     * @param array $headers additional headers to send
     * @param mixed $parameters parameters to send in the querystring
     * @param string $username Basic Authentication username
     * @param string $password Basic Authentication password
     * @return string|stdObj response string or stdObj if response is json-decodable
     */
    public static function connect($url, $headers = array(), $parameters = null, $username = null, $password = null)
    {
        return self::send(Method::CONNECT, $url, $parameters, $headers, $username, $password);
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
     * Send TRACE request to a URL
     * @param string $url URL to send the TRACE request to
     * @param array $headers additional headers to send
     * @param mixed $body TRACE body data
     * @param string $username Basic Authentication username
     * @param string $password Basic Authentication password
     * @return string|stdObj response string or stdObj if response is json-decodable
     */
    public static function trace($url, $headers = array(), $body = null, $username = null, $password = null)
    {
        return self::send(Method::TRACE, $url, $body, $headers, $username, $password);
    }

    /**
     * This function is useful for serializing multidimensional arrays, and avoid getting
     * the 'Array to string conversion' notice
     */
    public static function buildHTTPCurlQuery($data, $parent = false)
    {
        $result = array();

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        foreach ($data as $key => $value) {
            if ($parent) {
                $new_key = sprintf('%s[%s]', $parent, $key);
            } else {
                $new_key = $key;
            }

            if (!$value instanceof \CURLFile and (is_array($value) or is_object($value))) {
                $result = array_merge($result, self::buildHTTPCurlQuery($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }

        return $result;
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
    public static function send($method, $url, $body = null, $headers = array(), $username = null, $password = null)
    {
        $ch = curl_init();

        if ($method !== Method::GET) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            if (is_array($body) || $body instanceof \Traversable) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, self::buildHTTPCurlQuery($body));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        } elseif (is_array($body)) {
            if (strpos($url, '?') !== false) {
                $url .= '&';
            } else {
                $url .= '?';
            }

            $url .= urldecode(http_build_query(self::buildHTTPCurlQuery($body)));
        }

        curl_setopt($ch, CURLOPT_URL, self::encodeUrl($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::getFormattedHeaders($headers));
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, self::$verifyPeer);
        curl_setopt($ch, CURLOPT_ENCODING, ''); // If an empty string, '', is set, a header containing all supported encoding types is sent.

        if (self::$socketTimeout !== null) {
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

        return new Response($httpCode, $body, $header, self::$jsonOpts);
    }

    public static function getFormattedHeaders($headers)
    {
        $formattedHeaders = array();

        $combinedHeaders = array_change_key_case(array_merge((array) $headers, self::$defaultHeaders));

        foreach ($combinedHeaders as $key => $val) {
            $formattedHeaders[] = self::getHeaderString($key, $val);
        }

        if (!array_key_exists('user-agent', $combinedHeaders)) {
            $formattedHeaders[] = 'user-agent: unirest-php/2.0';
        }

        if (!array_key_exists('expect', $combinedHeaders)) {
            $formattedHeaders[] = 'expect:';
        }

        return $formattedHeaders;
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

        if ($query !== null) {
            $query = '?' . http_build_query(self::getArrayFromQuerystring($query));
        }

        if ($port && $port[0] !== ':') {
            $port = ':' . $port;
        }

        $result = $scheme . $host . $port . $path . $query;
        return $result;
    }

    private static function getHeaderString($key, $val)
    {
        $key = trim(strtolower($key));
        return $key . ': ' . $val;
    }
}

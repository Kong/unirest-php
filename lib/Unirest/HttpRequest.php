<?php

namespace Unirest;

class HttpRequest 
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
        Unirest::$verifyPeer = $enabled;
    }
    
    /**
     * Set a timeout
     * @param integer $seconds timeout value in seconds
     */
    public static function timeout($seconds)
    {
        Unirest::$socketTimeout = $seconds;
    }
    
    /**
     * Clear all the default headers
     */
    public static function clearDefaultHeaders()
    {
        Unirest::$defaultHeaders = array();
    }
    
    /**
     * @param HttpMethod $httpMethod is the method for sending the cURL request e.g., GET/POST/PUT/DELETE/PATCH
     */
    protected $httpMethod;

    /**
     * @param string $url is for sending the cURL request
     */
    protected $url;

    /**
     * @param mixed $body is the request body
     */
    protected $body = NULL;

    /**
     * @param array $headers is the collection of outgoing finalized headers
     */
    protected $headers = array();

    /**
     * @param string $username is the user name for Basic Authentication
     */
    protected $username = NULL;

    /**
     * @param string $password is the password for Basic Authentication
     */
    protected $password = NULL;

    /**
     * @param HttpMethod $httpMethod HTTP Method for invoking the cURL request
     * @param string $url URL for invoking the cURL request
     * @param string $headers raw header string from cURL request
     */
    public function __construct($httpMethod, $url, $body = NULL, $headers = array(), $username = NULL, $password = NULL)
    {
        $this->httpMethod = $httpMethod;
        $this->url = $url;
        $this->body = $body;
        $this->username = $username;
        $this->password = $password;

        $lowercaseHeaders = array();            
        $finalHeaders = array_merge($headers, HttpRequest::$defaultHeaders);        
        foreach ($finalHeaders as $key => $val) {
            $lowercaseHeaders[] = getHeader($key, $val);
        }

        $lowerCaseFinalHeaders = array_change_key_case($finalHeaders);
        if (!array_key_exists("user-agent", $lowerCaseFinalHeaders)) {
            $lowercaseHeaders[] = "user-agent: unirest-php/1.1";
        }
        if (!array_key_exists("expect", $lowerCaseFinalHeaders)) {
            $lowercaseHeaders[] = "expect:";
        }
        $this->headers = $lowercaseHeaders;
    }
	
    /**
     * Return a property of the response if it exists.
     * Possibilities include: code, raw_body, headers, body (if the response is json-decodable)
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            //UTF-8 is recommended for correct JSON serialization
            $value = $this->$property;
            if (is_string($value) && mb_detect_encoding($value, "UTF-8", TRUE) != "UTF-8") {
                return utf8_encode($value);
            }
            else {
                return $value;
            }
        }
    }
    
    /**
     * Set the properties of this object
     * @param string $property the property name
     * @param mixed $value the property value
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            //UTF-8 is recommended for correct JSON serialization
            if (is_string($value) && mb_detect_encoding($value, "UTF-8", TRUE) != "UTF-8") {
                $this->$property = utf8_encode($value);
            }
            else {
                $this->$property = $value;
            }
        }

        return $this;
    }
	
    /**
     * Prepares a file for upload. To be used inside the parameters declaration for a request.
     * @param string $path The file path
     */
    public static function file($path)
    {
        if (function_exists("curl_file_create")) {
            return curl_file_create($path);
        } else {
            return "@" . $path;
        }
    }
    
    /**
     * This function is useful for serializing multidimensional arrays, and avoid getting
     * the "Array to string conversion" notice
     */
    public static function http_build_query_for_curl($arrays, &$new = array(), $prefix = null)
    {
        if (is_object($arrays)) {
            $arrays = get_object_vars($arrays);
        }
        
        foreach ($arrays AS $key => $value) {
            $k = isset($prefix) ? $prefix . '[' . $key . ']' : $key;
            if (!$value instanceof \CURLFile AND (is_array($value) OR is_object($value))) {
                HttpRequest::http_build_query_for_curl($value, $new, $k);
            } else {
                $new[$k] = $value;
            }
        }
    }
	
    /**
     * Executes the cURL request and performs post processing to loading headers and response
     * @return HttpResponse
     */
    public function getResponse()
    {
        $ch = curl_init();
        if ($this->httpMethod != HttpMethod::GET) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->httpMethod);
            if (is_array($this->body) || $this->body instanceof Traversable) {
                HttpRequest::http_build_query_for_curl($this->body, $postBody);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
            }
        } else if (is_array($this->body)) {
            if (strpos($this->url, '?') !== false) {
                $this->url .= "&";
            } else {
                $this->url .= "?";
            }
            HttpRequest::http_build_query_for_curl($this->body, $postBody);
            $url .= urldecode(http_build_query($postBody));
        }

        curl_setopt($ch, CURLOPT_URL, HttpRequest::encodeUrl($this->url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, HttpRequest::$verifyPeer);
        curl_setopt($ch, CURLOPT_ENCODING, ""); // If an empty string, "", is set, a header containing all supported encoding types is sent.
        if (HttpRequest::$socketTimeout != null) {
            curl_setopt($ch, CURLOPT_TIMEOUT, HttpRequest::$socketTimeout);
        }
        if (!empty($this->username)) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . ((empty($this->password)) ? "" : $this->password));
        }

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        if ($error) {
            throw new Exception($error);
        }

        // Split the full response in its headers and body
        $curl_info   = curl_getinfo($ch);
        $header_size = $curl_info["header_size"];
        $header      = substr($response, 0, $header_size);
        $body        = substr($response, $header_size);
        $httpCode    = $curl_info["http_code"];

        return new HttpResponse($httpCode, $body, $header);
    }
		    
    private static function getArrayFromQuerystring($querystring)
    {
        $pairs = explode("&", $querystring);
        $vars  = array();
        foreach ($pairs as $pair) {
            $nv          = explode("=", $pair, 2);
            $name        = $nv[0];
            $value       = $nv[1];
            $vars[$name] = $value;
        }
        return $vars;
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
            $query = '?' . http_build_query(HttpRequest::getArrayFromQuerystring($url_parsed['query']));
        }
        
        if ($port && $port[0] != ":")
            $port = ":" . $port;
        
        $result = $scheme . $host . $port . $path . $query;
        return $result;
    }
    
    private static function getHeader($key, $val)
    {
        $key = trim(strtolower($key));
        return $key . ": " . $val;
    }    
}

if (!function_exists('http_chunked_decode')) {
	/**
	 * Dechunk an http 'transfer-encoding: chunked' message 
	 * @param string $chunk the encoded message 
	 * @return string the decoded message
	 */
	function http_chunked_decode($chunk)
	{
		$pos     = 0;
		$len     = strlen($chunk);
		$dechunk = null;
		
		while (($pos < $len) && ($chunkLenHex = substr($chunk, $pos, ($newlineAt = strpos($chunk, "\n", $pos + 1)) - $pos))) {
			
			if (!is_hex($chunkLenHex)) {
				trigger_error('Value is not properly chunk encoded', E_USER_WARNING);
				return $chunk;
			}
			
			$pos      = $newlineAt + 1;
			$chunkLen = hexdec(rtrim($chunkLenHex, "\r\n"));
			$dechunk .= substr($chunk, $pos, $chunkLen);
			$pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
		}
		
		return $dechunk;
	}
}

/**
 * determine if a string can represent a number in hexadecimal 
 * @link http://uk1.php.net/ctype_xdigit
 * @param string $hex 
 * @return boolean true if the string is a hex, otherwise false 
 */
function is_hex($hex)
{
	return ctype_xdigit($hex);
}
?>
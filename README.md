# Unirest for PHP

Unirest is a set of lightweight HTTP libraries available in multiple languages.

Created with love by http://mashape.com

### Install with Composer
If you're using [Composer](https://github.com/composer/composer) to manage
dependencies, you can add Unirest with it.

```javascript
{
  "require" : {
    "mashape/unirest-php" : "dev-master"
  },
  "autoload": {
    "psr-0": {"Unirest": "lib/"}
  }
}
```

### Install source from GitHub
Unirest-PHP requires PHP `v5.3+`. Download the PHP library from Github, and require in your script like so:

To install the source code:

```bash
$ git clone git@github.com:Mashape/unirest-php.git 
```

And include it in your scripts:

```bash
require_once '/path/to/unirest-php/lib/Unirest.php';
```

## Creating Request
So you're probably wondering how using Unirest makes creating requests in PHP easier, let's look at a working example:

```php
$response = Unirest::post("http://httpbin.org/post", array( "Accept" => "application/json" ),
  array(
    "parameter" => 23,
    "foo" => "bar"
  )
);
```

## File Uploads
To upload files in a multipart form representation simply place an @ symbol before the path:

```php
$response = Unirest::post("http://httpbin.org/post", array( "Accept" => "application/json" ),
  array(
    "file" => "@/tmp/file.txt"
  )
);
 ```
 
## Custom Entity Body
Sending a custom body such as a JSON Object rather than a string or form style parameters we utilize json_encode for the body:
```php
$response = Unirest::post("http://httpbin.org/post", array( "Accept" => "application/json" ),
  json_encode(
    array(
      "parameter" => "value",
      "foo" => "bar"
    )
  )
);
```

# Request
```php
Unirest::get($url, $headers = array());
Unirest::post($url, $headers = array(), $body = NULL);
Unirest::put($url, $headers = array(), $body = NULL);
Unirest::patch($url, $headers = array(), $body = NULL);
Unirest::delete($url, $headers = array());
```
  
- `url` - Endpoint, address, or uri to be acted upon and requested information from.
- `headers` - Request Headers as associative array or object
- `body` - Request Body associative array or object

# Response
Upon recieving a response Unirest returns the result in the form of an Object, this object should always have the same keys for each language regarding to the response details.

- `code` - HTTP Response Status Code (Example `200`)
- `headers` - HTTP Response Headers
- `body` - Parsed response body where applicable, for example JSON responses are parsed to Objects / Associative Arrays.
- `raw_body` - Un-parsed response body

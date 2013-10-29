# Unirest for PHP

Unirest is a set of lightweight HTTP libraries available in multiple languages.

Created with love by http://mashape.com



### Installing
Unirest-PHP requires PHP `v5.3+`. Download the PHP library from Github, and require in your script like so:

```php
require_once './lib/Unirest.php';
```

### Using Composer

[Composer](http://getcomposer.org/) is a package manager for PHP.

In the composer.json file in your project add:

```javascript
{
  "require" : {
    "mashape/unirest-php" : "dev-master"
  }
}
```
And then run:

```
php composer.phar install
```

Include the library in your project with:

```php
require 'vendor/autoload.php';
````

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

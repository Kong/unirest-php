Unirest-PHP
============================================

Unirest is a set of lightweight HTTP libraries available in PHP, Ruby, Python, Java, Objective-C.

Documentation
-------------------

### Installing
Download the PHP library from Github, and require in your script like so:

```php
require_once './lib/Unirest/Unirest.php';
```

#### Using Composer

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

### Creating Request
So you're probably wondering how using Unirest makes creating requests in PHP easier, let's look at a working example:

```php
$response = Unirest::post("http://httpbin.org/post", array( "Accept" => "application/json" ),
  array(
    "parameter" => 23,
    "foo" => "bar"
  )
);
```

### File Uploads
To upload files in a multipart form representation simply place an @ symbol before the path:

```php
$response = Unirest::post("http://httpbin.org/post", array( "Accept" => "application/json" ),
  array(
    "file" => "@/tmp/file.txt"
  )
);
 ```
 
### Custom Entity Body
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

### Request Reference
```php
Unirest::get($url, $headers = array());
Unirest::post($url, $headers = array(), $body = NULL);
Unirest::put($url, $headers = array(), $body = NULL);
Unirest::patch($url, $headers = array(), $body = NULL);
Unirest::delete($url, $headers = array());
```
  
`url`
Endpoint, address, or uri to be acted upon and requested information from.

`headers`
Request Headers as associative array or object

`body`
Request Body associative array or object

### Response Reference
Upon recieving a response Unirest returns the result in the form of an Object, this object should always have the same keys for each language regarding to the response details.

`code`
HTTP Response Status Code (Example `200`)

`headers`
HTTP Response Headers

`body`
Parsed response body where applicable, for example JSON responses are parsed to Objects / Associative Arrays.

`raw_body`
Un-parsed response body

License
---------------

The MIT License

Copyright (c) 2013 Mashape (http://mashape.com)

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

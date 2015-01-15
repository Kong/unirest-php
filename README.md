# Unirest for PHP ![GitHub version][github-image] [![version][composer-image]][composer-url]

[![Build Status][travis-image]][travis-url]
[![Code Climate][codeclimate-image]][codeclimate-url]
[![Coverage Status][codecoverage-image]][codecoverage-url]
[![Dependency Status][dependency-image]][dependency-url]

Unirest is a set of lightweight HTTP libraries available in multiple languages, ideal for most applications:

## Features

* Utility methods to call `GET`, `HEAD`, `POST`, `PUT`, `DELETE`, `CONNECT`, `OPTIONS`, `TRACE`, `PATCH` requests
* Supports form parameters, file uploads and custom body entities
* Supports gzip
* Supports Basic Authentication natively
* Customizable timeout
* Customizable default headers for every request (DRY)
* Automatic JSON parsing into a native object for JSON responses

## Installation

### [Componser](https://getcomposer.org)

To install unirest-php with Composer, just add the following to your `composer.json` file:

```json
// composer.json
{
    "require-dev": {
        "mashape/unirest-php": "2.*"
    }
}
```

or by running the following command:

```shell
composer require mashape/unirest-php
```

This will get you the latest version of the reporter and install it. If you do want the master, untagged, version you may use the command below:

```shell
composer require mashape/php-test-reporter:@dev-master
```

Composer installs autoloader at `./vendor/autoloader.php`. to include the library in your script, add:

```php
require_once 'vendor/autoload.php';
```

If you use Symfony2, autoloader has to be detected automatically.

*You can see this library on [Packagist](https://packagist.org/packages/mashape/unirest-php).*

### Install from source

Unirest-PHP requires PHP `v5.4+`. Download the PHP library from Github, then include `Unirest.php` in your script:

```shell
git clone git@github.com:Mashape/unirest-php.git 
```

```php
require_once '/path/to/unirest-php/src/Unirest.php';
```

## Usage

### Creating a Request

So you're probably wondering how using Unirest makes creating requests in PHP easier, let's look at a working example:

```php
$headers = array("Accept" => "application/json");
$body = array("foo" => "hellow", "bar" => "world");

$response = Unirest\Request::post("http://httpbin.org/post", $headers, $body);

$response->code;        // HTTP Status code
$response->headers;     // Headers
$response->body;        // Parsed body
$response->raw_body;    // Unparsed body
```

### File Uploads

To upload files in a multipart form representation use the return value of `Unirest::file($path)` as the value of a parameter:

```php
$headers = array("Accept" => "application/json");
$body = array("file" => Unirest\File::add("/tmp/file.txt"));

$response = Unirest\Request::post("http://httpbin.org/post", $headers, $body);
 ```
 
### Custom Entity Body

Sending a custom body such as a JSON Object rather than a string or form style parameters we utilize json_encode for the body:
```php
$headers = array("Accept" => "application/json");
$body =   json_encode(array("foo" => "hellow", "bar" => "world"));

$response = Unirest\Request::post("http://httpbin.org/post", $headers, $body);
```

### Basic Authentication

Authenticating the request with basic authentication can be done by providing the `username` and `password` arguments:

```php
$response = Unirest\Request::get("http://httpbin.org/get", null, null, "username", "password");
```

### Request Object

```php
Unirest\Request::get($url, $headers = array(), $parameters = NULL, $username = NULL, $password = NULL)
Unirest\Request::post($url, $headers = array(), $body = NULL, $username = NULL, $password = NULL)
Unirest\Request::put($url, $headers = array(), $body = NULL, $username = NULL, $password = NULL)
Unirest\Request::patch($url, $headers = array(), $body = NULL, $username = NULL, $password = NULL)
Unirest\Request::delete($url, $headers = array(), $body = NULL, $username = NULL, $password = NULL)
```
  
- `url` - Endpoint, address, or uri to be acted upon and requested information from.
- `headers` - Request Headers as associative array or object
- `body` - Request Body as associative array or object
- `username` - Basic Authentication username
- `password` - Basic Authentication password

### Response Object

Upon recieving a response Unirest returns the result in the form of an Object, this object should always have the same keys for each language regarding to the response details.

- `code` - HTTP Response Status Code (Example `200`)
- `headers` - HTTP Response Headers
- `body` - Parsed response body where applicable, for example JSON responses are parsed to Objects / Associative Arrays.
- `raw_body` - Un-parsed response body

### Advanced Configuration

You can set some advanced configuration to tune Unirest-PHP:

#### Timeout

You can set a custom timeout value (in **seconds**):

```php
Unirest\Request::timeout(5); // 5s timeout
```

#### Default Request Headers

You can set default headers that will be sent on every request:

```php
Unirest\Request::defaultHeader("Header1", "Value1");
Unirest\Request::defaultHeader("Header2", "Value2");
```

You can clear the default headers anytime with:

```php
Unirest\Request::clearDefaultHeaders();
```

#### SSL validation

You can explicitly enable or disable SSL certificate validation when consuming an SSL protected endpoint:

```php
Unirest\Request::verifyPeer(false); // Disables SSL cert validation
```

By default is `true`.

## License

Licensed under [the MIT license](LICENSE).

Created with love by [Mashape](https://www.mashape.com)

[github-image]: https://badge.fury.io/gh/mashape%2Funirest-php.svg

[composer-url]: http://badge.fury.io/ph/mashape%2Funirest-php
[composer-image]: https://badge.fury.io/ph/mashape%2Funirest-php.svg

[travis-url]: https://travis-ci.org/Mashape/unirest-php
[travis-image]: https://travis-ci.org/Mashape/unirest-php.png?branch=master

[codeclimate-url]: https://codeclimate.com/github/Mashape/unirest-php
[codeclimate-image]: https://codeclimate.com/github/Mashape/unirest-php/badges/gpa.svg

[codecoverage-url]: https://codeclimate.com/github/Mashape/unirest-php
[codecoverage-image]: https://codeclimate.com/github/Mashape/unirest-php/badges/coverage.svg

[dependency-url]: https://www.versioneye.com/user/projects/54b702db050646ca5c00019d
[dependency-image]: https://www.versioneye.com/user/projects/54b702db050646ca5c00019d/badge.svg?style=flat

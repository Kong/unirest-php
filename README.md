# Unirest for PHP [![Build Status][travis-image]][travis-url] [![version][packagist-version]][packagist-url]

[![Downloads][packagist-downloads]][packagist-url]
[![Code Climate][codeclimate-quality]][codeclimate-url]
[![Coverage Status][codeclimate-coverage]][codeclimate-url]
[![Dependencies][versioneye-image]][versioneye-url]
[![Gitter][gitter-image]][gitter-url]
[![License][packagist-license]][license-url]

![][unirest-logo]


[Unirest](http://unirest.io) is a set of lightweight HTTP libraries available in multiple languages, built and maintained by [Mashape](https://github.com/Mashape), who also maintain the open-source API Gateway [Kong](https://github.com/Mashape/kong). 


## Features

* Utility methods to call `GET`, `HEAD`, `POST`, `PUT`, `DELETE`, `CONNECT`, `OPTIONS`, `TRACE`, `PATCH` requests
* Supports form parameters, file uploads and custom body entities
* Supports gzip
* Supports Basic, Digest, Negotiate, NTLM Authentication natively
* Customizable timeout
* Customizable default headers for every request (DRY)
* Automatic JSON parsing into a native object for JSON responses

## Requirements

- [cURL](http://php.net/manual/en/book.curl.php)
- PHP 5.4+

## Installation

### Using [Composer](https://getcomposer.org)

To install unirest-php with Composer, just add the following to your `composer.json` file:

```json
{
    "require-dev": {
        "mashape/unirest-php": "3.*"
    }
}
```

or by running the following command:

```shell
composer require mashape/unirest-php
```

This will get you the latest version of the reporter and install it. If you do want the master, untagged, version you may use the command below:

```shell
composer require mashape/php-test-reporter dev-master
```

Composer installs autoloader at `./vendor/autoloader.php`. to include the library in your script, add:

```php
require_once 'vendor/autoload.php';
```

If you use Symfony2, autoloader has to be detected automatically.

*You can see this library on [Packagist](https://packagist.org/packages/mashape/unirest-php).*

### Install from source

Download the PHP library from Github, then include `Unirest.php` in your script:

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
$headers = array('Accept' => 'application/json');
$query = array('foo' => 'hello', 'bar' => 'world');

$response = Unirest\Request::post('http://mockbin.com/request', $headers, $query);

$response->code;        // HTTP Status code
$response->headers;     // Headers
$response->body;        // Parsed body
$response->raw_body;    // Unparsed body
```

### JSON Requests *(`application/json`)*

A JSON Request can be constructed using the `Unirest\Request\Body::Json` helper:

```php
$headers = array('Accept' => 'application/json');
$data = array('name' => 'ahmad', 'company' => 'mashape');

$body = Unirest\Request\Body::json($data);

$response = Unirest\Request::post('http://mockbin.com/request', $headers, $body);
```

**Notes:**
- `Content-Type` headers will be automatically set to `application/json` 
- the data variable will be processed through [`json_encode`](http://php.net/manual/en/function.json-encode.php) with default values for arguments.
- an error will be thrown if the [JSON Extension](http://php.net/manual/en/book.json.php) is not available.

### Form Requests *(`application/x-www-form-urlencoded`)*

A typical Form Request can be constructed using the `Unirest\Request\Body::Form` helper:

```php
$headers = array('Accept' => 'application/json');
$data = array('name' => 'ahmad', 'company' => 'mashape');

$body = Unirest\Request\Body::form($data);

$response = Unirest\Request::post('http://mockbin.com/request', $headers, $body);
```

**Notes:** 
- `Content-Type` headers will be automatically set to `application/x-www-form-urlencoded`
- the final data array will be processed through [`http_build_query`](http://php.net/manual/en/function.http-build-query.php) with default values for arguments.

### Multipart Requests *(`multipart/form-data`)*

A Multipart Request can be constructed using the `Unirest\Request\Body::Multipart` helper:

```php
$headers = array('Accept' => 'application/json');
$data = array('name' => 'ahmad', 'company' => 'mashape');

$body = Unirest\Request\Body::multipart($data);

$response = Unirest\Request::post('http://mockbin.com/request', $headers, $body);
```

**Notes:** 

- `Content-Type` headers will be automatically set to `multipart/form-data`.
- an auto-generated `--boundary` will be set.

### Multipart File Upload

simply add an array of files as the second argument to to the `Multipart` helper:

```php
$headers = array('Accept' => 'application/json');
$data = array('name' => 'ahmad', 'company' => 'mashape');
$files = array('bio' => '/path/to/bio.txt', 'avatar' => '/path/to/avatar.jpg');

$body = Unirest\Request\Body::multipart($data, $files);

$response = Unirest\Request::post('http://mockbin.com/request', $headers, $body);
 ```

If you wish to further customize the properties of files uploaded you can do so with the `Unirest\Request\Body::File` helper:

```php
$headers = array('Accept' => 'application/json');
$body = array(
    'name' => 'ahmad', 
    'company' => 'mashape'
    'bio' => Unirest\Request\Body::file('/path/to/bio.txt', 'text/plain'),
    'avatar' => Unirest\Request\Body::file('/path/to/my_avatar.jpg', 'text/plain', 'avatar.jpg')
);

$response = Unirest\Request::post('http://mockbin.com/request', $headers, $body);
 ```

**Note**: we did not use the `Unirest\Request\Body::multipart` helper in this example, it is not needed when manually adding files.
 
### Custom Body

Sending a custom body such rather than using the `Unirest\Request\Body` helpers is also possible, for example, using a [`serialize`](http://php.net/manual/en/function.serialize.php) body string with a custom `Content-Type`:

```php
$headers = array('Accept' => 'application/json', 'Content-Type' => 'application/x-php-serialized');
$body = serialize((array('foo' => 'hello', 'bar' => 'world'));

$response = Unirest\Request::post('http://mockbin.com/request', $headers, $body);
```

### Authentication

First, if you are using [Mashape][mashape-url]:
```php
// Mashape auth
Unirest\Request::setMashapeKey('<mashape_key>');
```

Otherwise, passing a username, password *(optional)*, defaults to Basic Authentication:

```php
// basic auth
Unirest\Request::auth('username', 'password');
```

The third parameter, which is a bitmask, will Unirest which HTTP authentication method(s) you want it to use for your proxy authentication.

If more than one bit is set, Unirest *(at PHP's libcurl level)* will first query the site to see what authentication methods it supports and then pick the best one you allow it to use. *For some methods, this will induce an extra network round-trip.*

**Supported Methods**

| Method               | Description                                                                                                                                                                                                     |
| -------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `CURLAUTH_BASIC`     | HTTP Basic authentication. This is the default choice                                                                                                                                                           | 
| `CURLAUTH_DIGEST`    | HTTP Digest authentication. as defined in [RFC 2617](http://www.ietf.org/rfc/rfc2617.txt)                                                                                                                       | 
| `CURLAUTH_DIGEST_IE` | HTTP Digest authentication with an IE flavor. *The IE flavor is simply that libcurl will use a special "quirk" that IE is known to have used before version 7 and that some servers require the client to use.* | 
| `CURLAUTH_NEGOTIATE` | HTTP Negotiate (SPNEGO) authentication. as defined in [RFC 4559](http://www.ietf.org/rfc/rfc4559.txt)                                                                                                           |
| `CURLAUTH_NTLM`      | HTTP NTLM authentication. A proprietary protocol invented and used by Microsoft.                                                                                                                                |
| `CURLAUTH_NTLM_WB`   | NTLM delegating to winbind helper. Authentication is performed by a separate binary application. *see [libcurl docs](http://curl.haxx.se/libcurl/c/CURLOPT_HTTPAUTH.html) for more info*                        | 
| `CURLAUTH_ANY`       | This is a convenience macro that sets all bits and thus makes libcurl pick any it finds suitable. libcurl will automatically select the one it finds most secure.                                               |
| `CURLAUTH_ANYSAFE`   | This is a convenience macro that sets all bits except Basic and thus makes libcurl pick any it finds suitable. libcurl will automatically select the one it finds most secure.                                  |
| `CURLAUTH_ONLY`      | This is a meta symbol. OR this value together with a single specific auth value to force libcurl to probe for un-restricted auth and if not, only that single auth algorithm is acceptable.                     |

```php
// custom auth method
Unirest\Request::proxyAuth('username', 'password', CURLAUTH_DIGEST);
```

Previous versions of **Unirest** support *Basic Authentication* by providing the `username` and `password` arguments:

```php
$response = Unirest\Request::get('http://mockbin.com/request', null, null, 'username', 'password');
```

**This has been deprecated, and will be completely removed in `v.3.0.0` please use the `Unirest\Request::auth()` method instead**

### Cookies

Set a cookie string to specify the contents of a cookie header. Multiple cookies are separated with a semicolon followed by a space (e.g., "fruit=apple; colour=red")

```php
Unirest\Request::cookie($cookie)
```

Set a cookie file path for enabling cookie reading and storing cookies across multiple sequence of requests.

```php
Unirest\Request::cookieFile($cookieFile)
```

`$cookieFile` must be a correct path with write permission.

### Request Object

```php
Unirest\Request::get($url, $headers = array(), $parameters = null)
Unirest\Request::post($url, $headers = array(), $body = null)
Unirest\Request::put($url, $headers = array(), $body = null)
Unirest\Request::patch($url, $headers = array(), $body = null)
Unirest\Request::delete($url, $headers = array(), $body = null)
```
  
- `url` - Endpoint, address, or uri to be acted upon and requested information from.
- `headers` - Request Headers as associative array or object
- `body` - Request Body as associative array or object

You can send a request with any [standard](http://www.iana.org/assignments/http-methods/http-methods.xhtml) or custom HTTP Method:

```php
Unirest\Request::send(Unirest\Method::LINK, $url, $headers = array(), $body);

Unirest\Request::send('CHECKOUT', $url, $headers = array(), $body);
```

### Response Object

Upon recieving a response Unirest returns the result in the form of an Object, this object should always have the same keys for each language regarding to the response details.

- `code` - HTTP Response Status Code (Example `200`)
- `headers` - HTTP Response Headers
- `body` - Parsed response body where applicable, for example JSON responses are parsed to Objects / Associative Arrays.
- `raw_body` - Un-parsed response body

### Advanced Configuration

You can set some advanced configuration to tune Unirest-PHP:

#### Custom JSON Decode Flags

Unirest uses PHP's [JSON Extension](http://php.net/manual/en/book.json.php) for automatically decoding JSON responses.
sometime you may want to return associative arrays, limit the depth of recursion, or use any of the [customization flags](http://php.net/manual/en/json.constants.php).

To do so, simply set the desired options using the `jsonOpts` request method:

```php
Unirest\Request::jsonOpts(true, 512, JSON_NUMERIC_CHECK & JSON_FORCE_OBJECT & JSON_UNESCAPED_SLASHES);
```

#### Timeout

You can set a custom timeout value (in **seconds**):

```php
Unirest\Request::timeout(5); // 5s timeout
```

#### Proxy

Set the proxy to use for the upcoming request.

you can also set the proxy type to be one of `CURLPROXY_HTTP`, `CURLPROXY_HTTP_1_0`, `CURLPROXY_SOCKS4`, `CURLPROXY_SOCKS5`, `CURLPROXY_SOCKS4A`, and `CURLPROXY_SOCKS5_HOSTNAME`.

*check the [cURL docs](http://curl.haxx.se/libcurl/c/CURLOPT_PROXYTYPE.html) for more info*.

```php
// quick setup with default port: 1080
Unirest\Request::proxy('10.10.10.1');

// custom port and proxy type
Unirest\Request::proxy('10.10.10.1', 8080, CURLPROXY_HTTP);

// enable tunneling
Unirest\Request::proxy('10.10.10.1', 8080, CURLPROXY_HTTP, true);
```

##### Proxy Authenticaton

Passing a username, password *(optional)*, defaults to Basic Authentication:

```php
// basic auth
Unirest\Request::proxyAuth('username', 'password');
```

The third parameter, which is a bitmask, will Unirest which HTTP authentication method(s) you want it to use for your proxy authentication. 

If more than one bit is set, Unirest *(at PHP's libcurl level)* will first query the site to see what authentication methods it supports and then pick the best one you allow it to use. *For some methods, this will induce an extra network round-trip.*

See [Authentication](#authentication) for more details on methods supported.

```php
// basic auth
Unirest\Request::proxyAuth('username', 'password', CURLAUTH_DIGEST);
```

#### Default Request Headers

You can set default headers that will be sent on every request:

```php
Unirest\Request::defaultHeader('Header1', 'Value1');
Unirest\Request::defaultHeader('Header2', 'Value2');
```

You can set default headers in bulk by passing an array:

```php
Unirest\Request::defaultHeaders(array(
    'Header1' => 'Value1',
    'Header2' => 'Value2'
));
```

You can clear the default headers anytime with:

```php
Unirest\Request::clearDefaultHeaders();
```

#### Default cURL Options

You can set default [cURL options](http://php.net/manual/en/function.curl-setopt.php) that will be sent on every request:

```php
Unirest\Request::curlOpt(CURLOPT_COOKIE, 'foo=bar');
```

You can set options bulk by passing an array:

```php
Unirest\Request::curlOpts(array(
    CURLOPT_COOKIE => 'foo=bar'
));
```

You can clear the default options anytime with:

```php
Unirest\Request::clearCurlOpts();
```

#### SSL validation

You can explicitly enable or disable SSL certificate validation when consuming an SSL protected endpoint:

```php
Unirest\Request::verifyPeer(false); // Disables SSL cert validation
```

By default is `true`.

#### Utility Methods

```php
// alias for `curl_getinfo`
Unirest\Request::getInfo()

// returns internal cURL handle
Unirest\Request::getCurlHandle()
```

----

Made with &#9829; from the [Mashape][mashape-url] team

[unirest-logo]: http://cl.ly/image/2P373Y090s2O/Image%202015-10-12%20at%209.48.06%20PM.png


[mashape-url]: https://www.mashape.com/

[license-url]: https://github.com/Mashape/unirest-php/blob/master/LICENSE

[gitter-url]: https://gitter.im/Mashape/unirest-php
[gitter-image]: https://img.shields.io/badge/Gitter-Join%20Chat-blue.svg?style=flat

[travis-url]: https://travis-ci.org/Mashape/unirest-php
[travis-image]: https://img.shields.io/travis/Mashape/unirest-php.svg?style=flat

[packagist-url]: https://packagist.org/packages/Mashape/unirest-php
[packagist-license]: https://img.shields.io/packagist/l/Mashape/unirest-php.svg?style=flat
[packagist-version]: https://img.shields.io/packagist/v/Mashape/unirest-php.svg?style=flat
[packagist-downloads]: https://img.shields.io/packagist/dm/Mashape/unirest-php.svg?style=flat

[codeclimate-url]: https://codeclimate.com/github/Mashape/unirest-php
[codeclimate-quality]: https://img.shields.io/codeclimate/github/Mashape/unirest-php.svg?style=flat
[codeclimate-coverage]: https://img.shields.io/codeclimate/coverage/github/Mashape/unirest-php.svg?style=flat

[versioneye-url]: https://www.versioneye.com/user/projects/54b82450050646ca5c0001f3
[versioneye-image]: https://img.shields.io/versioneye/d/php/mashape:unirest-php.svg?style=flat

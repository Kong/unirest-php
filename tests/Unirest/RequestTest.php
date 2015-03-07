<?php

class UnirestRequestTest extends \PHPUnit_Framework_TestCase
{
    // Generic
    public function testHttpBuildQueryWhenCurlFile()
    {
        $file = Unirest\File::add(UPLOAD_FIXTURE);
        $body = array(
            'to' => 'mail@mailinator.com',
            'from' => 'mail@mailinator.com',
            'file' => $file
        );

        $result = Unirest\Request::buildHTTPCurlQuery($body);
        $this->assertEquals($result['file'], $file);
    }

    /**
     * @expectedException Exception
     */
    public function testTimeoutFail()
    {
        Unirest\Request::timeout(1);

        Unirest\Request::get('http://mockbin.com/delay/3000');

        Unirest\Request::timeout(null); // Cleaning timeout for the other tests
    }

/*
    public function testTimeoutSuccess()
    {
        Unirest\Request::timeout(3);

        $response = Unirest\Request::get('http://mockbin.com/delay/2000');
        $this->assertEquals(200, $response->code);

        Unirest\Request::timeout(null); // Cleaning timeout for the other tests
    }
*/

    public function testDefaultHeader()
    {
        Unirest\Request::defaultHeader('Hello', 'custom');

        $response = Unirest\Request::get('http://mockbin.com/request');

        $this->assertEquals(200, $response->code);
        $this->assertTrue(property_exists($response->body->headers, 'hello'));
        $this->assertEquals('custom', $response->body->headers->hello);

        Unirest\Request::clearDefaultHeaders();

        $response = Unirest\Request::get('http://mockbin.com/request');

        $this->assertEquals(200, $response->code);
        $this->assertFalse(property_exists($response->body->headers, 'hello'));
    }

    public function testSetMashapeKey()
    {
        Unirest\Request::setMashapeKey('abcd');

        $response = Unirest\Request::get('http://mockbin.com/request');

        $this->assertEquals(200, $response->code);
        $this->assertTrue(property_exists($response->body->headers, 'x-mashape-key'));
        $this->assertEquals('abcd', $response->body->headers->{'x-mashape-key'});

        // send another request
        $response = Unirest\Request::get('http://mockbin.com/request');

        $this->assertEquals(200, $response->code);
        $this->assertTrue(property_exists($response->body->headers, 'x-mashape-key'));
        $this->assertEquals('abcd', $response->body->headers->{'x-mashape-key'});

        Unirest\Request::clearDefaultHeaders();

        $response = Unirest\Request::get('http://mockbin.com/request');

        $this->assertEquals(200, $response->code);
        $this->assertFalse(property_exists($response->body->headers, 'x-mashape-key'));
    }

    public function testGzip()
    {
        $response = Unirest\Request::get('http://mockbin.com/gzip/request');

        $this->assertEquals('gzip', $response->headers['Content-Encoding']);
    }

    public function testBasicAuthenticationDeprecated()
    {
        $response = Unirest\Request::get('http://mockbin.com/request', array(), array(), 'user', 'password');

        $this->assertEquals('Basic dXNlcjpwYXNzd29yZA==', $response->body->headers->authorization);
    }

    public function testBasicAuthentication()
    {
        Unirest\Request::auth('user', 'password');

        $response = Unirest\Request::get('http://mockbin.com/request');

        $this->assertEquals('Basic dXNlcjpwYXNzd29yZA==', $response->body->headers->authorization);
    }

    public function testCustomHeaders()
    {
        $response = Unirest\Request::get('http://mockbin.com/request', array(
            'user-agent' => 'unirest-php',
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('unirest-php', $response->body->headers->{'user-agent'});
    }

    // GET
    public function testGet()
    {
        $response = Unirest\Request::get('http://mockbin.com/request?name=Mark', array(
            'Accept' => 'application/json'
        ), array(
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('GET', $response->body->method);
        $this->assertEquals('Mark', $response->body->queryString->name);
        $this->assertEquals('thefosk', $response->body->queryString->nick);
    }

    public function testGetMultidimensionalArray()
    {
        $response = Unirest\Request::get('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'key' => 'value',
            'items' => array(
                'item1',
                'item2'
            )
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('GET', $response->body->method);
        $this->assertEquals('value', $response->body->queryString->key);
        $this->assertEquals('item1', $response->body->queryString->items[0]);
        $this->assertEquals('item2', $response->body->queryString->items[1]);
    }

    public function testGetWithDots()
    {
        $response = Unirest\Request::get('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'user.name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('GET', $response->body->method);
        $this->assertEquals('Mark', $response->body->queryString->{'user.name'});
        $this->assertEquals('thefosk', $response->body->queryString->nick);
    }

    public function testGetWithDotsAlt()
    {
        $response = Unirest\Request::get('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'user.name' => 'Mark Bond',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('GET', $response->body->method);
        $this->assertEquals('Mark Bond', $response->body->queryString->{'user.name'});
        $this->assertEquals('thefosk', $response->body->queryString->nick);
    }
    public function testGetWithEqualSign()
    {
        $response = Unirest\Request::get('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark=Hello'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('GET', $response->body->method);
        $this->assertEquals('Mark=Hello', $response->body->queryString->name);
    }

    public function testGetWithEqualSignAlt()
    {
        $response = Unirest\Request::get('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark=Hello=John'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('GET', $response->body->method);
        $this->assertEquals('Mark=Hello=John', $response->body->queryString->name);
    }

    public function testGetWithComplexQuery()
    {
        $response = Unirest\Request::get('http://mockbin.com/request?query=[{"type":"/music/album","name":null,"artist":{"id":"/en/bob_dylan"},"limit":3}]&cursor');

        $this->assertEquals(200, $response->code);
        $this->assertEquals('GET', $response->body->method);
        $this->assertEquals('', $response->body->queryString->cursor);
        $this->assertEquals('[{"type":"/music/album","name":null,"artist":{"id":"/en/bob_dylan"},"limit":3}]', $response->body->queryString->query);
    }

    public function testGetArray()
    {
        $response = Unirest\Request::get('http://mockbin.com/request', array(), array(
            'name[0]' => 'Mark',
            'name[1]' => 'John'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('GET', $response->body->method);
        $this->assertEquals('Mark', $response->body->queryString->name[0]);
        $this->assertEquals('John', $response->body->queryString->name[1]);
    }

    // POST
    public function testPost()
    {
        $response = Unirest\Request::post('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('POST', $response->body->method);
        $this->assertEquals('Mark', $response->body->postData->params->name);
        $this->assertEquals('thefosk', $response->body->postData->params->nick);
    }

    public function testPostWithEqualSign()
    {
        $response = Unirest\Request::post('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark=Hello'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('POST', $response->body->method);
        $this->assertEquals('Mark=Hello', $response->body->postData->params->name);
    }

    public function testPostArray()
    {
        $response = Unirest\Request::post('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'name[0]' => 'Mark',
            'name[1]' => 'John'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('POST', $response->body->method);
        $this->assertEquals('Mark', $response->body->postData->params->{'name[0]'});
        $this->assertEquals('John', $response->body->postData->params->{'name[1]'});
    }

    public function testPostWithDots()
    {
        $response = Unirest\Request::post('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'user.name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('POST', $response->body->method);
        $this->assertEquals('Mark', $response->body->postData->params->{'user.name'});
        $this->assertEquals('thefosk', $response->body->postData->params->nick);
    }

    public function testRawPost()
    {
        $response = Unirest\Request::post('http://mockbin.com/request', array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ), json_encode(array(
            'author' => 'Sam Sullivan'
        )));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('POST', $response->body->method);
        $this->assertEquals('Sam Sullivan', json_decode($response->body->postData->text)->author);
    }

    public function testPostMultidimensionalArray()
    {
        $response = Unirest\Request::post('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'key' => 'value',
            'items' => array(
                'item1',
                'item2'
            )
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('POST', $response->body->method);
        $this->assertEquals('value', $response->body->postData->params->key);
        $this->assertEquals('item1', $response->body->postData->params->{'items[0]'});
        $this->assertEquals('item2', $response->body->postData->params->{'items[1]'});
    }

    // PUT
    public function testPut()
    {
        $response = Unirest\Request::put('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('PUT', $response->body->method);
        $this->assertEquals('Mark', $response->body->postData->params->name);
        $this->assertEquals('thefosk', $response->body->postData->params->nick);
    }

    // PATCH
    public function testPatch()
    {
        $response = Unirest\Request::patch('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('PATCH', $response->body->method);
        $this->assertEquals('Mark', $response->body->postData->params->name);
        $this->assertEquals('thefosk', $response->body->postData->params->nick);
    }

    // DELETE
    public function testDelete()
    {
        $response = Unirest\Request::delete('http://mockbin.com/request', array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded'
        ), array(
            'name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('DELETE', $response->body->method);
    }

    // Upload
    public function testUpload()
    {
        $response = Unirest\Request::post('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'file' => Unirest\File::add(UPLOAD_FIXTURE)
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('POST', $response->body->method);
        $this->assertEquals('Mark', $response->body->postData->params->name);
        $this->assertEquals('This is a test', $response->body->postData->params->file);
    }

    public function testUploadIfFilePartOfData()
    {
        $response = Unirest\Request::post('http://mockbin.com/request', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'files[owl.gif]' => Unirest\File::add(UPLOAD_FIXTURE)
        ));

        $this->assertEquals(200, $response->code);
        $this->assertEquals('POST', $response->body->method);
        $this->assertEquals('Mark', $response->body->postData->params->name);
        $this->assertEquals('This is a test', $response->body->postData->params->{'files[owl.gif]'});
    }
}

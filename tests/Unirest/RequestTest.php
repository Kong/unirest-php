<?php

require_once __DIR__ . '/../../vendor/autoload.php';

define('UPLOAD_FIXTURE', dirname(__DIR__) . '/fixtures/upload.txt');

use Unirest\Request as Request;

class UnirestTest extends \PHPUnit_Framework_TestCase
{

    public function testGet()
    {
        $response = Request::get('http://httpbin.org/get?name=Mark', array(
            'Accept' => 'application/json'
        ), array(
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('Mark', $args->name);
        $this->assertEquals('thefosk', $args->nick);
    }

    public function testGetMultidimensionalArray()
    {
        $response = Request::get('http://httpbin.org/get', array(
            'Accept' => 'application/json'
        ), array(
            'key' => 'value',
            'items' => array(
                'item1',
                'item2'
            )
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;

        $this->assertEquals('value', $args->key);
        $this->assertEquals('item1', $args->{'items[0]'});
        $this->assertEquals('item2', $args->{'items[1]'});
    }

    public function testGetWithDots()
    {
        $response = Request::get('http://httpbin.org/get', array(
            'Accept' => 'application/json'
        ), array(
            'user.name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('Mark', $args->{'user.name'});
        $this->assertEquals('thefosk', $args->nick);
    }

    public function testGetWithDots2()
    {
        $response = Request::get('http://httpbin.org/get', array(
            'Accept' => 'application/json'
        ), array(
            'user.name' => 'Mark Bond',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('Mark Bond', $args->{'user.name'});
        $this->assertEquals('thefosk', $args->nick);
    }

    public function testPost()
    {
        $response = Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);

        $form = $response->body->form;
        $this->assertEquals('Mark', $form->name);
        $this->assertEquals('thefosk', $form->nick);
    }

    public function testPostWithEqualSign()
    {
        $response = Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark=Hello'
        ));

        $this->assertEquals(200, $response->code);

        $form = $response->body->form;
        $this->assertEquals('Mark=Hello', $form->name);
    }

    public function testGetWithEqualSign()
    {
        $response = Request::get('http://httpbin.org/get', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark=Hello'
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('Mark=Hello', $args->name);

        $response = Request::get('http://httpbin.org/get', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark=Hello=John'
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('Mark=Hello=John', $args->name);
    }

    public function testPostArray()
    {
        $response = Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'name[0]' => 'Mark',
            'name[1]' => 'John'
        ));

        $this->assertEquals(200, $response->code);

        $form = $response->body->form;

        $this->assertEquals('Mark', $form->{'name[0]'});
        $this->assertEquals('John', $form->{'name[1]'});
    }

    public function testGetArray()
    {
        $response = Request::get('http://httpbin.org/get', array(), array(
            'name[0]' => 'Mark',
            'name[1]' => 'John'
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('Mark', $args->{'name[0]'});
        $this->assertEquals('John', $args->{'name[1]'});
    }

    public function testPostWithDots()
    {
        $response = Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'user.name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);

        $form = $response->body->form;
        $this->assertEquals('Mark', $form->{'user.name'});
        $this->assertEquals('thefosk', $form->nick);
    }

    public function testRawPost()
    {
        $response = Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ), json_encode(array(
            'author' => 'Sam Sullivan'
        )));

        $this->assertEquals(200, $response->code);

        $json = $response->body->json;

        $this->assertEquals('Sam Sullivan', $json->author);
    }

    public function testUpload()
    {
        $response = Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'file' => Request::file(UPLOAD_FIXTURE)
        ));
        $this->assertEquals(200, $response->code);

        $files = $response->body->files;
        $this->assertEquals('This is a test', $files->file);

        $form = $response->body->form;
        $this->assertEquals('Mark', $form->name);
    }

    public function testUploadIfFilePartOfData()
    {
        $response = Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'files[owl.gif]' => Request::file(UPLOAD_FIXTURE)
        ));
        $this->assertEquals(200, $response->code);

        //$files = $response->body->files;
        //$this->assertEquals('This is a test', $files->file);

        $form = $response->body->form;
        $this->assertEquals('Mark', $form->name);
    }

    public function testPostMultidimensionalArray()
    {
        $response = Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'key' => 'value',
            'items' => array(
                'item1',
                'item2'
            )
        ));

        $this->assertEquals(200, $response->code);

        $form = $response->body->form;
        $this->assertEquals('value', $form->key);
        $this->assertEquals('item1', $form->{'items[0]'});
        $this->assertEquals('item2', $form->{'items[1]'});
    }

    public function testPut()
    {
        $response = Request::put('http://httpbin.org/put', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);

        $form = $response->body->form;
        $this->assertEquals('Mark', $form->name);
        $this->assertEquals('thefosk', $form->nick);
    }

    public function testPatch()
    {
        $response = Request::patch('http://httpbin.org/patch', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);

        $form = $response->body->form;
        $this->assertEquals('Mark', $form->name);
        $this->assertEquals('thefosk', $form->nick);
    }

    public function testDelete()
    {
        $response = Request::delete('http://httpbin.org/delete', array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded'
        ), array(
            'name' => 'Mark',
            'nick' => 'thefosk'
        ));

        $this->assertEquals(200, $response->code);
        $data = $response->body->data;
        $this->assertTrue(empty($data));
    }

    /**
     * @expectedException Exception
     */
    public function testTimeoutFail()
    {
        Request::timeout(1);

        Request::get('http://httpbin.org/delay/3');

        Request::timeout(null); // Cleaning timeout for the other tests
    }

    public function testTimeoutSuccess()
    {
        Request::timeout(3);

        $response = Request::get('http://httpbin.org/delay/1');
        $this->assertEquals(200, $response->code);

        Request::timeout(null); // Cleaning timeout for the other tests
    }

    public function testDefaultHeader()
    {
        Request::defaultHeader('Hello', 'custom');
        $response = Request::get('http://httpbin.org/get');

        $this->assertEquals(200, $response->code);
        $headers    = $response->body->headers;
        $properties = get_object_vars($headers);
        $this->assertTrue(array_key_exists('Hello', $properties));
        $this->assertEquals('custom', $headers->Hello);
        $response = Request::get('http://httpbin.org/get');

        $this->assertEquals(200, $response->code);
        $headers    = $response->body->headers;
        $properties = get_object_vars($headers);
        $this->assertTrue(array_key_exists('Hello', $properties));
        $this->assertEquals('custom', $headers->Hello);
        Request::clearDefaultHeaders();
        $response = Request::get('http://httpbin.org/get');

        $this->assertEquals(200, $response->code);
        $headers    = $response->body->headers;
        $properties = get_object_vars($headers);
        $this->assertFalse(array_key_exists('Hello', $properties));
    }

    public function testGzip()
    {
        $response = Request::get('http://httpbin.org/gzip');
        $args     = $response->body;
        $this->assertEquals(true, $args->gzipped);
    }

    public function testBasicAuthentication()
    {
        $response = Request::get('http://httpbin.org/get', null, array(), 'user', 'password');
        $headers  = $response->body->headers;
        $this->assertEquals('Basic dXNlcjpwYXNzd29yZA==', $headers->Authorization);
    }

    public function testCustomHeaders()
    {
        $response = Request::get('http://httpbin.org/get', array(
            'user-agent' => 'ciao',
        ));

        $this->assertEquals(200, $response->code);

        $headers    = $response->body->headers;
        $this->assertEquals('ciao', $headers->{'User-Agent'});
    }

    public function testHttpBuildQueryWhenCurlFile()
    {
        $file = Request::file(UPLOAD_FIXTURE);
        $body = array(
            'to' => 'mail@mailinator.com',
            'from' => 'mail@mailinator.com',
            'file' => $file
        );

        Request::buildHTTPCurlQuery($body, $postBody);
        $this->assertEquals($postBody['file'], $file);
    }
}

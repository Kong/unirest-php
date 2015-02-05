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

        Unirest\Request::get('http://httpbin.org/delay/3');

        Unirest\Request::timeout(null); // Cleaning timeout for the other tests
    }

    public function testTimeoutSuccess()
    {
        Unirest\Request::timeout(3);

        $response = Unirest\Request::get('http://httpbin.org/delay/1');
        $this->assertEquals(200, $response->code);

        Unirest\Request::timeout(null); // Cleaning timeout for the other tests
    }

    public function testDefaultHeader()
    {
        Unirest\Request::defaultHeader('Hello', 'custom');
        $response = Unirest\Request::get('http://httpbin.org/get');

        $this->assertEquals(200, $response->code);
        $headers    = $response->body->headers;
        $properties = get_object_vars($headers);
        $this->assertTrue(array_key_exists('Hello', $properties));
        $this->assertEquals('custom', $headers->Hello);
        $response = Unirest\Request::get('http://httpbin.org/get');

        $this->assertEquals(200, $response->code);
        $headers    = $response->body->headers;
        $properties = get_object_vars($headers);
        $this->assertTrue(array_key_exists('Hello', $properties));
        $this->assertEquals('custom', $headers->Hello);
        Unirest\Request::clearDefaultHeaders();
        $response = Unirest\Request::get('http://httpbin.org/get');

        $this->assertEquals(200, $response->code);
        $headers    = $response->body->headers;
        $properties = get_object_vars($headers);
        $this->assertFalse(array_key_exists('Hello', $properties));
    }

    public function testGzip()
    {
        $response = Unirest\Request::get('http://httpbin.org/gzip');
        $args     = $response->body;
        $this->assertEquals(true, $args->gzipped);
    }

    public function testBasicAuthenticationDeprecated()
    {
        $response = Unirest\Request::get('http://httpbin.org/get', array(), array(), 'user', 'password');
        $headers  = $response->body->headers;
        $this->assertEquals('Basic dXNlcjpwYXNzd29yZA==', $headers->Authorization);
    }

    public function testBasicAuthentication()
    {
        Unirest\Request::auth('user', 'password');
        $response = Unirest\Request::get('http://httpbin.org/get');

        $this->assertEquals('Basic dXNlcjpwYXNzd29yZA==', $response->body->headers->Authorization);
    }

    public function testCustomHeaders()
    {
        $response = Unirest\Request::get('http://httpbin.org/get', array(
            'user-agent' => 'ciao',
        ));

        $this->assertEquals(200, $response->code);

        $headers    = $response->body->headers;
        $this->assertEquals('ciao', $headers->{'User-Agent'});
    }

    // GET
    public function testGet()
    {
        $response = Unirest\Request::get('http://httpbin.org/get?name=Mark', array(
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
        $response = Unirest\Request::get('http://httpbin.org/get', array(
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
        $response = Unirest\Request::get('http://httpbin.org/get', array(
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

    public function testGetWithDotsAlt()
    {
        $response = Unirest\Request::get('http://httpbin.org/get', array(
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

    public function testGetWithEqualSign()
    {
        $response = Unirest\Request::get('http://httpbin.org/get', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark=Hello'
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('Mark=Hello', $args->name);
    }

    public function testGetWithEqualSignAlt()
    {
        $response = Unirest\Request::get('http://httpbin.org/get', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark=Hello=John'
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('Mark=Hello=John', $args->name);
    }

    public function testGetWithComplexQuery()
    {
        $response = Unirest\Request::get('http://httpbin.org/get?query=[{"type":"/music/album","name":null,"artist":{"id":"/en/bob_dylan"},"limit":3}]&cursor');

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('', $args->cursor);
        $this->assertEquals('[{"type":"/music/album","name":null,"artist":{"id":"/en/bob_dylan"},"limit":3}]', $args->query);
    }

    public function testGetArray()
    {
        $response = Unirest\Request::get('http://httpbin.org/get', array(), array(
            'name[0]' => 'Mark',
            'name[1]' => 'John'
        ));

        $this->assertEquals(200, $response->code);

        $args = $response->body->args;
        $this->assertEquals('Mark', $args->{'name[0]'});
        $this->assertEquals('John', $args->{'name[1]'});
    }

    // POST
    public function testPost()
    {
        $response = Unirest\Request::post('http://httpbin.org/post', array(
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
        $response = Unirest\Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark=Hello'
        ));

        $this->assertEquals(200, $response->code);

        $form = $response->body->form;
        $this->assertEquals('Mark=Hello', $form->name);
    }

    public function testPostArray()
    {
        $response = Unirest\Request::post('http://httpbin.org/post', array(
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

    public function testPostWithDots()
    {
        $response = Unirest\Request::post('http://httpbin.org/post', array(
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
        $response = Unirest\Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ), json_encode(array(
            'author' => 'Sam Sullivan'
        )));

        $this->assertEquals(200, $response->code);

        $json = $response->body->json;

        $this->assertEquals('Sam Sullivan', $json->author);
    }

    public function testPostMultidimensionalArray()
    {
        $response = Unirest\Request::post('http://httpbin.org/post', array(
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

    // PUT
    public function testPut()
    {
        $response = Unirest\Request::put('http://httpbin.org/put', array(
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

    // PATCH
    public function testPatch()
    {
        $response = Unirest\Request::patch('http://httpbin.org/patch', array(
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

    // DELETE
    public function testDelete()
    {
        $response = Unirest\Request::delete('http://httpbin.org/delete', array(
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

    // Upload
    public function testUpload()
    {
        $response = Unirest\Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'file' => Unirest\File::add(UPLOAD_FIXTURE)
        ));
        $this->assertEquals(200, $response->code);

        $files = $response->body->files;
        $this->assertEquals('This is a test', $files->file);

        $form = $response->body->form;
        $this->assertEquals('Mark', $form->name);
    }

    public function testUploadIfFilePartOfData()
    {
        $response = Unirest\Request::post('http://httpbin.org/post', array(
            'Accept' => 'application/json'
        ), array(
            'name' => 'Mark',
            'files[owl.gif]' => Unirest\File::add(UPLOAD_FIXTURE)
        ));
        $this->assertEquals(200, $response->code);

        //$files = $response->body->files;
        //$this->assertEquals('This is a test', $files->file);

        $form = $response->body->form;
        $this->assertEquals('Mark', $form->name);
    }
}

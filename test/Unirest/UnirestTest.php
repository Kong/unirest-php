<?php

class UnirestTest extends UnitTestCase
{

    public function testGet()
    {
        $response = Unirest::get("http://httpbin.org/get?name=Mark", array(
            "Accept" => "application/json"
        ), array(
            "nick" => "thefosk"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $args = $response->body->args;
        $this->assertEqual("Mark", $args->name);
        $this->assertEqual("thefosk", $args->nick);
    }
    
    public function testGetMultidimensionalArray()
    {
        $response = Unirest::get("http://httpbin.org/get", array(
            "Accept" => "application/json"
        ), array(
            'key' => 'value',
            'items' => array(
                'item1',
                'item2'
            )
        ));
        
        $this->assertEqual(200, $response->code);
        
        $args = $response->body->args;
        
        $this->assertEqual("value", $args->key);
        $this->assertEqual("item1", $args->{"items[0]"});
        $this->assertEqual("item2", $args->{"items[1]"});
    }
    
    public function testGetWithDots()
    {
        $response = Unirest::get("http://httpbin.org/get", array(
            "Accept" => "application/json"
        ), array(
            "user.name" => "Mark",
            "nick" => "thefosk"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $args = $response->body->args;
        $this->assertEqual("Mark", $args->{"user.name"});
        $this->assertEqual("thefosk", $args->nick);
    }
    
    public function testGetWithDots2()
    {
        $response = Unirest::get("http://httpbin.org/get", array(
            "Accept" => "application/json"
        ), array(
            "user.name" => "Mark Bond",
            "nick" => "thefosk"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $args = $response->body->args;
        $this->assertEqual("Mark Bond", $args->{"user.name"});
        $this->assertEqual("thefosk", $args->nick);
    }
    
    public function testPost()
    {
        $response = Unirest::post("http://httpbin.org/post", array(
            "Accept" => "application/json"
        ), array(
            "name" => "Mark",
            "nick" => "thefosk"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $form = $response->body->form;
        $this->assertEqual("Mark", $form->name);
        $this->assertEqual("thefosk", $form->nick);
    }

    public function testPostWithEqualSign()
    {
        $response = Unirest::post("http://httpbin.org/post", array(
            "Accept" => "application/json"
        ), array(
            "name" => "Mark=Hello"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $form = $response->body->form;
        $this->assertEqual("Mark=Hello", $form->name);
    }

    public function testGetWithEqualSign()
    {
        $response = Unirest::get("http://httpbin.org/get", array(
            "Accept" => "application/json"
        ), array(
            "name" => "Mark=Hello"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $args = $response->body->args;
        $this->assertEqual("Mark=Hello", $args->name);

        $response = Unirest::get("http://httpbin.org/get", array(
            "Accept" => "application/json"
        ), array(
            "name" => "Mark=Hello=John"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $args = $response->body->args;
        $this->assertEqual("Mark=Hello=John", $args->name);
    }

    public function testPostArray()
    {
        $response = Unirest::post("http://httpbin.org/post", array(
            "Accept" => "application/json"
        ), array(
            "name[0]" => "Mark",
            "name[1]" => "John"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $form = $response->body->form;

        $this->assertEqual("Mark", $form->{"name[0]"});
        $this->assertEqual("John", $form->{"name[1]"});
    }

    public function testGetArray()
    {
        $response = Unirest::get("http://httpbin.org/get", array(), array(
            "name[0]" => "Mark",
            "name[1]" => "John"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $args = $response->body->args;
        $this->assertEqual("Mark", $args->{"name[0]"});
        $this->assertEqual("John", $args->{"name[1]"});
    }
    
    public function testPostWithDots()
    {
        $response = Unirest::post("http://httpbin.org/post", array(
            "Accept" => "application/json"
        ), array(
            "user.name" => "Mark",
            "nick" => "thefosk"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $form = $response->body->form;
        $this->assertEqual("Mark", $form->{"user.name"});
        $this->assertEqual("thefosk", $form->nick);
    }
    
    public function testRawPost()
    {
        $response = Unirest::post("http://httpbin.org/post", array(
            "Accept" => "application/json"
        ), json_encode(array(
            "author" => "Sam Sullivan"
        )));
        
        $this->assertEqual(200, $response->code);
        
        $json = $response->body->json;
        $this->assertEqual("Sam Sullivan", $json->author);
    }
    
    public function testUpload()
    {  
        $response = Unirest::post("http://httpbin.org/post", array(
            "Accept" => "application/json"
        ), array(
            "name" => "Mark",
            "file" => Unirest::file(dirname(__FILE__) . "/test_upload.txt")
        ));
        $this->assertEqual(200, $response->code);
        
        $files = $response->body->files;
        $this->assertEqual("This is a test", $files->file);
        
        $form = $response->body->form;
        $this->assertEqual("Mark", $form->name);
    }

    public function testUploadIfFilePartOfData()
    {  
        $response = Unirest::post("http://httpbin.org/post", array(
            "Accept" => "application/json"
        ), array(
            "name" => "Mark",
            "files[owl.gif]" => Unirest::file(dirname(__FILE__) . "/test_upload.txt")
        ));
        $this->assertEqual(200, $response->code);
        
        //$files = $response->body->files;
        //$this->assertEqual("This is a test", $files->file);
        
        $form = $response->body->form;
        $this->assertEqual("Mark", $form->name);
    }
   
    public function testPostMultidimensionalArray()
    {
        $response = Unirest::post("http://httpbin.org/post", array(
            "Accept" => "application/json"
        ), array(
            'key' => 'value',
            'items' => array(
                'item1',
                'item2'
            )
        ));
        
        $this->assertEqual(200, $response->code);
        
        $form = $response->body->form;
        $this->assertEqual("value", $form->key);
        $this->assertEqual("item1", $form->{"items[0]"});
        $this->assertEqual("item2", $form->{"items[1]"});
    }
    
    public function testPut()
    {
        $response = Unirest::put("http://httpbin.org/put", array(
            "Accept" => "application/json"
        ), array(
            "name" => "Mark",
            "nick" => "thefosk"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $form = $response->body->form;
        $this->assertEqual("Mark", $form->name);
        $this->assertEqual("thefosk", $form->nick);
    }
    
    public function testPatch()
    {
        $response = Unirest::patch("http://httpbin.org/patch", array(
            "Accept" => "application/json"
        ), array(
            "name" => "Mark",
            "nick" => "thefosk"
        ));
        
        $this->assertEqual(200, $response->code);
        
        $form = $response->body->form;
        $this->assertEqual("Mark", $form->name);
        $this->assertEqual("thefosk", $form->nick);
    }
    
    public function testDelete()
    {
        $response = Unirest::delete("http://httpbin.org/delete", array(
            "Accept" => "application/json",
            "Content-Type" => "application/x-www-form-urlencoded"
        ), array(
            "name" => "Mark",
            "nick" => "thefosk"
        ));
        
        $this->assertEqual(200, $response->code);
        $data = $response->body->data;
        $this->assertFalse(empty($data));
    }
    
    public function testTimeoutFail()
    {
        Unirest::timeout(1);
        
        $this->expectException();
        $response = Unirest::get("http://httpbin.org/delay/3");
        
        Unirest::timeout(null); // Cleaning timeout for the other tests
    }
    
    public function testTimeoutSuccess()
    {
        Unirest::timeout(3);
        
        $response = Unirest::get("http://httpbin.org/delay/1");
        $this->assertEqual(200, $response->code);
        
        Unirest::timeout(null); // Cleaning timeout for the other tests
    }
    
    public function testDefaultHeader()
    {
        Unirest::defaultHeader("Hello", "custom");
        $response = Unirest::get("http://httpbin.org/get");
        
        $this->assertEqual(200, $response->code);
        $headers    = $response->body->headers;
        $properties = get_object_vars($headers);
        $this->assertTrue(array_key_exists("Hello", $properties));
        $this->assertEqual("custom", $headers->Hello);
        $response = Unirest::get("http://httpbin.org/get");
        
        $this->assertEqual(200, $response->code);
        $headers    = $response->body->headers;
        $properties = get_object_vars($headers);
        $this->assertTrue(array_key_exists("Hello", $properties));
        $this->assertEqual("custom", $headers->Hello);
        Unirest::clearDefaultHeaders();
        $response = Unirest::get("http://httpbin.org/get");
        
        $this->assertEqual(200, $response->code);
        $headers    = $response->body->headers;
        $properties = get_object_vars($headers);
        $this->assertFalse(array_key_exists("Hello", $properties));
    }
    
    public function testGzip()
    {
        $response = Unirest::get("http://httpbin.org/gzip");
        $args     = $response->body;
        $this->assertEqual(true, $args->gzipped);
    }
    
    public function testBasicAuthentication()
    {
        $response = Unirest::get("http://httpbin.org/get", null, null, "user", "password");
        $headers  = $response->body->headers;
        $this->assertEqual("Basic dXNlcjpwYXNzd29yZA==", $headers->Authorization);
    }

    public function testCustomHeaders() 
    {
        $response = Unirest::get('http://httpbin.org/get', array(
            'user-agent' => 'ciao',
        ));

        $this->assertEqual(200, $response->code);

        $headers    = $response->body->headers;
        $this->assertEqual("ciao", $headers->{'User-Agent'});
    }

    public function testHttpBuildQueryWhenCurlFile()
    {
      $file = Unirest::file(dirname(__FILE__) . "/test_upload.txt");
      $body = array(
        "to" => "mail@mailinator.com",
        "from" => "mail@mailinator.com",
        "file" => $file 
      );
      Unirest::http_build_query_for_curl($body, $postBody);
      $this->assertEqual($postBody['file'], $file);
    }
}

<?php

class UnirestTest extends UnitTestCase
{
  public function testGet()
  {
  	$response = Unirest::get("http://httpbin.org/get?name=Mark", array( "Accept" => "application/json" ),
													  array(
													    "nick" => "thefosk"
													  ));

  	$this->assertEqual(200, $response->code);

  	$args = $response->body->args;
  	$this->assertEqual("Mark", $args->name);
  	$this->assertEqual("thefosk", $args->nick);
  }

  public function testPost()
  {
  	$response = Unirest::post("http://httpbin.org/post", array( "Accept" => "application/json" ),
													  array(
													  	"name" => "Mark",
													    "nick" => "thefosk"
													  ));

  	$this->assertEqual(200, $response->code);

  	$form = $response->body->form;
  	$this->assertEqual("Mark", $form->name);
  	$this->assertEqual("thefosk", $form->nick);
  }

  public function testPut()
  {
  	$response = Unirest::put("http://httpbin.org/put", array( "Accept" => "application/json" ),
													  array(
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
  	$response = Unirest::patch("http://httpbin.org/patch", array( "Accept" => "application/json" ),
													  array(
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
  	$response = Unirest::delete("http://httpbin.org/delete", array( "Accept" => "application/json", "Content-Type" => "application/x-www-form-urlencoded" ),
													  array(
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
  }

  public function testTimeoutSuccess()
  {
  	Unirest::timeout(3);

  	$response = Unirest::get("http://httpbin.org/delay/1");
  	$this->assertEqual(200, $response->code);
  }

   public function testDefaultHeader()
  {
  	Unirest::defaultHeader("Hello", "custom");
  	$response = Unirest::get("http://httpbin.org/get");

		$this->assertEqual(200, $response->code);
  	$headers = $response->body->headers;
  	$properties = get_object_vars($headers);
  	$this->assertTrue(array_key_exists("Hello", $properties));
		$this->assertEqual("custom", $headers->Hello);

		$response = Unirest::get("http://httpbin.org/get");

		$this->assertEqual(200, $response->code);
  	$headers = $response->body->headers;
  	$properties = get_object_vars($headers);
  	$this->assertTrue(array_key_exists("Hello", $properties));
		$this->assertEqual("custom", $headers->Hello);

		Unirest::clearDefaultHeaders();
		$response = Unirest::get("http://httpbin.org/get");

		$this->assertEqual(200, $response->code);
  	$headers = $response->body->headers;
  	$properties = get_object_vars($headers);
  	$this->assertFalse(array_key_exists("Hello", $properties));
  }

  public function testGzip()
  {
  	$response = Unirest::get("http://httpbin.org/gzip");
  	$args = $response->body;
  	$this->assertEqual(true, $args->gzipped);
  }

  public function testBasicAuthentication()
  {
  	$response = Unirest::get("http://httpbin.org/get", null, null, "user", "password");
  	$headers = $response->body->headers;
  	$this->assertEqual("Basic dXNlcjpwYXNzd29yZA==", $headers->Authorization);
  }

}
<?php

class UnirestResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testJSONAssociativeArrays()
    {
        $opts = Unirest\Request::jsonOpts(true);
        $response = new Unirest\Response(200, '{"a":1,"b":2,"c":3,"d":4,"e":5}', '', $opts);

        $this->assertEquals($response->body['a'], 1);
    }

    public function testJSONAObjects()
    {
        $opts = Unirest\Request::jsonOpts(false);
        $response = new Unirest\Response(200, '{"a":1,"b":2,"c":3,"d":4,"e":5}', '', $opts);

        $this->assertEquals($response->body->a, 1);
    }

    public function testJSONOpts()
    {
        $opts = Unirest\Request::jsonOpts(false, 512, JSON_NUMERIC_CHECK);
        $response = new Unirest\Response(200, '{"number": 1234567890}', '', $opts);

        $this->assertSame($response->body->number, 1234567890);
    }
}

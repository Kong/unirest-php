<?php

namespace Unirest\Response\Test;

use Unirest\Request as Request;
use Unirest\Response as Response;

require __DIR__ . '/../../src/Unirest.php';

class UnirestResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testJSONAssociativeArrays()
    {
        $opts = Request::jsonOpts(true);
        $response = new Response(200, '{"a":1,"b":2,"c":3,"d":4,"e":5}', '', $opts);

        $this->assertEquals($response->body['a'], 1);
    }

    public function testJSONAObjects()
    {
        $opts = Request::jsonOpts(false);
        $response = new Response(200, '{"a":1,"b":2,"c":3,"d":4,"e":5}', '', $opts);

        $this->assertEquals($response->body->a, 1);
    }

    public function testJSONOpts()
    {
        $opts = Request::jsonOpts(false, 512, JSON_NUMERIC_CHECK);
        $response = new Response(200, '{"number": 1234567890}', '', $opts);

        $this->assertSame($response->body->number, 1234567890);
    }
}

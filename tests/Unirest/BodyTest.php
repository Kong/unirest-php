<?php

namespace Unirest\Request\Body\Test;

use Unirest\Request as Request;
use Unirest\Request\Body as Body;

require_once __DIR__ . '/../../src/Unirest.php';

class BodyTest extends \PHPUnit_Framework_TestCase
{
    public function testCURLFile()
    {
        $fixture = __DIR__ . '/fixtures/upload.txt';

        $file = Body::File($fixture);

        if (PHP_MAJOR_VERSION === 5 && PHP_MINOR_VERSION === 4) {
            $this->assertEquals($file, sprintf('@%s;filename=%s;type=', $fixture, basename($fixture)));
        } else {
            $this->assertTrue($file instanceof \CURLFile);
        }
    }

    public function testHttpBuildQueryWithCurlFile()
    {
        $fixture = __DIR__ . '/fixtures/upload.txt';

        $file = Body::File($fixture);
        $body = array(
            'to' => 'mail@mailinator.com',
            'from' => 'mail@mailinator.com',
            'file' => $file
        );

        $result = Request::buildHTTPCurlQuery($body);
        $this->assertEquals($result['file'], $file);
    }

    public function testJson()
    {
        $body = Body::Json(array('foo', 'bar'));

        $this->assertEquals($body, '["foo","bar"]');
    }

    public function testForm()
    {
        $body = Body::Form(array('foo' => 'bar', 'bar' => 'baz'));

        $this->assertEquals($body, 'foo=bar&bar=baz');

        // try again with a string
        $body = Body::Form($body);

        $this->assertEquals($body, 'foo=bar&bar=baz');
    }

    public function testMultipart()
    {
        $arr = array('foo' => 'bar', 'bar' => 'baz');

        $body = Body::Multipart((object) $arr);

        $this->assertEquals($body, $arr);

        $body = Body::Multipart('flat');

        $this->assertEquals($body, array('flat'));
    }

    public function testMultipartFiles()
    {
        $fixture = __DIR__ . '/fixtures/upload.txt';

        $data = array('foo' => 'bar', 'bar' => 'baz');
        $files = array('test' => $fixture);

        $body = Body::Multipart($data, $files);

        // echo $body;

        $this->assertEquals($body, array(
            'foo' => 'bar',
            'bar' => 'baz',
            'test' => Body::File($fixture)
        ));
    }
}

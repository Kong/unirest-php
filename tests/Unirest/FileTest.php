<?php

use Unirest\File as File;

class UnirestFileTest extends \PHPUnit_Framework_TestCase
{
    public function testCURLFile()
    {
        $file = File::add(UPLOAD_FIXTURE);
        $this->assertTrue($file instanceof \CURLFile);
    }
}

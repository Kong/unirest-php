<?php

use Unirest\File as File;

class UnirestFileTest extends \PHPUnit_Framework_TestCase
{
    public function testCURLFile()
    {
        $file = File::add(UPLOAD_FIXTURE);

        if (PHP_MAJOR_VERSION === 5 && PHP_MINOR_VERSION === 4) {
            $this->assertEquals($file, sprintf('@%s;filename=%s;type=', UPLOAD_FIXTURE, basename(UPLOAD_FIXTURE)));
        } else {
            $this->assertTrue($file instanceof \CURLFile);
        }
    }
}

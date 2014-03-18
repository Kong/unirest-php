<?php

echo "Running the Unirest-PHP bindings test suite.\n" . "If you're trying to use the Unirest-PHP bindings you'll probably want " . "to require('lib/Unirest.php'); instead of this file\n";

$ok = @include_once(dirname(__FILE__) . '/simpletest/autorun.php');
if (!$ok) {
    $ok = @include_once(dirname(__FILE__) . '/../vendor/vierbergenlars/simpletest/autorun.php');
}
if (!$ok) {
    echo "MISSING DEPENDENCY: The Unirest-PHP test cases depend on SimpleTest. " . "Download it at <http://www.simpletest.org/>, and either install it " . "in your PHP include_path or put it in the test/ directory.\n";
    exit(1);
}

// Throw an exception on any error
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}

set_error_handler('exception_error_handler');
error_reporting(E_ALL | E_STRICT);

require_once(dirname(__FILE__) . '/../lib/Unirest.php');

require_once(dirname(__FILE__) . '/Unirest/UnirestTest.php');

?>
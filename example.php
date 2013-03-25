<?php

require_once './lib/Unicorn.php';

$response = Unicorn::post("http://httpbin.org/post", array( "Accept" => "application/json" ),
  array(
    "parameter" => 23,
    "foo" => "bar"
  )
);

echo '<pre>';
print_r($response->body);
echo '</pre>';
<?php
require_once ('../lib/Unirest.php');

$url      = "https://www.google.es/";
$filename = getcwd().DIRECTORY_SEPARATOR.'google_es_cert_with_chain.pem';
//var_dump(file_exists($filename));
Unirest::sslPemFile($filename);
$response = Unirest::GET($url);
//print_r($response);
echo $response->raw_body;

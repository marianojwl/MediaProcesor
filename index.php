<?php
require_once 'config.php';
use marianojwl\App;


// Set the Content-Type header to indicate JSON data
header('Content-Type: application/json');
echo " hola" . PHP_EOL;



$app = new App();

$app->requestQueueFillUp();
$app->requestQueueProcessAll();

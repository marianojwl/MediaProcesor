<?php
require_once 'config.php';
use marianojwl\MediaProcessor\MediaProcessor;
use marianojwl\MediaProcessor\Request;

// Set the Content-Type header to indicate JSON data
header('Content-Type: application/json');
echo " hola" . PHP_EOL;



$mp = new MediaProcessor();

$mp->addRequest(new Request());
/*
$mp->requestQueueFillUp();
$mp->requestQueueProcessAll();
*/
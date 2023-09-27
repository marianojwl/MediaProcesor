<?php
spl_autoload_register(function ($c) {
    $f = str_repeat("../", substr_count($_SERVER["SCRIPT_NAME"], '/')-1) . 'lib/' .  $c . '.php';
    if (file_exists($f))
        require_once $f;
});
$_ENV['RESOURCE_PATH'] = 'resources/';
$_ENV['ORIGINALS_PATH'] = 'resources/originals/';
$_ENV['PROCESSED_PATH'] = 'resources/processed/';
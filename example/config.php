<?php
spl_autoload_register(function ($c) { $f = '../' . explode("\\",$c)[2] . '.php'; if (file_exists($f)) require_once $f; });

$_ENV['RESOURCE_PATH'] = 'resources/';

<?php
spl_autoload_register(function ($c) { $f = 'src/' .  str_replace('\\', '/', $c)  . '.php'; if (file_exists($f)) require_once $f; });

$_ENV['RESOURCE_PATH'] = 'resources/';

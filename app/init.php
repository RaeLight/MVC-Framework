<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
//ob_start();
date_default_timezone_set('Europe/Istanbul');

$config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

function loadClasses($className) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['classes', strtolower($className).'.php']);
}

spl_autoload_register('loadClasses');

foreach(glob(__DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, ['helpers', '*.php'])) as $helperFile){
    require_once $helperFile;
}

$Blade = new Blade();

require_once __DIR__ . DIRECTORY_SEPARATOR . 'blades.php';

$Route = new Route();

require_once __DIR__ . DIRECTORY_SEPARATOR . 'routes.php';

$Route->init();
<?php

define('PATH', realpath('.'));
define('SUBFOLDER_NAME', dirname($_SERVER['SCRIPT_NAME']));
define('URL', 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '').'://'.$_SERVER['SERVER_NAME'].(SUBFOLDER_NAME == '/' ? null : SUBFOLDER_NAME));

return [
    'db' => [
        'name' => 'db1',
        'host' => 'localhost',
        'user' => 'root',
        'pass' => 'root'
    ]
];
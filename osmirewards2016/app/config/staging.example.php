<?php

use Monolog\Logger;

return array(
    "displayErrorDetails" => true,

    "monolog_level" => Logger::DEBUG,

    "twig_debug" => true,

    "routerCacheFile" => false,

    "db" => [
        'connection' => 'mysql:host=localhost;dbname=osmirewards2016',
        'username' => 'foo',
        'password' => 'foo',
    ],
);

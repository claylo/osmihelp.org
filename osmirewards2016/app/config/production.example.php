<?php

use Monolog\Logger;

return array(
    "displayErrorDetails" => false,

    "monolog_level" => Logger::NOTICE,

    "twig_debug" => false,

    "routerCacheFile" => false,

    "db" => [
        'connection' => 'mysql:host=localhost;dbname=osmirewards2016',
        'username' => 'foo',
        'password' => 'foo',
    ],
);

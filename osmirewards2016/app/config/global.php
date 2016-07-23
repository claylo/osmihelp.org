<?php

use Monolog\Logger;

$dotenv = new Dotenv\Dotenv(dirname(__DIR__) . '/../../');
$dotenv->load();

return array(
    "displayErrorDetails" => false,
    "routerCacheFile" => false,

    "csrf_secret" => "erfiojweoigrjow[4ghw[g490h[40gjp[weog[0w9woeifjw",

    "monolog_level" => Logger::ERROR,

    "pricing_url" => 'https://console.graphstory.com/api/pricing',

    "twig_templates_path" => "../../templates",
    "twig_templates_cache_path" => "../../cache",
    "twig_debug" => false,

    "s3_assets_base_url" => 'http://gs-public-assets.s3.amazonaws.com',

    "db" => [
        'connection' => getenv('PDO_DB_CONNECTION'),
        'username' => getenv('PDO_DB_USERNAME'),
        'password' => getenv('PDO_DB_PASSWORD'),
    ],
);

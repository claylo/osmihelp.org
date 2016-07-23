<?php

use Monolog\Logger;

return array(
    "displayErrorDetails" => false,
    "routerCacheFile" => false,

    "csrf_secret" => "erfiojweoigrjow[4ghw[g490h[40gjp[weog[0w9woeifjw",

    "monolog_level" => Logger::ERROR,

    "pricing_url" => 'https://console.graphstory.com/api/pricing',

    "twig_templates_path" => "../../templates",
    "twig_templates_cache_path" => "../../cache",
    "twig_debug" => false,

    "intercom_api_id" => 'jpxvc7v8',
    "intercom_api_key" => 'd5505779115583f0c72f8ff7dfd47c333f6f32dd',

    "s3_assets_base_url" => 'http://gs-public-assets.s3.amazonaws.com',

    "db" => [
        'connection' => 'mysql:host=localhost;dbname=osmirewards2016',
        'username' => 'osmirewards',
        'password' => 'osmirewards',
    ],
);

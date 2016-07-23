<?php

require dirname(__FILE__) . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use SparkPost\SparkPost;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$dotenv = new Dotenv\Dotenv(dirname(__DIR__));
$dotenv->load();

$httpAdapter = new GuzzleAdapter(new Client());
$sparky = new SparkPost($httpAdapter, [
    'key' => getenv('SPARKPOST_API_KEY'),
    'async' => false,
]);

$htmlTpl = file_get_contents(dirname(__DIR__) . "/templates/email/rewards-sizing-form-2016.tpl");

$results = $sparky->transmissions->post([
    'content' => [
        'from' => [
            'name' => 'OSMIHelp',
            'email' => 'info@osmihelp.org',
        ],
        'subject' => 'OSMI Shirts and Hoodies: We need your size info!',
        'html' => $htmlTpl,
    ],
    'substitution_data' => ['name' => 'Ed', 'email' => 'coj@funkatron.com'],
    'recipients' => [
        [
            'address' => [
                'name' => 'Ed Finkler',
                'email' => 'coj@funkatron.com',
            ],
        ],
    ],
    'cc' => null,
    'bcc' => null,
]);

var_dump($results);
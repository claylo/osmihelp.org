<?php

require dirname(__FILE__) . '/../vendor/autoload.php';

use Aura\Sql\ExtendedPdo;
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

$pdo = new ExtendedPdo(
    getenv('PDO_DB_CONNECTION'),
    getenv('PDO_DB_USERNAME'),
    getenv('PDO_DB_PASSWORD')
);

$stm = "SELECT * FROM `indiegogo` WHERE `Perk ID` = 3613098 OR `Perk ID` = 3633662";
$sth = $pdo->query($stm);
$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
if (!$rs) {
    throw new Exception('No results for matching perk customers');
}

foreach ($rs as $row) {
    echo "Sending to {$row['Email']}...\n";
    $results = $sparky->transmissions->post([
        'content' => [
            'from' => [
                'name' => 'OSMIHelp',
                'email' => 'info@osmihelp.org',
            ],
            'subject' => 'OSMI Shirts and Hoodies: We need your size info!',
            'html' => $htmlTpl,
        ],
        'substitution_data' => ['name' => $row['Name'], 'email' => $row['Email']],
        'recipients' => [
            [
                'address' => [
                    'name' => $row['Name'],
                    'email' => $row['Email'],
                ],
            ],
        ],
        'cc' => null,
        'bcc' => null,
    ]);
}

echo "done.\n";

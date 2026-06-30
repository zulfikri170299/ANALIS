<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$api = new \App\Services\CryptoApiService();
$klines = $api->fetchKlines('SOLUSDT', '1d', 10);
$chartData = [];
foreach ($klines as $k) {
    $chartData[] = [
        'time' => gmdate('Y-m-d', (int)($k[0] / 1000)),
        'open' => (float)$k[1],
        'high' => (float)$k[2],
        'low' => (float)$k[3],
        'close' => (float)$k[4]
    ];
}
echo json_encode($chartData, JSON_PRETTY_PRINT);

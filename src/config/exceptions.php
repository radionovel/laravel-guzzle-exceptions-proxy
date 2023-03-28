<?php
declare(strict_types=1);

return [
    'proxy' => [
        \GuzzleHttp\Exception\ClientException::class,
        \GuzzleHttp\Exception\ServerException::class,
        \GuzzleHttp\Exception\ConnectException::class,
    ],

    'dont_report' => [
        \GuzzleHttp\Exception\ClientException::class,
        \GuzzleHttp\Exception\ServerException::class,
        \GuzzleHttp\Exception\ConnectException::class,
    ],

    'wrapper' => []
];

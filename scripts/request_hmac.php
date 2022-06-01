<?php

declare(strict_types=1);

use Novuso\Common\Adapter\Auth\Hmac\HmacRequestService;

$rootDirectory = dirname(__DIR__);

// $paths = [
//     'bin'     => $rootDirectory.'/bin',
//     'build'   => $rootDirectory.'/etc/build',
//     'cache'   => $rootDirectory.'/var/cache',
//     'etc'     => $rootDirectory.'/etc',
//     'reports' => $rootDirectory.'/var/reports',
//     'scripts' => $rootDirectory.'/scripts',
//     'src'     => $rootDirectory.'/src',
//     'tests'   => $rootDirectory.'/tests',
//     'var'     => $rootDirectory.'/var',
//     'vendor'  => $rootDirectory.'/vendor'
// ];

require $rootDirectory.'/vendor/autoload.php';

// $url = 'http://httpbin.org/get';
$url = 'http://172.17.0.1:8123/v1/admin/example';
$request = new \GuzzleHttp\Psr7\Request('GET', $url);
$public = "PubKey";
$private = bin2hex("Shhhhhhhhhh!");
$requestService = new HmacRequestService($public, $private);
$signedRequest = $requestService->signRequest($request);

$client = new \GuzzleHttp\Client();
$guzzleClient = new \Novuso\Common\Adapter\HttpClient\Guzzle\GuzzleClient($client);

$response = $guzzleClient->send($signedRequest);
echo $response->getStatusCode() . "\n" . $response->getBody() . "\n";
echo json_encode($signedRequest->getHeaders()) . "\n";


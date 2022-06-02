<?php

declare(strict_types=1);

$rootDirectory = dirname(__DIR__);
require $rootDirectory.'/vendor/autoload.php';

use Novuso\Common\Adapter\Auth\Hmac\HmacRequestService;

// $url = 'http://httpbin.org/get';
$url = 'http://172.17.0.1:8123/v1/admin/test_get';
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


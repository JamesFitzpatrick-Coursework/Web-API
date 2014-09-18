<?php
define ("DEFAULT_FORMAT", "json");

require_once 'core/Utils.php';

require_all('core');
require_all('data');
require_all('endpoints');
require_all('format');

$endpoints = array();
$endpoints[""] = new ServerEndpoint();
$endpoints["handshake"] = new HandshakeEndpoint();

$formats = array();
$formats["json"] = new JsonResponseFormat(false);
$formats["json/pretty"] = new JsonResponseFormat(true);
$formats["xml"] = new XMLResponseFormat();

// Request from the same server don't have a HTTP_ORIGIN
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

$responseFormat = DEFAULT_FORMAT;

if (array_key_exists('HTTP_X_RESPONSE_FORMAT', $_SERVER)) {
	$responseFormat = $_SERVER['HTTP_X_RESPONSE_FORMAT'];
}

try {
	$request = strtolower($_REQUEST['request']);
	
	if (strlen($request) != 0 && endsWith("/", $request)) {
		$request = substr($request, 0, strlen($request) - 1);
	}
	
	if (!array_key_exists($request, $endpoints)) {
		$code = 404;
		$payload = array("error" => "File Not Found", "requested" => $request);
	} else {
		$endpoint = $endpoints[$request];
		
		$body = @file_get_contents('php://input');
		
		$payload = $endpoint->handle($body);
		$code = 200;
	}
} catch (Exception $ex) {
	$code = 500;
	$payload = array("error" => $ex->getMessage());
}

if (!array_key_exists($responseFormat, $formats)) {
	$code = 400;
	$payload = array("error" => "Response type not found", "requested" => $responseFormat);
	$responseFormat = DEFAULT_FORMAT;
}
					
$format = $formats[$responseFormat];


$response = array("success" => $code == 200,
					"status" => $code,
					"payload" => $payload);

header("Content-Type: " . $format->getContentType());
$format->render($response);
					
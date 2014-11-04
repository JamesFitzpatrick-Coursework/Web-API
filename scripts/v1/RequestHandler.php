<?php
define ("DEFAULT_FORMAT", "json");
define ("IN_BACKEND", True);

set_include_path(get_include_path() . PATH_SEPARATOR . 'libs/phpseclib');

require_once 'core/Utils.php';

require_all('core');
require_all('data');
require_all('database');
require_all('endpoints');
require_all('format');
require_all('exception');

require_once 'Routes.php';

// Setup response formats
$formats = array();
$formats["json"] = new JsonResponseFormat(false);
$formats["json/pretty"] = new JsonResponseFormat(true);
$formats["xml"] = new XMLResponseFormat();

// Setup config
Config::loadConfig();
Database::load();

// Request from the same server don't have a HTTP_ORIGIN
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

// Get response format if provided
$responseFormat = DEFAULT_FORMAT;

if (array_key_exists('HTTP_X_RESPONSE_FORMAT', $_SERVER)) {
	$responseFormat = $_SERVER['HTTP_X_RESPONSE_FORMAT'];
}

try {
	// get the requested endpoint
	$request = strtolower($_REQUEST['request']);
	
	// Sanitize the request
	if (strlen($request) != 0 && endsWith("/", $request)) {
		$request = substr($request, 0, strlen($request) - 1);
	}
	
	// Check to see if endpoint exists
	global $endpoints;
	if (!array_key_exists($request, $endpoints)) {
		throw new MethodNotFoundException($request);
	} else {
		$endpoint = $endpoints[$request];
		
		$body = @file_get_contents('php://input');
		
		// Handle request
		$payload = $endpoint->handle($body);
		$code = HTTP_OK;
	}
} catch (MethodNotFound $ex) {
	$code = HTTP_NOT_FOUND;
	$payload = array("error" => "Method Not Found", "requested" => $ex->getRequest());
} catch (EndpointExecutionException $ex) {
	$code = HTTP_BAD_REQUEST;
	$payload = array("error" => $ex->getMessage());
	
	foreach ($ex->getData() as $key => $value) {
		$payload[$key] = $value;
	}
} catch (Exception $ex) {
	$code = HTTP_INTERNAL_ERROR;
	$payload = array("error" => $ex->getMessage());
}

// Check to see if response format is valid
if (!array_key_exists($responseFormat, $formats)) {
	$code = HTTP_BAD_REQUEST;
	$payload = array("error" => "Response type not found", "requested" => $responseFormat);
	$responseFormat = DEFAULT_FORMAT;
}
					
$format = $formats[$responseFormat];

// Create the response
$response = array("success" => $code == HTTP_OK,
					"status" => $code,
					"payload" => $payload);

// Display the response to the client
http_response_code($code);
header("Content-Type: " . $format->getContentType());
$format->render($response);
					
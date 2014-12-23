<?php
define ("DEFAULT_FORMAT", "json");
define ("IN_BACKEND", true);
define ("DEBUG", true);

error_reporting(DEBUG ? E_ALL : E_NONE);
set_include_path(get_include_path() . PATH_SEPARATOR . 'libs/phpseclib');

require_once 'core/Utils.php';
require_all(array('core', 'data', 'database', 'endpoints', 'format', 'exception', 'secret'));
require_once 'Routes.php';

// Setup response formats
$formats = array();
$formats["json"] = new JsonResponseFormat(false);
$formats["json/pretty"] = new JsonResponseFormat(true);
$formats["xml"] = new XMLResponseFormat();

// Setup config
Config::loadConfig();
Database::init();

// Request from the same server don't have a HTTP_ORIGIN
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

// Get response format if provided
$responseFormat = DEFAULT_FORMAT;

if (array_key_exists(HEADER_RESPONSE_FORMAT, $_SERVER)) {
    $responseFormat = $_SERVER[HEADER_RESPONSE_FORMAT];
}

try {
    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        throw new MethodNotAllowedException($_SERVER['REQUEST_METHOD']);
    }

    // get the requested endpoint
    $request = strtolower($_REQUEST['request']);

    // Sanitize the request
    if (strlen($request) != 0 && endsWith($request, "/")) {
        $request = substr($request, 0, strlen($request) - 1);
    }

    // Check to see if endpoint exists
    global $endpoints;
    if (!array_key_exists($request, $endpoints)) {
        throw new MethodNotFoundException($request);
    } else {
        $endpoint = $endpoints[$request];

        // Get the request payload
        $body = @file_get_contents('php://input');

        // Handle request
        $payload = $endpoint->execute($body);
        $code = HTTP_OK;
        $success = true;
    }
// Error handling
} catch (EndpointExecutionException $ex) {
    $code = $ex->getErrorCode();
    $success = false;
    $payload = array(
        "cause" => "uk.co.thefishlive.meteor.exception." . get_class($ex),
        "error" => $ex->getMessage()
    );

    foreach ($ex->getData() as $key => $value) {
        $payload[$key] = $value;
    }
} catch (Exception $ex) {
    $code = HTTP_INTERNAL_ERROR;
    $success = false;
    $payload = array(
        "cause" => "uk.co.thefishlive.meteor.exception.ServerExecutionException",
        "error" => $ex->getMessage()
    );
}

// Check to see if response format is valid
if (!array_key_exists($responseFormat, $formats)) {
    $code = HTTP_BAD_REQUEST;
    $payload = array("error" => "Response type not found", "requested" => $responseFormat);
    $responseFormat = DEFAULT_FORMAT;
}

$format = $formats[$responseFormat];

// Create the response
$response = array(
    "success" => $success,
    "status" => $code,
    "payload" => $payload
);

// Display the response to the client
http_response_code($code);
header("Content-Type: " . $format->getContentType());
echo $format->render($response);
					
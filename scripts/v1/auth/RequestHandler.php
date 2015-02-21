<?php
namespace meteor;

use Exception;

use common\core\HTTP;
use common\core\Headers;
use common\core\Endpoint;
use common\core\ResponseFormat;
use common\response;
use common\exceptions\EndpointExecutionException;
use common\exceptions\MethodNotAllowedException;
use common\exceptions\MethodNotFoundException;
use meteor\core;
use meteor\database;

define ("DEFAULT_FORMAT", "json");
define ("IN_BACKEND", true);
define ("DEBUG", true);

// Class loading
require_once '../../../vendor/autoload.php';
require_once '../common/MeteorClassLoader.php';

error_reporting(DEBUG ? E_ALL : 0);

try {
    require_once 'core/Utils.php';
    require_once 'Routes.php';

    // Setup response formats
    $formats = array();
    $formats["json"] = new response\JsonResponseFormat(false);
    $formats["json/pretty"] = new response\JsonResponseFormat(true);
    $formats["xml"] = new response\XMLResponseFormat();

// Get response response if provided
$responseFormat = DEFAULT_FORMAT;

if (array_key_exists(Headers::RESPONSE_FORMAT, $_SERVER)) {
    $responseFormat = $_SERVER[Headers::RESPONSE_FORMAT];
}

error_reporting(DEBUG ? E_ALL : 0);

try {
    require_once 'core/Utils.php';
    require_once 'Routes.php';

    // Setup config
    core\Config::loadConfig();
    database\Database::init();

    // Request from the same server don't have a HTTP_ORIGIN
    if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
        $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
    }

    // Get response response if provided
    $responseFormat = DEFAULT_FORMAT;

    if (array_key_exists(Headers::RESPONSE_FORMAT, $_SERVER)) {
        $responseFormat = $_SERVER[Headers::RESPONSE_FORMAT];
    }
    $code = HTTP::INTERNAL_ERROR;
    $payload = array();

    $code = HTTP::INTERNAL_ERROR;
    $payload = array();

    // get the requested endpoint
    $request = strtolower($_REQUEST['request']);

    // Sanitize the request
    if (strlen($request) != 0 && ends_with($request, "/")) {
        $request = substr($request, 0, strlen($request) - 1);
    }

    // Check to see if endpoint exists
    global $endpoints;
    $success = false;

    /** @var Endpoint $endpoint */
    foreach ($endpoints as $pattern => $endpoint) {
        if (preg_match($pattern, $request, $matches)) {
            if (!in_array($_SERVER['REQUEST_METHOD'], $endpoint->get_acceptable_methods())) {
                throw new MethodNotAllowedException($_SERVER['REQUEST_METHOD']);
            }

            // Get the request payload
            $body = @file_get_contents('php://input');

            // Handle request
            $payload = $endpoint->execute($body, $matches);
            $code = HTTP::OK;
            $success = true;
            break;
        }
    }

    if (!$success) {
        throw new MethodNotFoundException($request);
    }
// Error handling
} catch (EndpointExecutionException $ex) {
    $code = $ex->get_error_code();
    $success = false;
    $payload = array(
        "cause" => "uk.co.thefishlive." . str_replace("\\", ".", get_class($ex)),
        "error" => $ex->getMessage()
    );

    foreach ($ex->get_data() as $key => $value) {
        $payload[$key] = $value;
    }

    if (DEBUG) {
        $payload["trace"] = $ex->getTrace();
    }
} catch (Exception $ex) {
    $code = HTTP::INTERNAL_ERROR;
    $success = false;
    $payload = array(
        "cause" => "uk.co.thefishlive.meteor.exceptions.ServerExecutionException",
        "error" => $ex->getMessage()
    );

    if (DEBUG) {
        $payload["trace"] = $ex->getTrace();
    }
}

// Check to see if response response is valid
if (!array_key_exists($responseFormat, $formats)) {
    $code = HTTP::BAD_REQUEST;
    $payload = array("error" => "Response type not found", "requested" => $responseFormat);
    $responseFormat = DEFAULT_FORMAT;
}

/** @var ResponseFormat $format */
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
					
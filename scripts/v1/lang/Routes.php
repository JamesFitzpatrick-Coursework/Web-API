<?php
namespace lang;

// Setup endpoints
use lang\endpoints\ListEndpoint;

$endpoints = array();

register_endpoint("", new endpoints\ServerEndpoint());

register_endpoint("lang", new ListEndpoint());

function register_endpoint($pattern, $handler)
{
    global $endpoints;
    if (ends_with($pattern, "/")) {
        $pattern = substr($pattern, 0, strlen($pattern) - 1);
    }
    $pattern = preg_quote($pattern, "/");
    while (preg_match("/\\:([^\\/\\\\]*)/", $pattern, $matches)) {
        $pattern = preg_replace("/\\\\:([^\\/\\\\]*)/", "(?<" . substr($matches[0], 1) . ">[^\\/]+)", $pattern, 1);
    }
    $endpoints["/^" . $pattern . "$/"] = $handler;
}

function ends_with($haystack, $needle) {
    if ($haystack == "") {
        return false;
    }

    return substr($haystack, strlen($haystack) - strlen($needle), strlen($needle)) === $needle;
}

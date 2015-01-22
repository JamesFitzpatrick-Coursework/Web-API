<?php
namespace launcher;

use launcher\endpoints\java;

// Setup endpoints
$endpoints = array();

register_endpoint("", new endpoints\ServerEndpoint());

register_endpoint("java/", new java\JavaVersionsEndpoint());
register_endpoint("java/:version/:system/:architecture/download/", new java\JavaVersionDownloadEndpoint());
register_endpoint("java/:version/:system/:architecture/", new java\JavaVersionInfoEndpoint());
register_endpoint("java/:version/:system/", new java\JavaVersionInfoEndpoint());
register_endpoint("java/:version/", new java\JavaVersionInfoEndpoint());

register_endpoint("libraries/", null);
register_endpoint("libraries/:library/download/:version", null);
register_endpoint("libraries/:library/versions/", null);
register_endpoint("libraries/:library", null);

register_endpoint("versions/", null);
register_endpoint("versions/:version/download", null);
register_endpoint("versions/:version/libraries", null);
register_endpoint("versions/:version/assets", null);
register_endpoint("versions/:version", null);

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

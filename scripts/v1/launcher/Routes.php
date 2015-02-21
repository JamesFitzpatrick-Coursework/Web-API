<?php
namespace launcher;

use launcher\endpoints\assets;
use launcher\endpoints\java;
use launcher\endpoints\version;

// Setup endpoints
$endpoints = [];

register_endpoint("", new endpoints\ServerEndpoint());

register_endpoint("java/", new java\JavaVersionsEndpoint());
register_endpoint("java/:version/:system/:architecture/download/", new java\JavaVersionDownloadEndpoint());
register_endpoint("java/:version/:system/:architecture/", new java\JavaVersionInfoEndpoint());
register_endpoint("java/:version/:system/", new java\JavaVersionInfoEndpoint());
register_endpoint("java/:version/", new java\JavaVersionInfoEndpoint());

register_endpoint("versions/", new version\ListVersionsEndpoint());
register_endpoint("versions/:version/download/", new version\VersionDownloadEndpoint("jar"));
register_endpoint("versions/:version/libraries/", new version\VersionDownloadEndpoint("libraries"));
register_endpoint("versions/:version/assets/", new version\VersionDownloadEndpoint("assets"));
register_endpoint("versions/:version/", new version\VersionInfoEndpoint());

register_endpoint("assets/:asset", new assets\AssetDownloadEndpoint());

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

function ends_with($haystack, $needle)
{
    if ($haystack == "") {
        return false;
    }

    return substr($haystack, strlen($haystack) - strlen($needle), strlen($needle)) === $needle;
}

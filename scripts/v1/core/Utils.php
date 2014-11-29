<?php

if (!defined("IN_BACKEND")) {
    die();
}

function checkEnv()
{
    if (!defined("IN_BACKEND")) {
        die();
    }
}

function include_all($dir)
{
    if (!is_array($dir)) {
        $dir = array($dir);
    }

    foreach ($dir as $current) {
        foreach (glob("$current/*.php") as $filename) {
            include_once $filename;
        }
    }
}

function require_all($dir)
{
    if (!is_array($dir)) {
        $dir = array($dir);
    }

    foreach ($dir as $current) {
        foreach (glob("$current/*.php") as $filename) {
            require_once $filename;
        }
    }
}

function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function randomHex($len)
{
    return substr(md5(rand()), 0, $len);
}

?>
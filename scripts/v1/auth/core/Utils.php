<?php

if (!defined("IN_BACKEND")) {
    die("Invalid environment");
}

function check_env()
{
    if (!defined("IN_BACKEND")) {
        die("Invalid environment");
    }
}

function include_all($dir)
{
    if (!is_array($dir)) {
        $dir = array($dir);
    }

    foreach ($dir as $current) {
        foreach (glob("$current/*.php.inc") as $filename) {
            include_once $filename;
        }

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
        foreach (glob("$current/*.php.inc") as $filename) {
            require_once $filename;
        }

        foreach (glob("$current/*.php") as $filename) {
            require_once $filename;
        }
    }
}

function starts_with($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function ends_with($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function random_hex($len)
{
    return substr(md5(rand()), 0, $len);
}

function obj_to_array($obj)
{
    if (is_object($obj)) {
        $obj = get_object_vars($obj);
    }

    if (is_array($obj)) {
        return array_map(__FUNCTION__, $obj);
    } else {
        return $obj;
    }
}
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
	foreach (glob("$dir/*.php") as $filename)
	{
		include_once $filename;
	}
}

function require_all($dir)
{
	foreach (glob("$dir/*.php") as $filename)
	{
		require_once $filename;
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
?>
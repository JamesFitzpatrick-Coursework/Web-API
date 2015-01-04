<?php
namespace meteor;

new MeteorClassLoader();

class MeteorClassLoader
{
    public function __construct()
    {
        spl_autoload_register(array ($this, "load_class"), true, true);
    }

    public function load_class($class)
    {
        // we only handle loading meteor classes
        if (starts_with($class, "meteor\\")) {
            $path = strtr($class, "\\", "/");
            $path = substr($path, 7);
            $path .= ".php";

            if (file_exists($path)) {
                require_once($path);
            }
        }
    }
} 
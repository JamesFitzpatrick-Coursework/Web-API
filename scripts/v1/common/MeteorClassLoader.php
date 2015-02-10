<?php
namespace meteor;

use common\exceptions\ClassLoadingException;

new MeteorClassLoader();

class MeteorClassLoader
{
    private $mappings = array(
        "meteor" => "auth",
        "launcher" => "launcher",
        "common" => "common",
        "lang" => "lang"
    );

    public function __construct()
    {
        spl_autoload_register(array ($this, "load_class"), true, true);
    }

    public function load_class($class)
    {
        $key = substr($class, 0, strpos($class, "\\"));
        if (array_key_exists($key, $this->mappings)) {
            $path = $class;
            $path = substr($path, strlen($key) + 1);
            $path = strtr($path, "\\", DIRECTORY_SEPARATOR);
            $path = ".." . DIRECTORY_SEPARATOR . $this->mappings[$key] . DIRECTORY_SEPARATOR . $path . ".php";

            if (is_readable($path)) {
                require_once($path);
            } else {
                throw new ClassLoadingException("Could not load meteor class " . $class . " (expected path " . $path . ")");
            }
        }
    }
} 
<?php
namespace meteor;

use meteor\exceptions\ClassLoadingException;

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
            $path = $class;
            $path = substr($path, 7);                           // Gets rid of the meteor prefix
            $path = strtr($path, "\\", DIRECTORY_SEPARATOR);    // Replace all backslashes with the current file separator
            $path .= ".php";                                    // Append the file extension to the end

            if (is_readable($path)) {
                require_once($path);
            } else {
                throw new ClassLoadingException("Could not load meteor class " . $class);
            }
        }
    }
} 
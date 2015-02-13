<?php
namespace meteor\data;

class QuestionType
{
    const MULTI_CHOICE = 0;
    const ANSWER = 1;

    public static function convert_to_ordinal($type)
    {
        return self::get_constants()[$type];
    }

    public static function convert_to_string($type)
    {
        foreach (self::get_constants() as $key => $value) {
            if ($value == $type) {
                return $key;
            }
        }
    }

    private static function get_constants()
    {
        $class = new \ReflectionClass(__CLASS__);
        return $class->getConstants();
    }
} 
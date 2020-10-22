<?php

namespace Core\Utils;

class Conditions
{
    public static function jsonNotContains(string $alias, string $fieldName): string
    {
        return sprintf("(JSON_CONTAINS(%s.options , '[\"%s\"]') <> 1 OR "
            . "JSON_CONTAINS(%s.options , '[\"%s\"]') IS NULL)", $alias, $fieldName, $alias, $fieldName);
    }

    public static function jsonContains(string $alias, string $fieldName): string
    {
        return sprintf("JSON_CONTAINS(%s.options , '[\"%s\"]') = 1", $alias, $fieldName);
    }
}

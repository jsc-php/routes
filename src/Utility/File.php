<?php

namespace JscPhp\Routes\Utility;

class File
{
    public static function getClassNameFromFile(string $file): false|string
    {
        if (!is_readable($file)) {
            throw new \InvalidArgumentException('File not readable');
        }
        $contents = file_get_contents($file);
        $tokens = \PhpToken::tokenize($contents);
        $namespace_found = 0;
        $classname_found = 0;
        $namespace = '';
        $classname = '';
        foreach ($tokens as $token) {
            if ($token->id === T_NAMESPACE) {
                $namespace_found = 1;
            }
            if ($namespace_found === 1 && in_array($token->id, [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
                $namespace .= $token->text;
            } elseif ($token->text === ';') {
                $namespace_found = 2;
            }
            if ($token->id === T_CLASS) {
                $classname_found = 1;
                $namespace_found = 2;
            }
            if ($classname_found === 1 && $token->id == T_STRING) {
                $classname .= $token->text;
            } else if ($token->text === '{') {
                $classname_found = 2;
            }
            if ($namespace_found === 2 && $classname_found === 2) {
                return '\\' . $namespace . '\\' . $classname;
            }
        }
        return false;
    }
}
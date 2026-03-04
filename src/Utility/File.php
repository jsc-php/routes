<?php

namespace JscPhp\Routes\Utility;

class File {
    /**
     * Extracts the fully qualified class name (namespace and class name) from a PHP file.
     *
     * @param string $file The path to the PHP file from which to extract the class name.
     *                     The file must exist and be readable.
     *
     * @return string|null Returns the fully qualified class name if it can be determined,
     *                     or null if the file does not exist, is not readable, or does not
     *                     contain a recognizable class declaration.
     */
    public static function getClassNameFromFile(string $file): null|string {
        if (!file_exists($file) || !is_readable($file)) {
            return null;
        }
        $contents = file_get_contents($file);
        $tokens = \PhpToken::tokenize($contents);
        $namespace = null;
        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i]->id === T_NAMESPACE) {
                $namespace = '';
                while ($tokens[$i]->text !== ';' && $tokens[$i]->text !== '{') {
                    if (in_array($tokens[$i]->id, [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
                        $namespace .= $tokens[$i]->text;
                    }
                    $i++;
                }
            }
            if (in_array($tokens[$i]->id, [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM])) {
                $classname = '';
                while ($tokens[$i]->id === T_WHITESPACE) {
                    $i++;
                }
                while (!in_array($tokens[$i]->text, [';', '{'])
                        && !in_array($tokens[$i]->id, [T_EXTENDS, T_IMPLEMENTS])) {
                    if (in_array($tokens[$i]->id, [T_STRING, T_NS_SEPARATOR, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
                        $classname .= $tokens[$i]->text;
                    }
                    $i++;
                }
                return '\\' . ($namespace) ? $namespace . '\\' . $classname : $classname;
            }
        }

        return null;


    }

}
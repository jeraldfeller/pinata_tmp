<?php

namespace Vivo\UtilBundle\Util;

use Behat\Transliterator\Transliterator;

class Urlizer extends Transliterator
{
    /**
     * Urlize text preserving the directories.
     *
     * @param string $text
     * @param string $separator
     *
     * @return string
     */
    public static function urlizeWithDirectories($text, $separator = '-')
    {
        // Explode directories
        $text = explode('/', $text);

        // Urlize each directory
        $text = array_map(function ($part) use ($separator) {
            return self::urlize($part, $separator);
        }, $text);

        // Filter empty array values
        $text = array_filter($text);

        // Implode directories
        $text = implode('/', $text);

        // Pre/Post slashes from slug
        $text = trim($text, '/');

        return $text;
    }
}

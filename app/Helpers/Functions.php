<?php

namespace App\Helpers;

use Carbon\Carbon;

abstract class Functions
{

    /**
     * Creates date from input
     *
     * @param mixed $date Input
     * @param string $format Format
     * @return \DateTime
     */
    public static function createDateFromFormat($format, $date = null)
    {
        if(is_null($date)) return NULL;
        if(!is_scalar($date) && (get_class ($date) == "DateTime" || get_class ($date) == Carbon::class)){
            return $date;
        }

        $d = \DateTime::createFromFormat($format, $date);
        if(!$d){
            return NULL;
        }

        return $d;
    }


    /**
     * Copy directory and all its files and subdirectories into destination directory
     * Source: http://php.net/manual/en/function.copy.php
     *
     * @param string $src
     * @param string $dst
     */
    static function recurseDirectoryCopy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    self::recurseDirectoryCopy($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }


    /**
     * Get forbidden characters in url.
     *
     * @param string $url
     * @return array
     */
    static function getForbiddenUrlCharacters($url) {
        $matches = [];
        $result = preg_match_all("/[^.\p{L}-\d-_~\/\+]+/u", $url, $matches, PREG_PATTERN_ORDER);

        if ($result) {
            return array_unique($matches[0] ?? []);
        }

        return [];
    }
}

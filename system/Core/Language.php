<?php
/**
 * Language - simple language handler.
 *
 * @author Bartek Kuśmierczuk - contact@qsma.pl - http://qsma.pl
 * @version 3.0
 */

namespace Core;

use Core\Error;
use Helpers\Session;
use Helpers\Cookie;

/**
 * Language class to load the requested language file.
 */
class Language
{
    /**
     * list of language codes
     * @var array
     */
    public static $codes = ['cs', 'de', 'en', 'es', 'fa', 'fr', 'it', 'ja', 'nl', 'pl', 'ro', 'ru'];

    /**
     * Variable holds array with language.
     *
     * @var array
     */
    private $array;


    public static function init()
    {
        if (Session::exists('language')) {
            // The Language was already set; nothing to do.
            return;
        } else if(Cookie::exists(PREFIX .'language')) {
            $cookie = Cookie::get(PREFIX .'language');

            if (preg_match ('/[a-z]/', $cookie) && in_array($cookie, self::$codes)) {
                Session::set('language', ucfirst($cookie));
            }
        }
    }

    protected static function getCurrentLanguage($code)
    {
        if ($code != LANGUAGE_CODE) {
            // User defined Language Code; nothing to do.
        } else if (Session::exists('language')) {
            return Session::get('language');
        }

        return ucfirst($code);
    }

    /**
     * Load language function.
     *
     * @param string $name
     * @param string $code
     */
    public function load($name, $code = LANGUAGE_CODE)
    {
        $code = self::getCurrentLanguage($code);

        /** lang file */
        $file = APPDIR."Language/$code/$name.php";

        /** check if is readable */
        if (is_readable($file)) {
            /** require file */
            $this->array[$code] = include $file;
        } else {
            /** display error */
            echo Error::display("Could not load language file '$code/$name.php'");
            die;
        }
    }

    /**
     * Get element from language array by key.
     *
     * @param  string $value
     *
     * @return string
     */
    public function get($value, $code = LANGUAGE_CODE)
    {
        $code = self::getCurrentLanguage($code);

        if (!empty($this->array[$code][$value])) {
            return $this->array[$code][$value];
        } elseif(!empty($this->array[LANGUAGE_CODE][$value])) {
            return $this->array[LANGUAGE_CODE][$value];
        } else {
            return $value;
        }
    }

    /**
     * Get lang for views.
     *
     * @param  string $value this is "word" value from language file
     * @param  string $name  name of file with language
     * @param  string $code  optional, language code
     *
     * @return string
     */
    public static function show($value, $name, $code = LANGUAGE_CODE)
    {
        $code = self::getCurrentLanguage($code);

        /** lang file */
        $file = APPDIR."Language/$code/$name.php";

        /** check if is readable */
        if (is_readable($file)) {
            /** require file */
            $array = include($file);
        } else {
            /** display error */
            echo Error::display("Could not load language file '$code/$name.php'");
            die;
        }

        if (!empty($array[$value])) {
            return $array[$value];
        } else {
            return $value;
        }
    }
}

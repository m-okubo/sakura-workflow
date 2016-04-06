<?php

/*
 * Sakura Workflow version 1.0.0
 * Copyright (C) 2016 PocketSoft, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see [http://www.gnu.org/licenses/].
 */

namespace SakuraWf;

class Util
{
    /**
     * A method to get a Log instance.
     *
     * @param string $className class name
     *
     * @return object
     */
    public static function getLogger($className)
    {
        $container = Container::getInstance();
        $config = $container->get('CONFIG');

        $handler  = $config['LOG_HANDLER'];
        $name     = isset($config['LOG_NAME']) ? $config['LOG_NAME'] : null;
        $ident    = $config['LOG_IDENT'].' - '.$className;
        $conf     = null;
        $maxLevel = $config['LOG_LEVEL'];

        return Log::singleton($handler, $name, $ident, $conf, $maxLevel);
    }

    /**
     * A method to get a name corresponding to the given value.
     *
     * @param string $className A class name of the given class
     * @param int    $typeValue Type value
     *
     * @return string a corresponding name
     */
    public static function getEnumLabel($className, $typeValue)
    {
        if (!isset($typeValue))
        {
            return null;
        }

        $labels = Container::getInstance()->get('LABELS');

        $prefix = strtolower(substr($className, 0, 1));
        $prefix .= substr($className, 1);

        return $labels[$prefix.$typeValue];
    }

    /**
     * A method to get a list of enum key and corresponding label.
     *
     * @param string $className  A class name of the given class
     * @param array  $typeValues An array of enum constants
     *
     * @return string a list of enum key and corresponding label;
     */
    public static function getEnumList($className, $typeValues)
    {
        $list = array();
        foreach ($typeValues as $typeValue) {
            $list[$typeValue] = Util::getEnumLabel($className, $typeValue);
        }

        return $list;
    }

    /**
     * A method to get a language.
     *
     * @return string a language
     */
    public static function getLanguage()
    {
        $language = 'ja';

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }

        if (isset($langs) && count($langs) > 0) {
            foreach ($langs as $lang) {
                if (preg_match('/^ja/i', $lang)) {
                    break;
                } elseif (preg_match('/^en/i', $lang)) {
                    $language = 'en';
                    break;
                }
            }
        }

        return $language;
    }

    /**
     * A method to judge whether a first param starts with a second param.
     *
     * @param string $haystack a string to be judged
     * @param string $needle   search keyword
     *
     * @return boolean
     */
    public static function startsWith($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }

    /**
     * A method to generate an initial password for admin.
     *
     * @return string initial password
     */
    public static function generateInitialPassword()
    {
        return (string)mt_rand(1000, 9999);
    }

    /**
     * A method to get a browser.
     *
     * @return string browser name
     */
    public static function getBrowser()
    {
        $browser = 'unknown';

        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        if (strpos($user_agent, 'MSIE') && !strpos($user_agent, 'Opera')) {
            $browser = 'msie';
        } elseif (strpos($user_agent, 'Opera')) {
            $browser = 'opera';
        } elseif (strpos($user_agent, 'Firefox')) {
            $browser = 'firefox';
        } elseif (strpos($user_agent, 'Chrome')) {
            $browser = 'chrome';
        } elseif (strpos($user_agent, 'Safari')) {
            $browser = 'safari';
        }

        return $browser;
    }

    /**
     * A method to get a content disposition string for html header.
     *
     * @param string $file_name a file name to be downloaded
     *
     * @return string a content disposition string
     */
    public static function getContentDisposition($file_name)
    {
        $prefix = 'Content-Disposition: attachment; filename=';

        switch (Util::getBrowser()) {
            case 'msie':
                $file_name = mb_convert_encoding($file_name, 'SJIS', 'UTF-8');
                break;
            case 'firefox':
            case 'chrome':
            case 'opera':
                $prefix = 'Content-Disposition: attachment; filename*=utf-8\'ja\'';
                $file_name = rawurlencode($file_name);
                break;
            case 'safari':
                $file_name = $file_name;
                break;
            default:
                $file_name = rawurlencode($file_name);
                break;
        }

        return $prefix . $file_name;
    }

    /**
     * A method to get a value type based on form type.
     *
     * @param int $form_type form type
     *
     * @return int value type
     */
    public static function getValueType($form_type)
    {
        $value_type = null;

        switch ($form_type) {
            case FormType::TEXT:
            case FormType::PASSWORD:
            case FormType::TEXTAREA:
            case FormType::TEL:
            case FormType::URL:
            case FormType::EMAIL:
            case FormType::COLOR:
            case FormType::LITERAL:
                $value_type = ValueType::STRING;
                break;
            case FormType::NUMBER:
            case FormType::RANGE:
            case FormType::PULLDOWN:
            case FormType::RADIO:
            case FormType::CHECKBOX:
                $value_type = ValueType::NUMBER;
                break;
            case FormType::DATE:
            case FormType::TIME:
            case FormType::DATETIME:
                $value_type = ValueType::DATETIME;
                break;
            case FormType::FILE:
                $value_type = ValueType::BLOB;
                break;
        }

        return $value_type;
    }
}

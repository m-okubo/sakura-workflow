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

use Monolog\Registry;

class Model
{
    private $view;
    private $layout;
    protected $labels;
    protected $messages;
    protected $errors = array();
    protected $logger;
    protected $dbm;

    public function __construct()
    {
        $this->logger = Registry::getInstance(LOG_NAME);
        $this->logger->debug(get_class($this) . ' is created.');

        $language = $this->getLanguage();

        $labels_path = RESOURCES_ROOT . '/labels_' . $language . '.ini';
        if (file_exists($labels_path)) {
            $this->labels = parse_ini_file($labels_path);
        } else {
            $this->labels = RESOURCES_ROOT . '/labels_en.ini';
        }

        $messages_path = RESOURCES_ROOT . '/messages_' . $language . '.ini';
        if (file_exists($messages_path)) {
            $this->messages = parse_ini_file($messages_path);
        } else {
            $this->messages = RESOURCES_ROOT . '/messages_en.ini';
        }

        $this->dbm = new DatabaseManager();
    }

    public function getView()
    {
        return $this->view;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLabels()
    {
        return $this->labels;
    }

    public function getMessage($key, $replace_values = array())
    {
        assert(is_array($replace_values));

        $message = $this->messages[$key];
        foreach ($replace_values as $index => $value) {
            $place_holder = '{' . $index . '}';
            $message = str_replace($place_holder, $value, $message);
        }

        return $message;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function redirect($url)
    {
        header('Location: ' . APP_ROOT . '/' . $url);
        exit();
    }


    /**
     * A method to get a language.
     *
     * @return string a language
     */
    public function getLanguage()
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
}

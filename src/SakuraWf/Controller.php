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

/**
 * A class to handle and dispatch a request.
 *
 * @package SakuraWf
 * @author  Masayuki Okubo <m.okubo@pocket-soft.co.jp>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version Release: 1.0.0
 * @link    https://github.com/m-okubo/sakura-workflow/src/SakuraWf
 */
class Controller
{
    /**
     * A method to dispatch a request.
     *
     * @return void
     */
    public function execute()
    {
        $uri = trim(filter_input(INPUT_SERVER, 'REQUEST_URI'), '/');
        $pos = strpos($uri, '?');
        if ($pos !== false) {
            $uri = substr($uri, 0, $pos);
        }
        $dirs = explode('/', $uri);
        array_shift($dirs);
        $page = empty($dirs[0]) ? 'login' : $dirs[0];
        $action = empty($dirs[1]) ? 'index' : $dirs[1];

        $model_name  = strtoupper(substr($page, 0, 1));
        $model_name .= substr($page, 1);
        $model_name .= 'Model';

        // Dynamic class name is regarded as a fully qualified name.
        $class_name = '\\' . __NAMESPACE__ . '\\Models\\' . $model_name;
        $model = new $class_name();

        $action_name = $action . 'Action';

        $logger = Registry::getInstance(LOG_NAME);
        $logger->info($model_name . '->' . $action_name . '() start');
        $model->$action_name();
        $logger->info($model_name . '->' . $action_name . '() end');

        $renderer = new Renderer($model, $page, $action);
        $renderer->render();
    }
}

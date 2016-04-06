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

require dirname(__DIR__) . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Registry;
use Monolog\Handler\StreamHandler;

try {
    // Define constants
    define('PROJECT_ROOT', dirname(__DIR__));
    define('RESOURCES_ROOT', PROJECT_ROOT . '/resources');
    define('WEB_ROOT', dirname(filter_input(INPUT_SERVER, 'SCRIPT_NAME')));
    define('APP_ROOT', dirname(WEB_ROOT));

    // Load config
    $config = parse_ini_file(RESOURCES_ROOT . '/config.ini');
    foreach ($config as $key => $value) {
        $env_value = getenv($key);
        if (!empty($env_value)) {
            define($key, $env_value);
        } else {
            define($key, $value);
        }
    }

    // Set error level
    error_reporting(ERROR_LEVEL);

    // Set timezone
    date_default_timezone_set(TIMEZONE);

    // Set assertion
    assert_options(ASSERT_ACTIVE, ASSERT_ENABLED);

    // Init Logger
    $logger = new Logger(LOG_NAME);
    $logging_path = PROJECT_ROOT . LOGGING_PATH;
    $log_level = constant('Monolog\Logger::' . LOG_LEVEL);
    $logger->pushHandler(new StreamHandler($logging_path, $log_level));
    Registry::addLogger($logger);
    $logger->debug('Logging start');

    // Start a process
    $controller = new SakuraWf\Controller();
    $controller->execute();
} catch (\Exception $e) {
    if (isset($logger)) {
        $message  = get_class($e) . ', ';
        $message .= $e->getMessage() . "\n";
        $message .= $e->getTraceAsString();

        $logger->error($message);
    }

    echo $e->getMessage();
}

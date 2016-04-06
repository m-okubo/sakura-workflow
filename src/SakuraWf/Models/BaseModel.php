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

namespace SakuraWf\Models;

class BaseModel extends \SakuraWf\Model
{
    public function __construct()
    {
        parent::__construct();

        // Disable cache
        session_cache_limiter('nocache');
        // Start session
        session_start();

        if (!($this instanceof PublicPage)) {
            if (empty($_SESSION['login_id'])) {
                $this->redirect('login');
            }
        }

        $this->setLayout('index.phtml');
    }

    public function logoutAction()
    {
        $_SESSION = array();
        if (!empty(filter_input(INPUT_COOKIE, session_name()))) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        session_destroy();

        $this->redirect('login');
    }
}

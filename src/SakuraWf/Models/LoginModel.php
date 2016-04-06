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

class LoginModel extends BaseModel implements PublicPage
{
    public function indexAction()
    {
    }

    public function loginAction()
    {
        $login_id = filter_input(INPUT_POST, 'login_id');
        $password = filter_input(INPUT_POST, 'password');

        if (!$this->validate()) {
            $this->setView('login/index');

            return;
        }

        $result = $this->verify($login_id, $password);
        if (count($result) == 0) {
            $this->setView('login/index');

            return;
        }
        $_SESSION['login_id'] = $login_id;

        $this->redirect('welcome');
    }

    public function getLayout()
    {
        return null;
    }

    private function validate()
    {
        $is_valid = true;
        $login_id = filter_input(INPUT_POST, 'login_id');
        $password = filter_input(INPUT_POST, 'password');

        if (empty($login_id)) {
            $parameters = array($this->labels['login_id']);
            $this->errors['login_id'] = $this->getMessage('required', $parameters);

            $is_valid = false;
        }

        if (empty($password)) {
            $parameters = array($this->labels['password']);
            $this->errors['password'] = $this->getMessage('required', $parameters);

            $is_valid = false;
        }

        return $is_valid;
    }

    private function verify($login_id, $password)
    {
        $sql  = 'SELECT * FROM m_user AS t1';
        $sql .= ' INNER JOIN m_organization AS t2';
        $sql .= ' ON t1.organization_id = t2.organization_id';
        $sql .= ' AND t2.organization_code = :organization_code';
        $sql .= ' WHERE t1.email = :email';
        $sql .= ' AND t1.encripted_password = :encripted_password';

        $params = [];
        $params[] = ['organization_code', 'pocketsoft', \PDO::PARAM_STR];
        $params[] = ['email', $login_id, \PDO::PARAM_STR];
        $params[] = ['encripted_password', $password, \PDO::PARAM_STR];

        return $this->dbm->exec($sql, $params);
    }
}

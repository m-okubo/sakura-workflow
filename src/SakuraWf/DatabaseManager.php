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

class DatabaseManager
{
    /**
     * Logger object.
     *
     * @var object
     */
    private $logger;

    /**
     * Database handler.
     *
     * @var object
     */
    private $dbh;

    /**
     * Constructor.
     *
     * @param array $spec a named array to pass the connection info
     */
    public function __construct()
    {
        $this->logger = Registry::getInstance(LOG_NAME);

        try {
            $url = parse_url(DATABASE_URL);
            $dsn = 'pgsql:host=' . $url['host'] . ';dbname=' . substr($url['path'], 1);
            $this->logger->debug('DATABASE_URL=' . DATABASE_URL);
            $this->logger->debug('dsn=' . $dsn);
            $this->logger->debug('user=' . $url['user']);
            $this->logger->debug('pass=' . $url['pass']);
            $this->dbh = new \PDO($dsn, $url['user'], $url['pass']);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    /**
     * A method to exec sql statement.
     *
     * @param string $sql    a sql statement
     * @param array  $params parameters to be set to a sql statement
     *
     * @return array array which have row info in each element
     */
    public function exec($sql, $params = null)
    {
        $rows = array();

        if (isset($params) && count($params) > 0) {
            foreach ($params as $key => $param) {
                if (!isset($param[1])
                    || (empty($param[1]) && $param[2] === \PDO::PARAM_INT)
                ) {
                    $sql = str_replace($param[0], 'NULL', $sql);
                    unset($params[$key]);
                }
            }
        }

        $this->logger->info($sql);
        if (isset($params) && count($params) > 0) {
            $stmt = $this->dbh->prepare($sql);
            foreach ($params as $param) {
                $this->logger->info($param[0]);
                $this->logger->info($param[1]);
                $this->logger->info($param[2]);
                $stmt->bindValue($param[0], $param[1], $param[2]);
            }
            $stmt->execute();
        } else {
            $stmt = $this->dbh->query($sql);
        }

        if (Util::startsWith(strtoupper($sql), 'SELECT')) {
            $results = array();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $x = new \stdClass();
                foreach ($row as $key => $val) {
                    $x->$key = $val;
                }
                array_push($results, $x);
            }

            $result = $results;
        } else {
            $result = $stmt->rowCount();
        }

        $stmt = null;

        return $result;
    }

    /**
     * A method to get the last id generated by auto increment.
     *
     * @return int the last id
     */
    public function getLastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * Begin the transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->dbh->beginTransaction();
    }

    /**
     * Commit the transaction.
     *
     * @return void
     */
    public function commitTransaction()
    {
        $this->dbh->commit();
    }

    /**
     * Rollback the transaction.
     *
     * @return void
     */
    public function rollbackTransaction()
    {
        $this->dbh->rollBack();
    }
}

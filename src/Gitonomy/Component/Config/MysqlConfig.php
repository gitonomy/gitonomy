<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Component\Config;

use Doctrine\DBAL\Connection;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class MysqlConfig extends AbstractConfig
{
    const TABLE_NAME = '`_config`';

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritDoc}
     */
    public function doGetAll()
    {
        $stmt   = $this->runSql(sprintf('SELECT * FROM %s', self::TABLE_NAME));

        $result = array();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result[$row['key']] = json_decode($row['value']);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    protected function doSetAll(array $values)
    {
        $this->runSQL(sprintf('DELETE FROM %s', self::TABLE_NAME));

        $rows = array();
        foreach ($values as $key => $value) {
            $rows[] = '('.$this->connection->quote($key).','.$this->connection->quote(json_encode($value)).')';
        }
        $query = 'INSERT INTO '.self::TABLE_NAME.' (`key`, `value`) VALUES '.implode(', ', $rows).';';

        $this->runSQL($query);
    }

    /**
     * Creates table if does not exists.
     */
    protected function checkTable()
    {
        try {
            return $this->connection->executeQuery(sprintf('CREATE TABLE %s (`key` VARCHAR(40), `value` TEXT);', self::TABLE_NAME));
        } catch (\Exception $e) {}
    }

    /**
     * Proxy method to connection object. If an error occurred because of unfound table, tries to create table and rerun request.
     *
     * @param string $query      SQL query
     * @param array  $parameters query parameters
     */
    protected function runSQL($query, array $parameters = array())
    {
        try {
            return $this->connection->executeQuery($query, $parameters);
        } catch (\Exception $e) {
            $this->checkTable();
        }

        return $this->connection->executeQuery($query, $parameters);
    }
}

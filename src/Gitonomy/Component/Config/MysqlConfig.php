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

use Gitonomy\Component\Config\Exception\RuntimeException;

/**
 * Bulk MySQL version of Config: deletes and reinsert rows.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class MysqlConfig extends AbstractConfig
{
    /**
     * @var Connection
     */
    protected $connection;
    protected $tableName;
    protected $tableExists;

    public function __construct(Connection $connection, $tableName = '_config')
    {
        $this->connection = $connection;
        $this->tableName  = $tableName;
    }

    /**
     * {@inheritDoc}
     */
    public function doGetAll()
    {
        $stmt = $this->runSql('SELECT `key`, `value` FROM `'. $this->tableName.'`');

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
        $this->runSQL('DELETE FROM `'.$this->tableName.'`');

        $rows = array();
        foreach ($values as $key => $value) {
            $this->runSQL('INSERT INTO '.$this->tableName.' (`key`, `value`) VALUES ('.$this->connection->quote($key).','.$this->connection->quote(json_encode($value)).');');
        }
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
            $this->connection->executeQuery(sprintf('CREATE TABLE %s (`key` VARCHAR(40), `value` TEXT);', $this->tableName));
        }

        return $this->connection->executeQuery($query, $parameters);
    }
}

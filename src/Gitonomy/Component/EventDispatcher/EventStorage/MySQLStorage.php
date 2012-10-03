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

namespace Gitonomy\Component\EventDispatcher\EventStorage;

use Symfony\Component\EventDispatcher\Event;

use Doctrine\DBAL\Connection;

use Gitonomy\Component\EventDispatcher\EventStorageInterface;
use Gitonomy\Component\EventDispatcher\AsyncEvent;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class MySQLStorage implements EventStorageInterface
{
    const TABLE_NAME = '`_async_events`';

    protected $connection;
    protected $checkTable;

    public function __construct(Connection $connection, $checkTable = true, $requeueErrors = true)
    {
        $this->connection    = $connection;
        $this->checkTable    = $checkTable;
        $this->requeueErrors = $requeueErrors;

        $this->load();
    }

    public function load()
    {
        if ($this->checkTable) {
            $this->checkTable();
        }
    }

    public function store(AsyncEvent $event)
    {
        $this->runSQL($this->getCreateQuery($event));
    }

    public function getNextToProcess()
    {
        $stmt = $this->runSQL($this->getSelectQuery());

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (false === $result) {
            return null;
        }

        $eventName  = $result['eventName'];
        $event      = unserialize($result['eventSerialized']);
        $signature  = $result['signature'];

        return new AsyncEvent($eventName, $event, $signature);
    }

    public function acknowledge(AsyncEvent $event, $isSuccess)
    {
        if (!$isSuccess && $this->requeueErrors) {
            $this->runSQL($this->getUpdateQuery($event));
        } else {
            $this->runSQL($this->getDeleteQuery($event));
        }
    }

    protected function checkTable()
    {
        try {
            $this->runSQL($this->getTableQuery());
        } catch (\Exception $e) {}
    }

    protected function runSQL($query, $parameters = array())
    {
        return $this->connection->executeQuery($query, $parameters);
    }

    protected function getTableQuery()
    {
        return sprintf('CREATE TABLE %s (signature VARCHAR(40), createdAt DATETIME, eventName VARCHAR(64), eventSerialized TEXT);', self::TABLE_NAME);
    }

    protected function getCreateQuery(AsyncEvent $event)
    {
        return sprintf('INSERT INTO %s (signature, createdAt, eventName, eventSerialized) VALUES (%s, NOW(), %s, %s)',
            self::TABLE_NAME,
            $this->connection->quote($event->getSignature()),
            $this->connection->quote($event->getEventName()),
            $this->connection->quote(serialize($event->getEvent()))
        );
    }

    protected function getSelectQuery()
    {
        return sprintf('SELECT signature, eventName, eventSerialized FROM %s ORDER BY createdAt LIMIT 1', self::TABLE_NAME);
    }

    protected function getDeleteQuery(AsyncEvent $event)
    {
        return sprintf('DELETE FROM %s WHERE signature = %s', self::TABLE_NAME, $this->connection->quote($event->getSignature()));
    }

    protected function getUpdateQuery(AsyncEvent $event)
    {
        return sprintf('UPDATE %s SET createdAt = NOW() WHERE signature = %s', self::TABLE_NAME, $this->connection->quote($event->getSignature()));
    }
}

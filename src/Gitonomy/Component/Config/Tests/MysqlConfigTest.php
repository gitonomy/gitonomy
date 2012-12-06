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

namespace Gitonomy\Component\Config\Tests;

use Doctrine\DBAL\Driver\PDOSqlite\Driver as SqliteDriver;
use Doctrine\DBAL\Connection;

use Gitonomy\Component\Config\MysqlConfig;

/**
 * Tests of MySQL configuration are achieved with a SQLite database.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class MysqlConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testAbstract()
    {
        $path = $this->tempFile();
        $configA = $this->createConfig($path);

        $configA->setAll(array('foo' => 'bar', 'bar' => 'baz'));

        $configB = $this->createConfig($path);
        $this->assertEquals('bar', $configB->get('foo'));
        $this->assertEquals('baz', $configB->get('bar'));

        unlink($path);
    }

    protected function createConfig($path)
    {
        $conn = new Connection(array('path' => $path), new SqliteDriver());

        return new MysqlConfig($conn);
    }

    protected function tempFile()
    {
        $file = tempnam(sys_get_temp_dir(), 'gitonomy_');
        unlink($file);

        return $file;
    }
}

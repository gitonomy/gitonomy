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

use Gitonomy\Component\Config\PhpFileConfig;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class PhpFileConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testInexisting()
    {
        $file = $this->getTempFile();

        $storage = new PhpFileConfig($file);

        $value = $storage->get('foo');
        $this->assertNull($value, "Returns null if not present");

        $value = $storage->get('foo', false);
        $this->assertFalse($value, "Returns the default argument");

        $value = $storage->all();
        $this->assertEquals(array(), $value, "Returns the default argument");

        $this->assertFalse(file_exists($file), "No file created (read-only)");
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalid()
    {
        $file = $this->getTempFile();
        file_put_contents($file, '<?php return 1;');

        $storage = new PhpFileConfig($file);
        $storage->get('foo');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testUnreadable()
    {
        $file = $this->getTempFile();
        touch($file);
        chmod($file, 0200);

        $storage = new PhpFileConfig($file);
        $storage->get('foo');
    }

    public function testWritable()
    {
        $file = $this->getTempFile();
        $a = new PhpFileConfig($file);
        $a->set('foo', 'bar');

        $b = new PhpFileConfig($file);
        $this->assertEquals('bar', $b->get('foo'));
    }

    private function getTempFile()
    {
        $file = tempnam(sys_get_temp_dir(), 'gitonomytest_');
        unlink($file);

        return $file;
    }
}

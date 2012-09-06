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

namespace Gitonomy\Bundle\CoreBundle\Tests\Git;

use Gitonomy\Bundle\CoreBundle\Git\AuthorizedKeysGenerator;

/**
 * Tests for the generator of authorized_keys file.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class AuthorizedKeysGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Gitonomy\Bundle\CoreBundle\Git\AuthorizedKeysGenerator
     */
    protected $generator;

    public function setUp()
    {
        $this->generator = new AuthorizedKeysGenerator();
    }

    public function testEmpty()
    {
        $actual = $this->generator->generate(array(), 'foo');
        $expected = '';

        $this->assertEquals($expected, $actual);
    }

    public function testOneRow()
    {
        $actual = $this->generator->generate(array(array('username' => 'bar', 'content' => 'baz')), 'foo');
        $expected = 'command="foo bar" baz'."\n";

        $this->assertEquals($expected, $actual);
    }
}

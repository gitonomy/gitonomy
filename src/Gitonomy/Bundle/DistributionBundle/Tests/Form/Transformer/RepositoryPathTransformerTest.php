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

namespace Gitonomy\Bundle\DistributionBundle\Tests\Form\Transformer;

use Gitonomy\Bundle\DistributionBundle\Form\Transformer\RepositoryPathTransformer;

class RepositoryPathTransformerTest extends \PHPUnit_Framework_TestCase
{
    protected $transformer;

    public function setUp()
    {
        $this->transformer = new RepositoryPathTransformer();
    }

    /**
     * @dataProvider provideTransform_UsualCase_Works
     */
    public function testTransform_UsualCase_Works($input, $type, $value)
    {
        $result = $this->transformer->transform($input);
        $this->assertEquals($type,  $result['type'],  'Type expectation from input');
        $this->assertEquals($value, $result['value'], 'Value expectation from input');
    }

    public function provideTransform_UsualCase_Works()
    {
        return array(
            array('',                      'app',    'repositories'),
            array('%kernel.root_dir%/foo', 'app',    'foo'),
            array('/var/repositories',     'custom', '/var/repositories'),
        );
    }

    /**
     * @dataProvider provideReverseTransform_UsualCase_Works
     */
    public function testReverseTransform_UsualCase_Works($type, $value, $expected)
    {
        $actual = $this->transformer->reverseTransform(array(
            'type'  => $type,
            'value' => $value
        ));
        $this->assertEquals($expected,  $actual,  'Output from input');
    }

    public function provideReverseTransform_UsualCase_Works()
    {
        return array(
            array('custom', '/var/repos', '/var/repos'),
            array('app',    'repos',      '%kernel.root_dir%/repos'),
        );
    }
}

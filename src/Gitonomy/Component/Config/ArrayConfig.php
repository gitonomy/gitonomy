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

/**
 * Stores values locally in an array.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ArrayConfig implements ConfigInterface
{
    /**
     * @var array
     */
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        return isset($this->values[$key]) ? $this->values[$key] : $default;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        unset($this->values[$key]);
    }

    /**
     * {@inheritDoc}
     */
    public function all()
    {
        return $this->values;
    }

    /**
     * {@inheritDoc}
     */
    public function setAll(array $values)
    {
        $this->values = $values;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(array $values)
    {
        $this->values = array_merge($this->values, $values);
    }
}

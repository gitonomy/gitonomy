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
 * Maps single value operations to massive operations.
 *
 * Works with optimistic mode (see self::$values).
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
abstract class AbstractConfig implements ConfigInterface
{
    /**
     * Current values
     *
     * @var array
     */
    private $values;

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $values = $this->all();
        if (!isset($values[$key])) {
            return $default;
        }

        return $values[$key];
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->setAll(array_merge($this->all(), array($key => $value)));
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $all = $this->all();
        if (!isset($all[$key])) {
            return;
        }

        unset($all[$key]);

        $this->setAll($all);
    }

    /**
     * {@inheritDoc}
     */
    public function all()
    {
        if (null !== $this->values) {
            return $this->values;
        }

        $this->values = $this->doGetAll();

        return $this->values;
    }

    /**
     * {@inheritDoc}
     */
    public function setAll(array $values)
    {
        $this->values = $values;
        $this->doSetAll($values);
    }

    /**
     * {@inheritDoc}
     */
    public function merge(array $values)
    {
        $this->setAll(array_merge($this->all(), $values));
    }

    /**
     * Read all values from configuration.
     *
     * @return array All values
     */
    abstract protected function doGetAll();

    /**
     * Save all values to configuration.
     *
     * @param array $values All values to save
     */
    abstract protected function doSetAll(array $values);
}

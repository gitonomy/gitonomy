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

namespace Gitonomy\Bundle\CoreBundle\Config;

/**
 * This abstracted configuration works on "all values" mode:
 *
 * Inheriting from this class, you only need to define two methods: readAll
 * and saveValues.
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
        return $this->save(array_merge($this->all(), array($key => $value)));
    }

    /**
     * {@inheritDoc}
     */
    public function all()
    {
        if (null !== $this->values)
        {
            return $this->values;
        }

        $this->values = $this->readAll();

        return $this->values;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(array $values)
    {
        return $this->save(array_merge($this->all(), $values));
    }

    /**
     * Read all values from configuration.
     *
     * @return array All values
     */
    abstract protected function readAll();

    /**
     * Save all values to configuration.
     *
     * @param array $values All values to save
     */
    abstract protected function save(array $values);
}

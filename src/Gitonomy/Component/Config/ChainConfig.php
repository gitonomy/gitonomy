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
 * ChainConfig is used to group different configs with an order.
 *
 * This order is useful because of the reading operation: it stops when a value is found.
 *
 * For writing purpose, it propagates to all sub-configs.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ChainConfig implements ConfigInterface
{
    protected $configs;

    /**
     * @param array $configs An array of ConfigInterface objects
     */
    public function __construct(array $configs)
    {
        if (count($configs) < 2) {
            throw new \LogicException(sprintf('You don\'t need a chain if you only have %s element(s)', count($configs)));
        }

        $this->configs = array();
        foreach ($configs as $config)
        {
            if (!$config instanceof ConfigInterface) {
                throw new \InvalidArgumentException(sprintf('Expected a ConfigInterface to be provided, given a %s', get_class($config)));
            }

            $this->configs[] = $config;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        $current = $default;

        $max = count($this->configs);
        for ($i = 0; $i < $max; $i++) {
            $current = $this->configs[$i]->get($key, $default);

            if ($i === 0 && $current !== $default) {
                return $current;
            } elseif ($current !== $default) {
                break;
            }
        }

        while ($i > 0) {
            $i--;
            $this->configs[$i]->set($key, $current);
        }

        return $current;
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        foreach ($this->configs as $config) {
            $config->set($key, $value);
        }
    }
    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        foreach ($this->configs as $config) {
            $config->remove($key);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function all()
    {
        $max = count($this->configs);
        for ($i = 0; $i < $max; $i++) {
            $current = $this->configs[$i]->all();

            if ($i === 0 && count($current)) {
                return $current;
            } elseif (count($current)) {
                break;
            }
        }

        while ($i > 0 && count($current)) {
            $i--;
            $this->configs[$i]->merge($current);
        }

        return $current;
    }

    /**
     * {@inheritDoc}
     */
    public function setAll(array $values)
    {
        foreach ($this->configs as $config) {
            $config->setAll($values);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function merge(array $values)
    {
        foreach ($this->configs as $config) {
            $config->merge($values);
        }
    }
}

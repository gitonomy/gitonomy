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
 * If a config fails, exception will be caught and ignored by chain-config.
 *
 * Writing operations are propagated to all sub-configs.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ChainConfig extends AbstractConfig
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
    public function doGetAll()
    {
        $max = count($this->configs);
        for ($i = 0; $i < $max; $i++) {

            // If error occurs on reading, ignore and pass to next
            try {
                $current = $this->configs[$i]->all();
            } catch (\Exception $e) {
                continue;
            }

            if ($i === 0 && count($current)) {
                return $current;
            } elseif (count($current)) {
                break;
            }
        }

        while ($i > 0 && count($current)) {
            $i--;
            try {
                $this->configs[$i]->setAll($current);
            } catch (\Exception $e) {
                continue;
            }
        }

        return $current;
    }

    /**
     * {@inheritDoc}
     */
    public function doSetAll(array $values)
    {
        foreach ($this->configs as $config) {
             try {
               $config->setAll($values);
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}

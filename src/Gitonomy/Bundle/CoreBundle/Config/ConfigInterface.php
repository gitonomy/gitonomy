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
 * Interface for dynamic configuration of Gitonomy.
 *
 * This configuration is basically a key-value storage or scalars.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
interface ConfigInterface
{
    /**
     * Fetch a configuration value.
     *
     * @param string $key     Key value
     * @param mixed  $default Value to return if nothing is found in confiugration
     *
     * @return mixed Value if exists, $default otherwise
     */
    public function get($key, $default = null);

    /**
     * Set a configuration value.
     *
     * @param string $key   Key of the value to set
     * @param mixed  $value Value to set
     */
    public function set($key, $value);

    /**
     * Returns all values.
     *
     * @return array All values
     */
    public function all();

    /**
     * Merge current configuration with a given array.
     *
     * @param array $values Values to merge in current configuration
     */
    public function merge(array $values);
}

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
     * Removes a given value from config.
     *
     * @param string $value The key of value to remove.
     */
    public function remove($key);

    /**
     * Returns all values.
     *
     * @return array All values
     */
    public function all();

    /**
     * Replaces all values with given one.
     */
    public function setAll(array $values);

    /**
     * Merge values in current config
     */
    public function merge(array $values);
}

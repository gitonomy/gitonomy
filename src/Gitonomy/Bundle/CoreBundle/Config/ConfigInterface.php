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
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
interface ConfigInterface
{
    public function get($key, $default = null);
    public function set($key, $value);
    public function all();
    public function merge(array $values);
}

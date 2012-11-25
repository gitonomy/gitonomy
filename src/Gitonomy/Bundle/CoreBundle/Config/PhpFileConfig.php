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
class PhpFileConfig extends AbstractConfig
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function readAll()
    {
        if (!file_exists($this->path)) {
            return array();
        }

        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('Unable to read "%s"', $this->path));
        }

        $raw = require $this->path;
        if (!is_array($raw)) {
            throw new \RuntimeException(sprintf('Content of file "%s" is invalid', $this->path));
        }

        return $raw;
    }

    protected function save(array $values)
    {
        file_put_contents($this->path, '<?php return '.var_export($values, true).';');
        $this->values = $values;

        return $this;
    }
}

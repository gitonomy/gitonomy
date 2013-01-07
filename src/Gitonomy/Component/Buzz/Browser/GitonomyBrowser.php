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

namespace Gitonomy\Component\Buzz\Browser;

use Buzz\Message\Request;
use Buzz\Browser;

/**
 * Buzz browser configured for Gitonomy APIs.
 *
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class GitonomyBrowser extends Browser
{
    const VERSION_PATTERN = '/^([0-9\.]+)(?:\-(\w+))?/';

    protected $currentVersion;
    protected $stable;

    public function getChangeLog()
    {
        $resource = sprintf('/changelog.json?from_version=%s', $this->getCurrentVersion());

        if (!$this->stable) {
            $resource.= '&stable=false';
        }

        $request = new Request('GET', $resource);

        return $this->send($request)->getJsonContent();
    }

    public function setCurrentVersion($currentVersion)
    {
        preg_match(self::VERSION_PATTERN, $currentVersion, $version);

        if (!$version) {
            throw new \LogicException(sprintf('Version "%s" does not match to "%s"', $currentVersion, self::VERSION_PATTERN));
        }

        $this->currentVersion = $version[1];

        if (!isset($version[2])) {
            $version[2] = 'stable';
        }

        $this->stable = 'dev' !== strtolower($version[2]);
    }

    protected function getCurrentVersion()
    {
        if (null === $this->currentVersion) {
            throw new \LogicException('No currentVersion set');
        }

        return $this->currentVersion;
    }
}

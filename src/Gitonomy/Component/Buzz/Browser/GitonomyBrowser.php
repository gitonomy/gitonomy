<?php

namespace Gitonomy\Component\Buzz\Browser;

use Buzz\Message\Request;
use Buzz\Browser;

class GitonomyBrowser extends Browser
{
    const VERSION_PATTERN = '/^([0-9\.]+)(?:\-(\w+))?/';

    private $currentVersion;
    private $stable;

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

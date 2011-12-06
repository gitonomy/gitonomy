<?php

namespace Gitonomy\Bundle\CoreBundle\Git\System;

use Gitonomy\Bundle\CoreBundle\Git\SystemInterface;

/**
 * Implementation of Git tasks with native binary.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class NativeSystem implements SystemInterface
{
    /**
     * Path to Git binary
     *
     * @var string
     */
    protected $binary;

    /**
     * Constructor.
     *
     * @param string $binary Path to git
     */
    public function __construct($binary = 'git')
    {
        $this->binary = $binary;
    }

    /**
     * @inheritdoc
     */
    public function createRepository($path)
    {
        if (is_dir($path)) {
            throw new \RuntimeException(sprintf('The folder "%s" already exists', $path));
        }

        system(sprintf('%s init -q --bare %s', $this->binary, $path));

        if (!is_dir($path)) {
            throw new \RuntimeException(sprintf('Unable to create repository "%s"'));
        }
    }

    /**
     * @inheritdoc
     */
    public function cloneRepository($pathFrom, $pathTo)
    {
        if (is_dir($pathTo)) {
            throw new \RuntimeException(sprintf('The folder "%s" already exists', $pathTo));
        }

        system(sprintf('%s clone -q --bare --no-hardlinks %s %s', $this->binary, $pathFrom, $pathTo));

        if (!is_dir($pathTo)) {
            throw new \RuntimeException(sprintf('Unable to create repository "%s"'));
        }
    }

    /**
     * @inheritdoc
     */
    public function executeShell($command, $path)
    {
        $argument = sprintf('%s \'%s\'', $command, $path);

        proc_open('git shell -c '.escapeshellarg($argument), array(STDIN, STDOUT, STDERR), $pipes);

    }
}

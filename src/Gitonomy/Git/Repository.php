<?php

namespace Gitonomy\Git;

class Repository
{
    protected $path;

    public function __construct($path)
    {
        $this->path   = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getRevision($name)
    {
        return new Revision($this, $name);
    }

    public function getCommit($hash)
    {
        return new Commit($this, $hash);
    }

    public function shell($command)
    {
        $argument = sprintf('%s \'%s\'', $command, $this->path);

        proc_open('git shell -c '.escapeshellarg($argument), array(STDIN, STDOUT, STDERR), $pipes);
    }
}

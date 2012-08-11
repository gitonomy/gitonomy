<?php

namespace Gitonomy\Git;

class Log
{
    protected $repository;
    protected $revisions;
    protected $limit;

    public function __construct(Repository $repository, $revisions, $limit = null)
    {
        $this->repository = $repository;
        $this->revisions = $revisions;
        $this->limit = $limit;
    }

    public function getCommits()
    {
        ob_start();
        system(sprintf(
            'cd %s && git log --format="format:%s" %s %s',
            escapeshellarg($this->repository->getPath()),
            '%H',
            null !== $this->limit ? '-n '.((int) $this->limit) : '',
            escapeshellarg($this->revisions)
        ), $result);
        $output = ob_get_clean();

        $exp = explode("\n", $output);

        $result = array();
        foreach ($exp as $hash) {
            $result[] = $this->repository->getCommit($hash);
        }

        return $result;
    }
}

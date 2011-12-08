<?php

namespace Gitonomy\Git;

class Commit
{
    protected $repository;
    protected $hash;

    protected $initialized;
    protected $treeHash;
    protected $parentHash;
    protected $authorName;
    protected $authorEmail;
    protected $authorDate;
    protected $committerName;
    protected $committerEmail;
    protected $committerDate;
    protected $message;

    function __construct(Repository $repository, $hash)
    {
        $this->repository = $repository;
        $this->hash = $hash;
        $this->initialized = false;
    }

    protected function initialize()
    {
        if (true === $this->initialized) {
            return;
        }

        ob_start();
        system(sprintf(
            'cd %s && git cat-file commit %s',
            escapeshellarg($this->repository->getPath()),
            escapeshellarg($this->hash)
        ), $return);
        $result = ob_get_clean();

        if (0 !== $return) {
            throw new \RuntimeException('Error while getting content of a commit');
        }

        $pattern = '/'.
            'tree (?<tree>[A-Za-z0-9]{40})'.
            "\n".
            '(parent (?<parent>[A-Za-z0-9]{40})'.
            "\n)?".
            'author (?<author_name>.*) <(?<author_email>.*)> (?<author_date>\d+ \+\d{4})'.
            "\n".
            'committer (?<committer_name>.*) <(?<committer_email>.*)> (?<committer_date>\d+ \+\d{4})'.
            "\n\n".
            '(?<message>[^$]*)'.
            '/'
        ;

        if (!preg_match($pattern, $result, $vars)) {
            throw new \RuntimeException('Unable to parse a commit');
        }

        $this->treeHash       = $vars['tree'];
        $this->parentHash     = $vars['parent'];
        $this->authorName     = $vars['author_name'];
        $this->authorEmail    = $vars['author_email'];
        $this->authorDate     = $vars['author_date'];
        $this->committerName  = $vars['committer_name'];
        $this->committerEmail = $vars['committer_email'];
        $this->committerDate  = $vars['committer_date'];
        $this->message        = $vars['message'];

        $this->initialized = true;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getParentHash()
    {
        $this->initialize();

        return $this->parentHash;
    }

    public function getParent()
    {
        $this->initialize();

        if ("" === $this->parentHash) {
            return null;
        }

        return $this->repository->getCommit($this->parentHash);
    }

    public function getTreeHash()
    {
        $this->initialize();

        return $this->treeHash;
    }

    public function getAuthorName()
    {
        $this->initialize();

        return $this->authorName;
    }

    public function getAuthorEmail()
    {
        $this->initialize();

        return $this->authorEmail;
    }

    public function getAuthorDate()
    {
        $this->initialize();

        return $this->authorDate;
    }

    public function getCommitterName()
    {
        $this->initialize();

        return $this->committerName;
    }

    public function getCommitterEmail()
    {
        $this->initialize();

        return $this->committerEmail;
    }

    public function getCommitterDate()
    {
        $this->initialize();

        return $this->committerDate;
    }

    public function getMessage()
    {
        $this->initialize();

        return $this->message;
    }


}

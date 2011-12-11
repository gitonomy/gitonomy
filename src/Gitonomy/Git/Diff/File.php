<?php

namespace Gitonomy\Git\Diff;

class File
{
    protected $oldName;
    protected $newName;
    protected $oldMode;
    protected $newMode;
    protected $changes;
    protected $isBinary;

    function __construct($oldName, $newName, $oldMode, $newMode, $isBinary)
    {
        $this->oldName = $oldName;
        $this->newName = $newName;
        $this->oldMode = $oldMode;
        $this->newMode = $newMode;
        $this->changes = array();
        $this->isBinary = $isBinary;
    }

    public function getAdditions()
    {
        $result = 0;
        foreach ($this->changes as $change)
        {
            $result += $change->getCount(FileChange::LINE_ADD);
        }
        return $result;
    }

    public function getDeletions()
    {
        $result = 0;
        foreach ($this->changes as $change)
        {
            $result += $change->getCount(FileChange::LINE_REMOVE);
        }
        return $result;
    }

    public function addChange(FileChange $change)
    {
        $this->changes[] = $change;
    }

    public function getOldName()
    {
        return $this->oldName;
    }

    public function getNewName()
    {
        return $this->newName;
    }

    public function getOldMode()
    {
        return $this->oldMode;
    }

    public function getNewMode()
    {
        return $this->newMode;
    }

    public function getChanges()
    {
        return $this->changes;
    }

    public function isBinary()
    {
        return $this->isBinary;
    }

}

<?php

namespace Gitonomy\Component\Git\Graph;

class Cell
{
    public $dot = - 1000;
    public $dotColor = 0;
    public $links = array();

    public function findFreeHeight()
    {
        $H = 0;
        while (!$this->isFree($H)) {
            $H ++;
        }

        return $H;
    }

    public function isFree($height)
    {
        if (isset($this->dot[$height]) && $this->dot[$height]) {
            return false;
        }

        // Nodes with origin binding on height
        foreach ($this->links as $link) {
            if ($link[0] == $height) {
                return false;
            }
        }

        return true;
    }

    public function getJson()
    {
        $id = md5(uniqid().microtime());

        return array($id, array($this->dot, $this->dotColor), $this->links);
    }
}

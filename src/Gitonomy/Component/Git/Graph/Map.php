<?php

namespace Gitonomy\Component\Git\Graph;

class Map
{
    private $cells;

    public function __construct($length) {
        $this->cells = array();
        for ($N = 0;$N < $length; $N++) {
            $this->cells[$N] = new Cell();
        }
    }

    public function setDot($N, $H)
    {
        $cell = $this->getCell($N)->dot = $H;
    }

    public function drawLine($fromN, $fromH, $toN, $toH)
    {
        for ($N = $fromN; $N < $toN; $N++) {
            if ($N !== $toN - 1) {
                $H = $this->findFreeHeight($N);
            } else {
                $H = $toH;
            }
            $this->cells[$N]->links[] = array($fromH, $H, 0);

            $fromH = $H;
        }
    }

    public function findFreeHeight($N)
    {
        return $this->getCell($N)->findFreeHeight();
    }

    public function getCell($N)
    {
        if (!isset($this->cells[$N])) {
            throw new \OutOfRangeException('No cell '.$N);
        }

        return $this->cells[$N];
    }

    public function getCells()
    {
        return $this->cells;
    }

    public function getJson()
    {
        $result = array();
        foreach ($this->cells as $cell) {
            $result[] = $cell->getJson();
        }

        return $result;
    }
}

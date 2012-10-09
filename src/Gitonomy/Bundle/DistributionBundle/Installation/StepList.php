<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation;

class StepList implements \IteratorAggregate, \Countable
{
    protected $steps;

    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }

    public function getStep($slug)
    {
        foreach ($this->steps as $step) {
            if ($step->getSlug() == $slug) {
                return $step;
            }
        }

        throw new \InvalidArgumentException(sprintf('Step "%s" not found', $slug));
    }

    public function count()
    {
        return count($this->steps);
    }

    public function getFirst()
    {
        reset($this->steps);

        return current($this->steps);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->steps);
    }
}

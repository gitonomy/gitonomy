<?php

namespace Gitonomy\Git;

class ReceiveReference
{
    protected $repository;
    protected $before;
    protected $after;
    protected $reference;

    public function __construct(Repository $repository, $before, $after, $reference)
    {
        $this->repository = $repository;
        $this->before     = $before;
        $this->after      = $after;
        $this->reference  = $reference;
    }
}

<?php

namespace Gitonomy\Git\Exception;

class ReferenceNotFoundException extends \InvalidArgumentException implements GitExceptionInterface
{
    public function __construct($reference)
    {
        parent::__construct(sprintf('Reference not found'));
    }
}

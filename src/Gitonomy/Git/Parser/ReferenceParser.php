<?php

namespace Gitonomy\Git\Parser;

class ReferenceParser extends ParserBase
{
    public $references;

    protected function doParse()
    {
        $this->references = array();

        while (!$this->isFinished()) {
            $hash = $this->consumeHash();
            $this->consume(" ");
            $name = $this->consumeTo("\n");
            $this->consumeNewLine();
            $this->references[] = array($hash, $name);
        }
    }
}

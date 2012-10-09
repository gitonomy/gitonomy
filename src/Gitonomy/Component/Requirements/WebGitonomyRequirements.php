<?php

namespace Gitonomy\Component\Requirements;

use Symfony\Component\HttpFoundation\Request;

class WebGitonomyRequirements extends GitonomyRequirements
{
    public function __construct()
    {
        $this->addRequirement($this->isDocumentRoot(), 'Your app/ folder is accessible to the world');
    }

    protected function isDocumentRoot()
    {
        $request = Request::createFromGlobals();

        return !preg_match('#web/install.php$#', $request->getBaseUrl());
    }
}

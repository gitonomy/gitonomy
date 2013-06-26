<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Component\Requirements;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class WebGitonomyRequirements extends GitonomyRequirements
{
    public function __construct()
    {
        $this->addRequirement($this->isDocumentRoot(), 'Your app/ folder is accessible to the world');

        parent::__construct();
    }

    protected function isDocumentRoot()
    {
        $request = Request::createFromGlobals();

        return !preg_match('#web/install.php$#', $request->getBaseUrl());
    }
}

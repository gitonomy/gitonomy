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

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Base class for every controller of the frontend.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class BaseController extends Controller
{
    protected function assertGranted($attributes, $object = null)
    {
        if (!$this->get('security.context')->isGranted($attributes, $object)) {
            throw new AccessDeniedException();
        }
    }
}

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

namespace Gitonomy\Bundle\CoreBundle\EventDispatcher;

final class GitonomyEvents
{
    const PROJECT_CREATE = 'gitonomy.project_create';
    const PROJECT_PUSH   = 'gitonomy.project_push';
    const PROJECT_DELETE = 'gitonomy.project_delete';

    const GIT_DELETE = 'gitonomy.git_delete';
    const GIT_CREATE = 'gitonomy.git_create';
    const GIT_FORCE = 'gitonomy.git_force';
    const GIT_WRITE = 'gitonomy.git_write';

    public static function getEvents()
    {
        return array(
            'GIT_DELETE' => self::GIT_DELETE,
            'GIT_CREATE' => self::GIT_CREATE,
            'GIT_FORCE'  => self::GIT_FORCE,
            'GIT_WRITE'  => self::GIT_WRITE,
        );
    }

    public static function getEventFromRequest($request)
    {
        $events = self::getEvents();

        return $events[$request];
    }
}

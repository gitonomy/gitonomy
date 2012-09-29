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

namespace Gitonomy\Bundle\CoreBundle\EventDispatcher\Event;

use Gitonomy\Git\PushReference;

use Symfony\Component\EventDispatcher\Event;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class PushReferenceEvent extends Event
{
    const GIT_CREATE = 'create';
    const GIT_DELETE = 'delete';
    const GIT_FORCE  = 'force';
    const GIT_WRITE  = 'write';

    protected $project;
    protected $user;
    protected $reference;

    public function __construct(Project $project, User $user, PushReference $reference)
    {
        $this->project   = $project;
        $this->user      = $user;
        $this->reference = $reference;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getReference()
    {
        return $this->reference;
    }
}

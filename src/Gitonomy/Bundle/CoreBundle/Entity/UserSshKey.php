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

namespace Gitonomy\Bundle\CoreBundle\Entity;

class UserSshKey
{
    protected $id;
    protected $user;
    protected $title;
    protected $content;
    protected $isInstalled;

    public function __construct(User $user, $title = null, $content = null)
    {
        $this->user        = $user;
        $this->title       = $title;
        $this->content     = $content;
        $this->isInstalled = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function isInstalled()
    {
        return $this->isInstalled;
    }

    public function setInstalled($isInstalled)
    {
        $this->isInstalled = $isInstalled;
    }
}

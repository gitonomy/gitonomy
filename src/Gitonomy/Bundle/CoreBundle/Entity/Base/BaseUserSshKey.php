<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Base;

use Gitonomy\Bundle\CoreBundle\Entity\User;

abstract class BaseUserSshKey
{
    protected $id;
    protected $user;
    protected $title;
    protected $content;
    protected $isInstalled;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
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

    public function getIsInstalled()
    {
        return $this->isInstalled;
    }

    public function setIsInstalled($isInstalled)
    {
        $this->isInstalled = $isInstalled;
    }
}

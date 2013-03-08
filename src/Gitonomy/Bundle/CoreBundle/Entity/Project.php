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

use Doctrine\Common\Collections\ArrayCollection;
use Gitonomy\Git\Repository;

class Project
{
    const SLUG_PATTERN = '[a-zA-Z0-9-_]+';

    protected $id;
    protected $name;
    protected $slug;
    protected $repositorySize;
    protected $userRoles;
    protected $gitAccesses;
    protected $feeds;
    protected $defaultBranch = 'master';
    protected $repository;

    public function __construct($name = null, $slug = null)
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->userRoles   = new ArrayCollection();
        $this->gitAccesses = new ArrayCollection();
    }

    /**
     * Returns the user role of a given user.
     *
     * @return UserRoleProject The user role on the project
     *
     * @throws InvalidArgumentException Throws an exception if no role was found for the given user on the project.
     */
    public function getUserRole(User $user)
    {
        foreach ($this->userRoles as $userRole) {
            if ($user->equals($userRole->getUser())) {
                return $userRole;
            }
        }

        return null;
    }

    public function getUsers()
    {
        $result = array();
        foreach ($this->userRoles as $userRole) {
            $result[] = $userRole->getUser();
        }

        return $result;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getRepositorySize()
    {
        return $this->repositorySize;
    }

    public function setRepositorySize($repositorySize)
    {
        $this->repositorySize = $repositorySize;
    }

    public function getUserRoles()
    {
        return $this->userRoles;
    }

    public function getGitAccesses()
    {
        return $this->gitAccesses;
    }

    public function getDefaultBranch()
    {
        return $this->defaultBranch;
    }

    public function setDefaultBranch($defaultBranch)
    {
        $this->defaultBranch = $defaultBranch;
    }

    public function getFeeds()
    {
        return $this->feeds;
    }

    public function setFeeds($feeds)
    {
        $this->feeds = $feeds;
    }

    public function getRepository()
    {
        if (!$this->hasRepository()) {
            throw new \LogicException("No repository injected in project");
        }

        return $this->repository;
    }

    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function hasRepository()
    {
        return null !== $this->repository;
    }

    public function isEmpty()
    {
        try {
            return !$this->getRepository()->getReferences()->hasBranches();
        } catch (\LogicException $e) {
            throw new \RuntimeException('Unable to determine if repository is empty', null, $e);
        }
    }
}


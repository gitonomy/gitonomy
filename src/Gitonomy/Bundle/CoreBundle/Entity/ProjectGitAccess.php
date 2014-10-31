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

class ProjectGitAccess
{
    const WRITE_PERMISSION = 1;
    const ADMIN_PERMISSION = 2;

    protected $id;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Role
     */
    protected $role;

    protected $reference;
    protected $isWrite;
    protected $isAdmin;

    public function __construct(Project $project, Role $role = null, $reference = '*', $isWrite = false, $isAdmin = false)
    {
        $this->project   = $project;
        $this->role      = $role;
        $this->reference = $reference;
        $this->isWrite   = $isWrite;
        $this->isAdmin   = $isAdmin;
    }

    /**
     * @param string $reference  Fully qualified reference name ("refs/heads/master")
     * @param int    $permission Write or Admin permission (see self::*_PERMISSION)
     */
    public function isGranted(User $user, $reference, $permission)
    {
        $userRole = $this->project->getUserRole($user);

        if (!$userRole || !$userRole->isRole($this->role)) {
            return false;
        }

        return $this->matches($reference) && $this->verifyPermission($permission);
    }

    public function matches($reference)
    {
        $pattern = preg_quote($this->reference);
        $pattern = str_replace('\*', '.*', $pattern);
        $pattern = '/^refs\/(heads|tags)\/'.$pattern.'$/';

        return 0 != preg_match($pattern, $reference);
    }

    public function verifyPermission($permission)
    {
        if ($permission === self::WRITE_PERMISSION) {
            return $this->isWrite || $this->isAdmin;
        } elseif ($permission === self::ADMIN_PERMISSION) {
            return $this->isAdmin;
        }
        throw new \InvalidArgumentException('Unknown permission '.$permission);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getProject()
    {
        return $this->project;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function isWrite()
    {
        return $this->isWrite;
    }

    public function setWrite($isWrite)
    {
        $this->isWrite = $isWrite;
    }

    public function isAdmin()
    {
        return $this->isAdmin;
    }

    public function setAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }
}

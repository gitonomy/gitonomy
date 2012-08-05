<?php

namespace Gitonomy\Bundle\CoreBundle\Security;

use Symfony\Component\Security\Core\Role\RoleInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Project;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ProjectRole implements RoleInterface
{
    protected $slug;
    protected $role;

    public function __construct(Project $project, $role)
    {
        $this->slug = $project->getSlug();
        $this->role = $role;
    }

    public function isProject(Project $project)
    {
        return $project->getSlug() == $this->slug;
    }

    public function getProjectRole()
    {
        return $this->role;
    }

    public function getRole()
    {
        return null;
    }
}

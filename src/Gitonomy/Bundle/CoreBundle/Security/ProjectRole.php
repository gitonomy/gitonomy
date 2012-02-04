<?php

namespace Gitonomy\Bundle\CoreBundle\Security;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * User role on a project.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ProjectRole implements RoleInterface
{
    protected $projectId;
    protected $projectRole;

    public function __construct($projectId, $projectRole)
    {
        $this->projectId   = $projectId instanceof Project ? $projectId->getId() : $projectId;
        $this->projectRole = $projectRole;
    }

    public function isProjectId($projectId)
    {
        if ($projectId instanceof Project) {
            $projectId = $projectId->getId();
        }

        return $projectId == $this->projectId;
    }

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function getProjectRole()
    {
        return $this->projectRole;
    }

    public function getRole()
    {
        return null;
    }
}

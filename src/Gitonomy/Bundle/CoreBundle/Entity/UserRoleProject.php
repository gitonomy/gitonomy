<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class UserRoleProject extends Base\BaseUserRoleProject
{
    public function __toString()
    {
        return sprintf('%s is %s in the project %s', $this->getUser(), $this->getRole(), $this->getProject());
    }
}

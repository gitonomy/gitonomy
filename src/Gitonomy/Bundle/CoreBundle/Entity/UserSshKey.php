<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class UserSshKey extends Base\BaseUserSshKey
{
    public function __construct()
    {
        $this->isInstalled = false;
    }
}

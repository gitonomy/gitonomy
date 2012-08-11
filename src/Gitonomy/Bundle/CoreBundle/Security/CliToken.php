<?php

namespace Gitonomy\Bundle\CoreBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class CliToken extends AbstractToken
{
    public function getCredentials()
    {
        return array();
    }
}

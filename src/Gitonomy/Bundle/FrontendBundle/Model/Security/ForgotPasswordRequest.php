<?php

namespace Gitonomy\Bundle\FrontendBundle\Model\Security;

class ForgotPasswordRequest
{
    protected $email;

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
}

<?php

namespace Gitonomy\Bundle\FrontendBundle\Model\Security;

use Symfony\Component\Validator\Constraints as Assert;

use Gitonomy\Bundle\FrontendBundle\Validation\Constraints as GitonomyAssert;

class ForgotPassword
{
    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @GitonomyAssert\UserEmail
     */
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

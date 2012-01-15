<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class UserForgotPassword extends Base\BaseUserForgotPassword
{
    public function __construct(User $user)
    {
        $this->user      = $user;
    }

    public function generateToken()
    {
        $this->token     = md5(uniqid().microtime());
        $this->createdAt = new \DateTime();
    }

    /**
     * Tests if the forgot password token is expired.
     *
     * @return boolean Result of test
     */
    public function isTokenExpired()
    {
        $max = clone $this->createdAt;
        $max->add(new \DateInterval('P2D')); // 2 days
        $now = new \DateTime();

        return $now->getTimestamp() > $max->getTimeStamp();
    }
}

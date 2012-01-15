<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

class Email extends Base\BaseEmail
{
    public function __construct()
    {
        $this->isDefault = false;
        parent::__construct();
    }

    public function __toString()
    {
        return $this->email;
    }

    public function isActivated()
    {
        return null === $this->activation;
    }

    public function generateActivationHash()
    {
        $timestamp = new \DateTime();
        $this->activation = md5($timestamp->format('U').$this->email);
    }
}

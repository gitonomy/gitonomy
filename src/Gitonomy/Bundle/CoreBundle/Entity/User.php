<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class User extends Base\BaseUser implements UserInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->regenerateSalt(); // @todo No sense?
    }
    public function __toString()
    {
        return $this->fullname;
    }

    public function equals(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        return $user->getUsername() === $this->username;
    }

    public function eraseCredentials()
    {
    }

    public function getRoles()
    {
        return array_merge($this->getGlobalPermissions(), array('AUTHENTICATED' => 'AUTHENTICATED'));
    }

    public function regenerateSalt()
    {
        return $this->salt = md5(uniqid().microtime());
    }

    public function getGlobalPermissions()
    {
        $permissions = array();

        foreach ($this->getUserRolesGlobal() as $userRole) {
            foreach ($userRole->getPermissions() as $permission) {
                if ($permission->hasParent()) {
                    $perm = $permission->getParent()->getName();
                    $permissions[$perm] = $perm;
                }

                $perm = $permission->getName();
                $permissions[$perm] = $perm;
            }
        }

        return $permissions;
    }

    public function getDefaultEmail()
    {
        foreach ($this->getEmails() as $email) {
            if ($email->isDefault()) {
                return $email;
            }
        }
    }

    public function hasDefaultEmail()
    {
        return null !== $this->getDefaultEmail();
    }

    public function setDefaultEmail(Email $email)
    {
        if ($this->hasDefaultEmail()) {
            throw new \LogicException(sprintf('User "%s" has already a default email address : "%s"', $this, $this->getDefaultEmail()));
        }

        $email->setUser($this);
        $email->setIsDefault(true);
        $this->addEmail($email);
    }

    public function createForgotPasswordToken()
    {
        $this->forgotPasswordToken     = md5(uniqid().microtime());
        $this->forgotPasswordCreatedAt = new \DateTime();
    }

    public function removeForgotPasswordToken()
    {
        $this->forgotPasswordToken     = null;
        $this->forgotPasswordCreatedAt = null;
    }

    public function isForgotPasswordTokenExpired()
    {
        $max = clone $this->forgotPasswordCreatedAt;
        $max->add(new \DateInterval('P2D')); // 2 days
        $now = new \DateTime();

        return $now->getTimestamp() > $max->getTimeStamp();
    }

    public function markAllKeysAsUninstalled()
    {
        foreach ($this->sshKeys as $sshKey) {
            $sshKey->setIsInstalled(false);
        }
    }

    public function generateActivationToken()
    {
        $this->activationToken = md5(uniqid().microtime());
    }

    public function removeActivationToken()
    {
        $this->activationToken = null;
    }

    public function isActived()
    {
        return (null !== $this->password && null === $this->activationToken);
    }

    public function addUserRoleGlobal(Role $userRoleGlobal)
    {
        $this->userRolesGlobal->add($userRoleGlobal);
    }

    public function addEmail(Email $email)
    {
        if (!$this->hasDefaultEmail()) {
            $email->setIsDefault(true);
        }

        $email->setUser($this);
        $this->emails->add($email);
    }
}

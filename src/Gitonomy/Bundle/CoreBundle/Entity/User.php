<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User in Gitonomy.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com
 * @author Julien DIDIER <julien@jdidier.net>
 */
class User extends Base\BaseUser implements UserInterface
{
    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->fullname;
    }

    /**
     * @inheritdoc
     */
    public function equals(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        return $user->getUsername() === $this->username;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritdoc
     */
    public function regenerateSalt()
    {
        return $this->salt = md5(uniqid().microtime());
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
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

        return array_merge($permissions, array('AUTHENTICATED' => 'AUTHENTICATED'));
    }

    /**
     * Returns the default email.
     *
     * @return Email The default e-mail or null if not found.
     */
    public function getDefaultEmail()
    {
        foreach ($this->getEmails() as $email) {
            if ($email->isDefault()) {
                return $email;
            }
        }
    }

    /**
     * Tests if a default email exists for the user.
     *
     * @return boolean Result of test
     */
    public function hasDefaultEmail()
    {
        return null !== $this->getDefaultEmail();
    }

    /**
     * Defines an mail as default.
     *
     * @param Email $email An email to mark as default
     *
     * @throws \LogicException An exceptions is thrown if a default mail already
     * exists for this user.
     */
    public function setDefaultEmail(Email $email)
    {
        if ($this->hasDefaultEmail()) {
            throw new \LogicException(sprintf('User "%s" has already a default email address : "%s"', $this, $this->getDefaultEmail()));
        }

        $email->setUser($this);
        $email->setIsDefault(true);
        $this->addEmail($email);
    }

    /**
     * Fills fields to create a new forgot password token.
     */
    public function createForgotPasswordToken()
    {
        $this->forgotPasswordToken     = md5(uniqid().microtime());
        $this->forgotPasswordCreatedAt = new \DateTime();
    }

    /**
     * Removes values associated to the forgot password token.
     */
    public function removeForgotPasswordToken()
    {
        $this->forgotPasswordToken     = null;
        $this->forgotPasswordCreatedAt = null;
    }

    /**
     * Tests if the forgot password token is expired.
     *
     * @return boolean Result of test
     */
    public function isForgotPasswordTokenExpired()
    {
        $max = clone $this->forgotPasswordCreatedAt;
        $max->add(new \DateInterval('P2D')); // 2 days
        $now = new \DateTime();

        return $now->getTimestamp() > $max->getTimeStamp();
    }

    /**
     * Marks all SSH keys of the user as uninstalled.
     */
    public function markAllKeysAsUninstalled()
    {
        foreach ($this->sshKeys as $sshKey) {
            $sshKey->setIsInstalled(false);
        }
    }

    /**
     * Generates a new activation token.
     */
    public function generateActivationToken()
    {
        $this->activationToken = md5(uniqid().microtime());
    }

    /**
     * Removes the activation token from the user.
     */
    public function removeActivationToken()
    {
        $this->activationToken = null;
    }

    /**
     * Tests of the account is activated.
     *
     * @return boolean Result of the test
     */
    public function isActivated()
    {
        return (null !== $this->password && null === $this->activationToken);
    }

    /**
     * Adds a new global role to the user.
     *
     * @param Role $userRoleGlobal The role to add
     */
    public function addUserRoleGlobal(Role $userRoleGlobal)
    {
        $this->userRolesGlobal->add($userRoleGlobal);
    }

    /**
     * Adds a new email to the user.
     *
     * @param Email $email The email to add.
     */
    public function addEmail(Email $email)
    {
        if (!$this->hasDefaultEmail()) {
            $email->setIsDefault(true);
        }

        $email->setUser($this);
        $this->emails->add($email);
    }
}

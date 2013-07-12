<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * User in Gitonomy.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com
 * @author Julien DIDIER <julien@jdidier.net>
 */
class User implements UserInterface
{
    protected $id;
    protected $username;
    protected $password;
    protected $salt;
    protected $fullname;
    protected $timezone;
    protected $locale;
    protected $activationToken;

    /**
     * @var ArrayCollection
     */
    protected $sshKeys;

    /**
     * @var ArrayCollection
     */
    protected $emails;

    /**
     * @var ArrayCollection
     */
    protected $projectRoles;

    /**
     * @var ArrayCollection
     */
    protected $globalRoles;

    public function __construct($username = null, $fullname = null, $timezone = null)
    {
        $this->username     = $username;
        $this->fullname     = $fullname;
        $this->timezone     = null === $timezone ? date_default_timezone_get() : $timezone;
        $this->sshKeys      = new ArrayCollection();
        $this->emails       = new ArrayCollection();
        $this->projectRoles = new ArrayCollection();
        $this->globalRoles  = new ArrayCollection();
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
    public function setPassword($raw, PasswordEncoderInterface $encoder = null)
    {
        $this->salt = md5(uniqid().microtime());

        if (null === $encoder) {
            $this->password = $raw;

            return;
        }

        $this->password = $encoder->encodePassword($raw, $this->salt);
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        $roles = array();

        foreach ($this->getProjectRoles() as $projectRole) {
            $roles = array_merge($roles, $projectRole->getSecurityRoles());
        }

        foreach ($this->getGlobalRoles() as $globalRole) {
            $roles = array_merge($roles, $globalRole->getSecurityRoles());
        }

        return $roles;
    }

    /**
     * @return Email
     */
    public function createEmail($email = null, $setDefault = false)
    {
        $email = new Email($this, $email);
        $this->emails->add($email);

        if ($setDefault) {
            $this->setDefaultEmail($email);
        }

        return $email;
    }

    public function createSshKey($title, $content)
    {
        $key = new UserSshKey($this, $title, $content);
        $this->sshKeys->add($key);

        return $key;
    }

    public function setDefaultEmail($email)
    {
        if (is_string($email)) {
            $email = new Email($this, $email);
        } elseif (!$email instanceof Email) {
            throw new \InvalidArgumentException(sprintf('Unable to convert a %s to a Email', gettype($email)));
        }

        foreach ($this->emails as $current) {
            if ($current !== $email && $current->isDefault()) {
                $current->setDefault(false);
            }
        }

        $email->setDefault(true);

        return $email;
    }

    public function getDefaultEmail($createNew = true)
    {
        foreach ($this->getEmails() as $email) {
            if ($email->isDefault()) {
                return $email;
            }
        }

        if (true === $createNew) {
            return $this->createEmail(null, true);
        }
    }

    public function hasDefaultEmail()
    {
        return null !== $this->getDefaultEmail(false);
    }

    /**
     * Marks all SSH keys of the user as uninstalled.
     */
    public function markAllKeysAsUninstalled()
    {
        foreach ($this->sshKeys as $sshKey) {
            $sshKey->setInstalled(false);
        }
    }

    public function createActivationToken()
    {
        return $this->activationToken = md5(uniqid().microtime());
    }

    public function validateActivation($token)
    {
        if ($this->activationToken === null) {
            throw new \LogicException('User is already active');
        }

        if ($this->activationToken !== $token) {
            throw new \InvalidArgumentException('Token is not correct');
        }

        $this->activationToken = null;

        return true;
    }

    public function isActive()
    {
        return (null !== $this->password && null === $this->activationToken);
    }

    public function getActivationToken()
    {
        return $this->activationToken;
    }

    /**
     * Adds a new global role to the user.
     *
     * @param Role $globalRole The role to add
     */
    public function addGlobalRole(Role $globalRole)
    {
        $this->globalRoles->add($globalRole);
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

    public function createForgotPasswordToken()
    {
        $token = new UserForgotPassword($this);

        return $token;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        if ($username !== $this->username) {
            $this->markAllKeysAsUninstalled();
        }

        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getSshKeys()
    {
        return $this->sshKeys;
    }

    public function getProjectRoles()
    {
        return $this->projectRoles;
    }

    public function getGlobalRoles()
    {
        return $this->globalRoles;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Searches a user email by text.
     *
     * @param string $email the email to search
     *
     * @return Email
     */
    public function getEmail($text)
    {
        foreach ($this->getEmails() as $email) {
            if ($email->getEmail() === $text) {
                return $email;
            }
        }

        return null;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function eraseCredentials()
    {
    }
}

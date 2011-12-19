<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * Loads the fixtures for user object.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * Service container of the application
     *
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @inheritdoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function load($manager)
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setFullname('Admin');
        $admin->setEmail('admin@example.org');
        $admin->setTimezone('Europe/Paris');
        $this->setPassword($admin, 'admin');
        $admin->addUserRoleGlobal($this->getReference('role-admin'));
        $manager->persist($admin);
        $this->setReference('user-admin', $admin);

        $lead = new User();
        $lead->setUsername('lead');
        $lead->setFullname('Lead');
        $lead->setEmail('lead@example.org');
        $lead->setTimezone('Europe/Paris');
        $this->setPassword($lead, 'lead');
        $manager->persist($lead);
        $this->setReference('user-lead', $lead);

        $alice = new User();
        $alice->setUsername('alice');
        $alice->setFullname('Alice');
        $alice->setEmail('alice@example.org');
        $alice->setTimezone('Europe/Paris');
        $this->setPassword($alice, 'alice');
        $manager->persist($alice);
        $this->setReference('user-alice', $alice);

        $bob = new User();
        $bob->setUsername('bob');
        $bob->setFullname('Bob');
        $bob->setEmail('bob@example.org');
        $bob->setTimezone('Europe/Paris');
        $this->setPassword($bob, 'bob');
        $manager->persist($bob);
        $this->setReference('user-bob', $bob);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 220;
    }

    /**
     * Changes the password for a user: regenerate salt and set password.
     *
     * @param Gitonomy\Bundle\CoreBundle\Entity\User $user A user entity
     * @param string $password The password to set
     */
    protected function setPassword(User $user, $password)
    {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($password, $user->regenerateSalt());
        $user->setPassword($password);
    }
}

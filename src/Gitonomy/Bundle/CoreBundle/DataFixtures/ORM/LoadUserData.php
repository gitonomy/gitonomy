<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * Loads the fixtures for user object.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 * @author Julien DIDIER <julien@jdidier.net>
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
    public function load(ObjectManager $manager)
    {
        $adminRole = $this->getReference('role-admin');

        $users = array();

        $admin = new User('admin', 'Admin', 'Europe/Paris');
        $admin->createEmail('admin@example.org', true);
        $admin->addGlobalRole($adminRole);
        $this->setPassword($admin, 'admin');
        $users[] = $admin;

        $lead = new User('lead', 'Lead', 'Europe/Paris');
        $lead->createEmail('lead@example.org', true);
        $this->setPassword($lead, 'lead');
        $users[] = $lead;

        $charlie = new User('charlie', 'Visitor', 'Europe/Paris');
        $charlie->createEmail('charlie@example.org');
        $this->setPassword($charlie, 'charlie');
        $users[] = $charlie;

        $alice = new User('alice', 'Alice', 'Europe/Paris');
        $alice->createEmail('alice@example.org', true);
        $alice->createEmail('derpina@example.org')->createActivationToken();
        $this->setPassword($alice, 'alice');
        $users[] = $alice;

        $bob = new User('bob', 'Bob', 'Europe/Paris');
        $bob->createEmail('bob@example.org', true);
        $this->setPassword($bob, 'bob');
        $users[] = $bob;

        $inactive = new User('inactive', 'Inactive', 'Europe/Paris');
        $inactive->createEmail('inactive@example.org', true)->createActivationToken();
        $inactive->createActivationToken();
        $users[] = $inactive;

        foreach ($users as $user) {
            $manager->persist($user);
            $this->setReference('user-'.$user->getUsername(), $user);
        }

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 3;
    }

    protected function setPassword(User $user, $password)
    {
        $factory = $this->container->get('security.encoder_factory');
        $user->setPassword($password, $factory->getEncoder($user));
    }
}

<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\Email;

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
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setFullname('Admin');
        $email = new Email();
        $email->setEmail('admin@example.org');
        $admin->setDefaultEmail($email);
        $admin->setTimezone('Europe/Paris');
        $this->setPassword($admin, 'admin');
        $admin->addGlobalRole($this->getReference('role-admin'));
        $manager->persist($admin);
        $this->setReference('user-admin', $admin);

        $lead = new User();
        $lead->setUsername('lead');
        $lead->setFullname('Lead');
        $email = new Email();
        $email->setEmail('lead@example.org');
        $lead->setDefaultEmail($email);
        $lead->setTimezone('Europe/Paris');
        $this->setPassword($lead, 'lead');
        $manager->persist($lead);
        $this->setReference('user-lead', $lead);

        $charlie = new User();
        $charlie->setUsername('charlie');
        $charlie->setFullname('Visitor');
        $email = new Email();
        $email->setEmail('charlie@example.org');
        $charlie->setDefaultEmail($email);
        $charlie->setTimezone('Europe/Paris');
        $this->setPassword($charlie, 'charlie');
        $manager->persist($charlie);
        $this->setReference('user-charlie', $charlie);

        $alice = new User();
        $alice->setUsername('alice');
        $alice->setFullname('Alice');
        $email = new Email();
        $email->setEmail('alice@example.org');
        $alice->setDefaultEmail($email);
        $inactivedEmail = new Email();
        $inactivedEmail->setEmail('derpina@example.org');
        $inactivedEmail->generateActivationHash();
        $alice->addEmail($inactivedEmail);
        $alice->setTimezone('Europe/Paris');
        $this->setPassword($alice, 'alice');
        $manager->persist($alice);
        $this->setReference('user-alice', $alice);

        $bob = new User();
        $bob->setUsername('bob');
        $bob->setFullname('Bob');
        $email = new Email();
        $email->setEmail('bob@example.org');
        $bob->setDefaultEmail($email);
        $bob->setTimezone('Europe/Paris');
        $this->setPassword($bob, 'bob');
        $manager->persist($bob);
        $this->setReference('user-bob', $bob);

        $inactive = new User();
        $inactive->setUsername('inactive');
        $inactive->setFullname('inactive');
        $email = new Email();
        $email->setEmail('inactive@example.org');
        $inactive->setDefaultEmail($email);
        $inactive->setTimezone('Europe/Paris');
        $inactive->generateActivationToken();
        $manager->persist($inactive);
        $this->setReference('user-inactive', $inactive);

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

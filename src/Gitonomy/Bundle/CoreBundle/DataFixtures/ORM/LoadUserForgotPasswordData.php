<?php

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\UserForgotPassword;

/**
 * Loads the fixtures for user forgot password objects.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadUserForgotPasswordData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $aliceForgotPassword = new UserForgotPassword($this->getReference('user-alice'));
        $aliceForgotPassword->setToken('forgottokenalice');
        $aliceForgotPassword->setCreatedAt(new \DateTime());
        $manager->persist($aliceForgotPassword);
        $this->setReference('user-forgot-password-alice', $aliceForgotPassword);

        $bobForgotPassword = new UserForgotPassword($this->getReference('user-bob'));
        $bobForgotPassword->setToken('forgottokenbob');
        $bobForgotPassword->setCreatedAt(new \DateTime('-3 days'));
        $manager->persist($bobForgotPassword);
        $this->setReference('user-forgot-password-bob', $bobForgotPassword);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 230;
    }

}

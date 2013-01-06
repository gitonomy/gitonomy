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

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;

use Gitonomy\Bundle\CoreBundle\DataFixtures\ORM\Fixture;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\UserForgotPassword;

/**
 * Loads the fixtures for user forgot password objects.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class LoadUserForgotPasswordData extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $aliceForgotPassword = new UserForgotPassword($this->getReference('user-alice'), 'forgottokenalice');
        $manager->persist($aliceForgotPassword);

        $bobForgotPassword = new UserForgotPassword($this->getReference('user-bob'), 'forgottokenbob', new \DateTime('-1 months'));
        $manager->persist($bobForgotPassword);

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 4; // user
    }
}

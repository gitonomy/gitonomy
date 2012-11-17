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

namespace Gitonomy\Bundle\CoreBundle\DataFixtures\ORM;

use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
abstract class UserFixture extends Fixture
{
    protected function setPassword(User $user, $password)
    {
        $factory = $this->container->get('security.encoder_factory');
        $user->setPassword($password, $factory->getEncoder($user));
    }
}

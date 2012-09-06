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

namespace Gitonomy\Bundle\CoreBundle\Tests\Entity;

use Gitonomy\Bundle\CoreBundle\Entity\Email;

class EmailTest extends \PHPUnit_Framework_TestCase
{
    const USER_CLASS = 'Gitonomy\Bundle\CoreBundle\Entity\User';

    public function testInstanciation()
    {
        $email = new Email($this->getMock(self::USER_CLASS));

        $this->assertFalse($email->isDefault(), "E-mail is not default on creation");
    }

    public function testActivation_ValidToken_ActivateMail()
    {
        $email = new Email($this->getMock(self::USER_CLASS));

        $activation = $email->createActivationToken();
        $this->assertTrue($email->validateActivationToken($activation), "Activation method returns true with a valid token");
        $this->assertTrue($email->isActive(), "Mail is active after successful activation");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testActivaction_InvalidToken_ThrowsException()
    {
        $email = new Email($this->getMock(self::USER_CLASS));

        $email->createActivationToken();
        $email->validateActivationToken('foo');
    }

    /**
     * @expectedException LogicException
     */
    public function testActivaction_AlreadyActive_ThrowsException()
    {
        $email = new Email($this->getMock(self::USER_CLASS));

        $email->validateActivationToken('foo');
    }
}

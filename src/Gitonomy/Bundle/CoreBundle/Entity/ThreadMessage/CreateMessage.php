<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

class CreateMessage extends ThreadMessage
{
    public function getSentence()
    {
        return 'created this thread';
    }

    public function getName()
    {
        return 'create';
    }
}

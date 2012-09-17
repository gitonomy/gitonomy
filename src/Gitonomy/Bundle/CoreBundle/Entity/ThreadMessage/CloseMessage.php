<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

class CloseMessage extends ThreadMessage
{
    public function getSentence()
    {
        return 'closed this thread';
    }

    public function getName()
    {
        return 'close';
    }
}

<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

class OpenMessage extends ThreadMessage
{
    public function getName()
    {
        return 'open';
    }
}

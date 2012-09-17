<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

class MergeMessage extends ThreadMessage
{
    public function getSentence()
    {
        return 'merged';
    }

    public function getName()
    {
        return 'merge';
    }
}

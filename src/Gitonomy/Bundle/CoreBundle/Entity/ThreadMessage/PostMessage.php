<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

use Gitonomy\Bundle\CoreBundle\Entity\ThreadMessage;

class PostMessage extends ThreadMessage
{
    protected $message;

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getName()
    {
        return 'post';
    }
}

<?php

namespace Gitonomy\Bundle\FrontendBundle\Mail;

use Gitonomy\Bundle\CoreBundle\Entity;

class MailSender
{
    protected $mailer;
    protected $mailFrom;
    protected $mailTo;

    public function __construct(Mailer $mailer, array $parameters)
    {
        $this->mailer   = $mailer;
        $this->mailFrom = $parameters['from'];
        $this->mailTo   = $parameters['to'];
    }

    protected function getFrom($from = null)
    {
        if(null !== $from) {
            return $from;
        } else {
            return $this->mailFrom;
        }
    }

    protected function getTo($to = null)
    {
        if (null !== $to) {
            return $to;
        } else {
            return $this->mailTo;
        }
    }
}

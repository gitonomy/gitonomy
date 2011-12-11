<?php

namespace Gitonomy\Bundle\FrontendBundle\Mail;

/**
 * Controller for repositories actions.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class Mailer
{
    protected $mailer;
    protected $mailGenerator;

    public function __construct(\Swift_Mailer $mailer, TwigMailGenerator $mailGenerator)
    {
        $this->mailer        = $mailer;
        $this->mailGenerator = $mailGenerator;
    }

    public function renderMessage($template, $parameters = array())
    {
        $this->mailGenerator->getTemplate($template);

        return \Swift_Message::newInstance()
            ->setSubject($this->mailGenerator->renderBlock('subject', $parameters))
            ->setBody($this->mailGenerator->renderBlock('body_text', $parameters), 'text/plain')
            ->addPart($this->mailGenerator->renderBlock('body_html', $parameters), 'text/html')
        ;
    }

    public function send(\Swift_Message $message, $from = null, $to = null)
    {
        $this->setFrom($message, $from);
        $this->setTo($message, $to);

        $this->mailer->send($message);
    }

    protected function setFrom(\Swift_Message $message, $from)
    {
        if (is_array($from)) {
            list($email, $name) = $from;
            $message->setFrom($email, $name);
            $message->setReplyTo($email, $name);
        } else {
            $message->setFrom($from);
        }
    }

    protected function setTo(\Swift_Message $message, $to)
    {
        if (is_array($to)) {
            list($email, $name) = $to;
            $message->setTo($email, $name);
        } else {
            $message->setTo($to);
        }
    }
}

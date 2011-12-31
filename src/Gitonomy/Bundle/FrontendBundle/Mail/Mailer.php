<?php

namespace Gitonomy\Bundle\FrontendBundle\Mail;

use Symfony\Component\Templating\EngineInterface;

/**
 * Service to send emails
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class Mailer
{
    protected $mailer;
    protected $templateEngine;
    protected $mailFrom;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templateEngine, array $from)
    {
        $this->mailer         = $mailer;
        $this->templateEngine = $templateEngine;
        $this->mailFrom       = $from;
    }

    public function sendMessage($template, $context = array(), $to)
    {
        $template = $this->templateEngine->loadTemplate($template);

        $subject  = $this->renderTwigBlock($template, 'subject',   $context);
        $bodyText = $this->renderTwigBlock($template, 'body_text', $context);
        $bodyHtml = $this->renderTwigBlock($template, 'body_html', $context);

        return \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setBody($bodyText, 'text/plain')
            ->addPart($bodyHtml, 'text/html')
            ->setFrom($this->mailFrom)
            ->setTo($to)
        ;

        $this->mailer->send($message);
    }

    protected function renderTwigBlock(\Twig_Template $template, $blockName, $context = array())
    {
        foreach ($template->getEnvironment()->getGlobals() as $key => $value) {
            if (!array_key_exists($key, $context)) {
                $context[$key] = $value;
            }
        }

        return $template->renderBlock($blockName, $context);
    }
}

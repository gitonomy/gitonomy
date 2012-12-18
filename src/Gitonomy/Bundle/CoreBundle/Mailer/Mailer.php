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

namespace Gitonomy\Bundle\CoreBundle\Mailer;

use Symfony\Component\Templating\EngineInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Email;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Component\Config\ConfigInterface;

/**
 * Service to send emails
 *
 * @author Julien DIDIER <julien@jdidier.net>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Mailer
{
    protected $config;
    protected $templateEngine;

    public function __construct(ConfigInterface $config, EngineInterface $templateEngine)
    {
        $this->config         = $config;
        $this->templateEngine = $templateEngine;
    }

    public function mail($to, $template, $context = array())
    {
        if ($to instanceof User) {
            if (!$to->hasDefaultEmail()) {
                throw new \RuntimeException('Can\'t send a mail to user '.$to->getUsername().': no mail');
            }

            $to = array($to->getDefaultEmail()->getEmail() => $to->getFullname());
        } elseif ($to instanceof Email) {
            $to = array($to->getEmail() => $to->getUser()->getFullname());
        } else {
            throw new \RuntimeException('Unexpected type of recipient: '.gettype($to));
        }

        $template = $this->templateEngine->loadTemplate($template);

        $subject  = $this->renderTwigBlock($template, 'subject',   $context);
        $bodyText = $this->renderTwigBlock($template, 'body_text', $context);
        $bodyHtml = $this->renderTwigBlock($template, 'body_html', $context);

        $fromEmail = $this->config->get('mailer_from_email');
        $fromName  = $this->config->get('mailer_from_name');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setBody($bodyText, 'text/plain')
            ->addPart($bodyHtml, 'text/html')
            ->setFrom(array($fromEmail => $fromName))
            ->setTo($to)
        ;

        $swiftmailer = SwiftmailerFactory::createFromConfig($this->config);

        $swiftmailer->send($message);
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

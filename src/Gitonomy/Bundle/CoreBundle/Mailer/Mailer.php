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

use Symfony\Component\DependencyInjection\ContainerInterface;

use Gitonomy\Bundle\CoreBundle\Entity\Email;
use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * Service to send emails
 *
 * @author Julien DIDIER <julien@jdidier.net>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Mailer
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function mail($to, $template, $context = array())
    {
        $config     = $this->container->get('gitonomy_core.config');
        $templating = $this->container->get('templating');

        // Transform $to in an array(email => name)
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

        $template  = $templating->loadTemplate($template);
        $subject   = $this->renderTwigBlock($template, 'subject',   $context);
        $bodyText  = $this->renderTwigBlock($template, 'body_text', $context);
        $bodyHtml  = $this->renderTwigBlock($template, 'body_html', $context);
        $fromEmail = $config->get('mailer_from_email');
        $fromName  = $config->get('mailer_from_name');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setBody($bodyText, 'text/plain')
            ->addPart($bodyHtml, 'text/html')
            ->setFrom(array($fromEmail => $fromName))
            ->setTo($to)
        ;

        $swiftmailer = SwiftmailerFactory::createFromConfig($config);

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

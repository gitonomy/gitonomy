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

use Gitonomy\Component\Config\ConfigInterface;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class SwiftMailerFactory
{
    public static function createFromConfig(ConfigInterface $config)
    {
        $transport  = $config->get('mailer_transport');
        $host       = $config->get('mailer_host');
        $port       = $config->get('mailer_port'); // 25 or 465 (smtp)
        $username   = $config->get('mailer_username');
        $password   = $config->get('mailer_password');
        $authMode   = $config->get('mailer_auth_mode');
        $encryption = $config->get('mailer_encryption');

        if ($transport == 'gmail') {
            $transport  = 'smtp';
            $host       = 'smtp.gmail.com';
            $authMode   = 'login';
            $encryption = 'ssl';
            $port       = 465;
        }

        $port = $port ? $port : 25;

        if ($transport == 'smtp') {
            $transport = \Swift_SmtpTransport::newInstance($host, $port)
                ->setUsername($username)
                ->setPassword($password)
            ;
        } elseif ($transport == 'mail') {
            $transport = \Swift_MailTransport::newInstance();
        } elseif ($transport == 'null') {
            $transport = \Swift_NullTransport::newInstance();
        } else {
            throw new \RuntimeException(sprintf('Unable to construct a transport of type "%s"', $transport));
        }

        return \Swift_Mailer::newInstance($transport);
    }
}

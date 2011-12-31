<?php

namespace Gitonomy\Bundle\FrontendBundle\Model\Security;

use Symfony\Bundle\DoctrineBundle\Registry;
use Gitonomy\Bundle\FrontendBundle\Mail\Mailer;

class ForgotPasswordHandler
{
    /**
     * @var Symfony\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @var Gitonomy\Bundle\FrontendBundle\Mail\Mailer
     */
    protected $mailer;

    public function __construct(Registry $doctrine, Mailer $mailer)
    {
        $this->doctrine = $doctrine;
        $this->mailer   = $mailer;
    }

    public function processRequest(ForgotPasswordRequest $request)
    {
        $user = $this->doctrine
            ->getRepository('GitonomyCoreBundle:User')
            ->findOneByEmail($request->getEmail())
        ;

        $email = $user->getDefaultEmail()->getEmail();
        $name = $user->getFullname();

        $this->mailer->sendMessage('GitonomyFrontendBundle:Security:forgotPassword.mail.twig', array(
                'user' => $user
            ), array(
                $email => $name
            )
        );
    }
}

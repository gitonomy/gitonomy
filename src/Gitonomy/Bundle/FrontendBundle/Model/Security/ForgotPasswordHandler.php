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
        $em = $this->doctrine->getManager();

        $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByEmail($request->getEmail());

        $email = $user->getDefaultEmail()->getEmail();
        $name = $user->getFullname();

        $user->createForgotPasswordToken();

        $em->persist($user);
        $em->flush();

        $this->mailer->sendMessage('GitonomyFrontendBundle:Security:forgotPassword.mail.twig', array(
                'user' => $user
            ), array(
                $email => $name
            )
        );
    }

    public function getUserIfForgotPasswordTokenValid($username, $forgotPasswordToken)
    {
        $user = $this->doctrine->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('The user with username "%s" was not found', $username));
        }

        if ($user->getForgotPasswordToken() !== $forgotPasswordToken) {
            throw new \InvalidArgumentException('The token is not correct');
        }

        if ($user->isForgotPasswordTokenExpired()) {
            throw new \InvalidArgumentException('The forgot password token has expired');
        }

        return $user;
    }
}

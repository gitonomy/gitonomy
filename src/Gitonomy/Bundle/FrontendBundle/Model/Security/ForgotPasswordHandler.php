<?php

namespace Gitonomy\Bundle\FrontendBundle\Model\Security;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Gitonomy\Bundle\FrontendBundle\Mail\Mailer;
use Gitonomy\Bundle\CoreBundle\Entity\User;

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

        $token = $em->getRepository('GitonomyCoreBundle:UserForgotPassword')->getToken($user);

        $em->persist($token);
        $em->flush();

        $this->mailer->sendMessage('GitonomyFrontendBundle:Security:forgotPassword.mail.twig', array(
                'user'  => $user,
                'token' => $token
            ), array(
                $email => $name
            )
        );
    }

    public function validate(User $user, $token)
    {
        $em = $this->doctrine->getManager();
        $forgotPassword = $em->getRepository('GitonomyCoreBundle:UserForgotPassword')->getToken($user);

        if (!$forgotPassword) {
            throw new \InvalidArgumentException('No token found');
        }

        if ($forgotPassword->isTokenExpired()) {
            throw new \InvalidArgumentException('The forgot password token has expired');
        }

        $forgotPassword->validateToken($token);

        return $user;
    }

    public function removeForgotPasswordToken(User $user)
    {
        $em = $this->doctrine->getManager();
        $forgotPassword = $em->getRepository('GitonomyCoreBundle:UserForgotPassword')->getToken($user);
        $em->remove($forgotPassword);
        $em->flush();
    }
}

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

        $token = $user->createForgotPasswordToken();

        $em->persist($user);
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
        $forgotPassword = $this->doctrine->getRepository('GitonomyCoreBundle:UserForgotPassword')->findOneBy(array(
            'user' => $user
        ));

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
        $this->doctrine->getEntityManager()->remove($user->getForgotPassword());
    }
}

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
        if (!$user = $this->doctrine->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username)) {
            throw new \InvalidArgumentException(sprintf('The user with username "%s" was not found', $username));
        }

        $userForgotPassword = $user->getForgotPassword();

        if ($userForgotPassword->getToken() !== $forgotPasswordToken) {
            throw new \InvalidArgumentException('The token is not correct');
        }

        if ($userForgotPassword->isTokenExpired()) {
            throw new \InvalidArgumentException('The forgot password token has expired');
        }

        return $user;
    }

    public function removeForgotPasswordToken(User $user)
    {
        $this->doctrine->getEntityManager()->remove($user->getForgotPassword());
    }
}

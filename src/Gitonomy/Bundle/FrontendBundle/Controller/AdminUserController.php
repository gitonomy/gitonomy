<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;
use Gitonomy\Bundle\CoreBundle\Entity\Email;
use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * Controller for user actions.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */

class AdminUserController extends BaseAdminController
{
    public function getMessage($object, $type)
    {
        if ($type == self::MESSAGE_TYPE_CREATE) {
            return sprintf('User "%s" is created', $object->getUsername());
        } elseif ($type == self::MESSAGE_TYPE_UPDATE) {
            return sprintf('User "%s" is updated', $object->getUsername());
        } elseif ($type == self::MESSAGE_TYPE_DELETE) {
            return sprintf('User "%s" is deleted', $object->getUsername());
        }

        throw new \InvalidArgumentException('Unknown type '.$type);
    }

    public function listAction()
    {
        $this->assertGranted('ROLE_USER_LIST');

        return parent::listAction();
    }

    public function createAction()
    {
        $this->assertGranted('ROLE_USER_CREATE');

        return parent::createAction();
    }

    public function editAction($id)
    {
        $this->assertGranted('ROLE_USER_EDIT');

        return parent::editAction($id);
    }

    public function activateAction($id)
    {
        $this->assertGranted('ROLE_USER_ACTIVATE');

        $user = $this->findUser($id);

        if (!$user->hasDefaultEmail()) {
            throw new \LogicException(sprintf('User #%d has no default email.', $id));
        }

        if ($user->isActive()) {
            throw new \LogicException(sprintf('User #%d already actived!', $id));
        }

        $em         = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();

        try {
            $em->beginTransaction();
            $token = $user->createActivationToken();
            $this->get('gitonomy_frontend.mailer')->sendMessage('GitonomyFrontendBundle:AdminUser:activate.mail.twig', array(
                'user' => $user,
                'token' => $token
            ), $user->getDefaultEmail()->getEmail());

            $em->flush();
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            $em->close();
            throw $e;
        }
        $this->get('session')->setFlash('success', sprintf('Activation mail for user "%s" sent.', $user->getFullname()));

        return $this->redirect($this->generateUrl($this->getRouteName('edit'), array(
            'id' => $user->getId()
        )));
    }

    public function emailSendActivationAction($id, $emailId)
    {
        $this->assertGranted('ROLE_USER_EDIT');

        $email = $this->findEmail($id, $emailId);
        $email->createActivationToken();
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($email);
        $em->flush();
        $this->sendActivationMail($email);

        $message = sprintf('Activation mail for email "%s" sent.', $email->getEmail());
        $this->get('session')->setFlash('success', $message);

        return $this->redirect($this->generateUrl($this->getRouteName('email_list'), array(
            'id' => $email->getUser()->getId()
        )));
    }

    /**
     * Action to create a new user role project for an user
     */
    public function userRolesAction($userId)
    {
        $this->assertGranted('ROLE_USER_EDIT');

        $user            = $this->findUser($userId);
        $userRoleProject = new UserRoleProject();
        $em              = $this->getDoctrine()->getManager();
        $repository      = $em->getRepository('GitonomyCoreBundle:Project');
        $usedProjects    = $repository->findByUser($user);
        $totalProjects   = $repository->findAll();
        $request         = $this->getRequest();

        $form = $this->createForm('adminuserroleproject', $userRoleProject, array(
            'usedProjects' => $usedProjects,
            'from'         => 'user',
        ));

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $userRoleProject->setUser($user);
                $em->persist($userRoleProject);
                $em->flush();

                $this->get('session')->setFlash('success', sprintf(
                    'Created role "%s" for "%s" on project "%s" ',
                    $userRoleProject->getRole()->getName(),
                    $userRoleProject->getUser()->getFullname(),
                    $userRoleProject->getProject()->getName()
                ));

                return $this->redirect($this->generateUrl($this->getRouteName('projectroles'), array(
                    'userId' => $user->getId()
                )));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminUser:projectroles.html.twig', array(
            'object'      => $user,
            'form'        => $form->createView(),
            'displayForm' => $totalProjects > $usedProjects,
        ));
    }

    public function deleteUserRoleAction($id)
    {
        $this->assertGranted('ROLE_USER_PROJECT_DELETE');

        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:UserRoleProject');

        if (!$userRole = $repository->find($id)) {
            throw new HttpException(404, sprintf('No UserRole found with id "%d".', $id));
        }

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->remove($userRole);
                $em->flush();

                $this->get('session')->setFlash('success',
                    sprintf(
                        'Removed user "%s" from project "%s".',
                        $userRole->getUser()->getUsername(),
                        $userRole->getProject()->getName()
                ));

                return $this->redirect($this->generateUrl($this->getRouteName('projectroles'), array(
                    'userId' => $userRole->getUser()->getId()
                )));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminRole:deleteUserRole.html.twig', array(
            'object' => $userRole,
            'form'   => $form->createView(),
        ));
    }

    public function deleteAction($id)
    {
        $this->assertGranted('ROLE_USER_DELETE');

        return parent::deleteAction($id);
    }

    /**
     * Action to create an email from admin user
     */
    public function emailsAction($id)
    {
        $this->assertGranted('ROLE_USER_EMAIL_LIST');

        $user    = $this->findUser($id);
        $email   = new Email($user);
        $request = $this->getRequest();
        $form    = $this->createForm('useremail', $email, array(
            'validation_groups' => 'admin',
        ));

        if ('POST' == $request->getMethod()) {
            $this->assertGranted('ROLE_USER_EMAIL_CREATE');
            $form->bindRequest($request);
            if ($form->isValid()) {
                $this->saveEmail($email);
                $message = sprintf('Email "%s" added.', $email->getEmail());

                return $this->successAndRedirect($message, 'gitonomyfrontend_adminuser_email_list', array('id' => $user->getId()));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminUser:emails.html.twig', array(
            'object' => $user,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Action to make as default an email from admin user
     */
    public function emailDefaultAction($id, $emailId)
    {
        $this->assertGranted('ROLE_USER_EMAIL_SET_DEFAULT');

        $defaultEmail = $this->findEmail($id, $emailId);
        $em           = $this->getDoctrine()->getEntityManager();
        $user         = $defaultEmail->getUser();

        $user->setDefaultEmail($defaultEmail);

        $em->persist($user);
        $em->flush();
        $message = sprintf('Email "%s" now as default.', $defaultEmail->getEmail());

        return $this->successAndRedirect($message, 'gitonomyfrontend_adminuser_email_list', array('id' => $user->getId()));
    }

    /**
     * Action to delete an email for a user from admin user
     */
    public function emailDeleteAction($id, $emailId)
    {
        $this->assertGranted('ROLE_USER_EMAIL_DELETE');

        $email   = $this->findEmail($id, $emailId);
        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($email);
                $em->flush();
                $message = sprintf('Email "%s" deleted.', $email->getEmail());

                return $this->successAndRedirect($message, 'gitonomyfrontend_adminuser_email_list', array('id' => $email->getUser()->getId()));
            }
        }

        return $this->render('GitonomyFrontendBundle:AdminUser:deleteEmail.html.twig', array(
            'object' => $email,
            'form'   => $form->createView(),
        ));
    }

    protected function findUser($id)
    {
        if (!$user = $this->getRepository()->find($id)) {
            throw new HttpException(404, sprintf('No %s found with id "%d".', $className, $id));
        }

        return $user;
    }

    protected function findEmail($userId, $emailId)
    {
        $user = $this->findUser($userId);
        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('GitonomyCoreBundle:Email');
        if (!$email = $repo->findOneBy(array('user' => $user, 'id' => $emailId))) {
            throw new HttpException(404, sprintf('No Email found with id "%d".', $emailId));
        }

        return $email;
    }

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('GitonomyCoreBundle:User');
    }

    protected function saveEmail(Email $email)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($email);
        $em->flush();
    }

    protected function sendActivationMail(Email $email)
    {
        $this->get('gitonomy_frontend.mailer')->sendMessage(
            'GitonomyFrontendBundle:Email:activateEmail.mail.twig',
            array(
                'email' => $email
            ),
            $email->getEmail()
        );
    }
}

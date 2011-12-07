<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\FrontendBundle\Form\RoleType;

/**
 * Controller for repository actions.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */

class RoleController extends BaseController
{
    public function listAction()
    {
        $em         = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('GitonomyCoreBundle:Role');
        $roles      = $repository->findAll();

        return $this->render('GitonomyFrontendBundle:Role:list.html.twig', array(
           'roles' => $roles,
        ));
    }

    public function createAction()
    {
        $role    = new Role();
        $form    = $this->createForm(new RoleType(), $role);
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($role);
                $em->flush();

                $this->get('session')->setFlash('success', 'Role saved');

                return $this->redirect($this->generateUrl('gitonomyfrontend_role_list'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Role:create.html.twig', array(
           'form' => $form->createView(),
        ));
    }

    public function editAction($id)
    {
        $em        = $this->getDoctrine()->getEntityManager();
        if (!$role = $em->getRepository('GitonomyCoreBundle:Role')->find($id)) {
            throw new HttpException(404, sprintf('No role found with id "%d".', $id));
        }
        $form      = $this->createForm(new RoleType(), $role);
        $request   = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->flush();

                $this->get('session')->setFlash('success', sprintf('Role "%s" updated.', $role->getName()));

                return $this->redirect($this->generateUrl('gitonomyfrontend_role_list'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Role:edit.html.twig', array(
            'role' => $role,
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($id)
    {
        $em        = $this->getDoctrine()->getEntityManager();
        if (!$role = $em->getRepository('GitonomyCoreBundle:Role')->find($id)) {
            throw new HttpException(404, sprintf('No role found with id "%d".', $id));
        }
        $form      = $this->createFormBuilder()->getForm();
        $request   = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em->remove($role);
                $em->flush();

                $this->get('session')->setFlash('success', sprintf('Role "%s" deleted.', $role->getName()));

                return $this->redirect($this->generateUrl('gitonomyfrontend_role_list'));
            }
        }

        return $this->render('GitonomyFrontendBundle:Role:delete.html.twig', array(
            'role' => $role,
            'form' => $form->createView(),
        ));
    }
}

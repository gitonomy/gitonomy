<?php

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\FrontendBundle\Form\Role\RoleType;

/**
 * Controller for repositories actions.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */

abstract class BaseAdminController extends BaseController
{
    public function listAction()
    {
        $className  = $this->getRepository()->getClassName();
        $repository = $this->getRepository();
        $objects    = $repository->findAll();

        return $this->render($this->getViewName($className, 'list'), array(
           'objects' => $objects,
        ));
    }

    public function createAction()
    {
        $className = $this->getRepository()->getClassName();
        $object    = new $className();
        $form      = $this->createForm(new RoleType(), $object);
        $request   = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($object);
                $em->flush();

                $this->get('session')->setFlash('success', sprintf('%s saved', $className));

                return $this->redirect($this->generateUrl($this->getRouteName('list')));
            }
        }

        return $this->render($this->getViewName($className, 'create'), array(
           'form' => $form->createView(),
        ));
    }

    public function editAction($id)
    {
        $className = $this->getRepository()->getClassName();

        if (!$object = $this->getRepository()->find($id)) {
            throw new HttpException(404, sprintf('No role found with id "%d".', $id));
        }

        $form    = $this->createForm(new RoleType(), $object);
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->flush();

                $this->get('session')->setFlash('success', sprintf('%s "%s" updated.', $className, $object->getName()));

                return $this->redirect($this->generateUrl($this->getRouteName('list')));
            }
        }

        return $this->render($this->getViewName($className, 'edit'), array(
            'object' => $object,
            'form'   => $form->createView(),
        ));
    }

    public function deleteAction($id)
    {
        $className = $this->getRepository()->getClassName();

        if (!$object = $this->getRepository()->find($id)) {
            throw new HttpException(404, sprintf('No %s found with id "%d".', $className, $id));
        }

        $form    = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($object);
                $em->flush();

                $this->get('session')->setFlash('success', sprintf('%s "%s" deleted.', $className, $object->getName()));

                return $this->redirect($this->generateUrl($this->getRouteName('list')));
            }
        }

        return $this->render($this->getViewName($className, 'delete'), array(
            'object' => $object,
            'form'   => $form->createView(),
        ));
    }

    protected function getRouteName($route)
    {
        $route = strtolower($this->getIdentifier($route));

        return 'gitonomyfrontend_'.$route.'_list';
    }

    protected function getViewName($className, $view)
    {
        $className = $this->getIdentifier($className);

        return 'GitonomyFrontendBundle:'.$className.':'.$view.'.html.twig';
    }

    protected function getIdentifier($string)
    {
        $string = get_class($this);
        $string = substr($string, strrpos($string, '\\') + 1);
        $string = preg_replace('/Controller$/', '', $string);

        return $string;
    }

    abstract protected function getRepository();
}

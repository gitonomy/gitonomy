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

namespace Gitonomy\Bundle\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Controller for admin actions.
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
abstract class BaseAdminController extends BaseController
{
    const MESSAGE_TYPE_CREATE = 'create';
    const MESSAGE_TYPE_UPDATE = 'update';
    const MESSAGE_TYPE_DELETE = 'delete';

    abstract public function getMessage($object, $type);

    public function listAction()
    {
        $repository = $this->getRepository();
        $className  = $repository->getClassName();
        $objects    = $repository->findAll();

        return $this->renderView('list', array(
           'objects' => $objects,
        ));
    }

    public function createAction()
    {
        $className = $this->getRepository()->getClassName();
        $object    = new $className();
        $form      = $this->createAdminForm($object, array('action' => 'create'));
        $request   = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $em->persist($object);
                $em->flush();
                $this->postCreate($object);

                $this->get('session')->setFlash('success', $this->getMessage($object, self::MESSAGE_TYPE_CREATE));

                return $this->redirect($this->generateUrl($this->getRouteName('edit'), array('id' => $object->getId())));
            }
        }

        return $this->renderView('create', array(
           'form' => $form->createView(),
        ));
    }

    public function editAction($id)
    {
        $className = $this->getRepository()->getClassName();

        if (!$object = $this->getRepository()->find($id)) {
            throw new HttpException(404, sprintf('No %s found with id "%d".', $className, $id));
        }

        $form    = $this->createAdminForm($object, array('action' => 'edit'));
        $request = $this->getRequest();

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $this->postEdit($object);
                $em->flush();

                $this->get('session')->setFlash('success', $this->getMessage($object, self::MESSAGE_TYPE_UPDATE));

                return $this->redirect($this->generateUrl($this->getRouteName('edit'), array('id' => $object->getId())));
            }
        }

        return $this->renderView('edit', array(
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

                $this->preDelete($object);
                $em->remove($object);
                $em->flush();
                $this->postDelete($object);

                $this->get('session')->setFlash('success', $this->getMessage($object, self::MESSAGE_TYPE_DELETE));

                return $this->redirect($this->generateUrl($this->getRouteName('list')));
            }
        }

        return $this->renderView('delete', array(
            'object' => $object,
            'form'   => $form->createView(),
        ));
    }

    protected function getFormType($className)
    {
        $className = strtolower($this->getIdentifier($className));

        return 'admin'.$className;
    }

    protected function getRouteName($routeSufix)
    {
        $className = get_class($this);
        $route     = $this->getIdentifier($className);
        $route     = strtolower(preg_replace('/Controller$/', '', $route));

        return 'gitonomyfrontend_'.$route.'_'.$routeSufix;
    }

    protected function getViewName($className, $view)
    {
        $className = $this->getIdentifier($className);

        return 'GitonomyFrontendBundle:Admin'.$className.':'.$view.'.html.twig';
    }

    protected function getIdentifier($className)
    {
        return substr($className, strrpos($className, '\\') + 1);
    }

    public function renderView($view, array $parameters = array(), Response $response = null)
    {
        $className     = $this->getRepository()->getClassName();
        $objectType    = $this->getIdentifier($className);
        $templating    = $this->container->get('templating');
        $viewDirectory = 'Admin'.$objectType;

        $parameters = array_merge(
            array(
                'object_type'       => strtolower($this->getIdentifier($className)),
                'route_prefix'      => 'gitonomyfrontend_admin'.strtolower($objectType),
                'controller_prefix' => 'GitonomyFrontendBundle:Admin'.$this->getIdentifier($className),
            ),
            $parameters
        );

        $template = new TemplateReference('GitonomyFrontendBundle', $viewDirectory, $view, 'html', 'twig');
        if ($templating->exists($template)) {
            $view = $template->getLogicalName();
        } else {
            $view = 'GitonomyFrontendBundle:BaseAdmin:'.$view.'.html.twig';
        }

        return parent::render($view, $parameters, $response);
    }

    protected function postCreate($object)
    {
    }

    protected function postEdit($object)
    {
    }

    protected function preDelete($object)
    {
    }

    protected function postDelete($object)
    {
    }

    abstract protected function getRepository();

    protected function fail($message)
    {
        $this->get('session')->setFlash('warning', $message);
    }

    protected function successAndRedirect($message, $route, array $parameters = null)
    {
        $this->get('session')->setFlash('success', $message);
        $parameters = (is_array($parameters) ? $parameters : array());

        return $this->redirect($this->generateUrl($route, $parameters));
    }

    protected function createAdminForm($object, $options = array())
    {
        $className = $this->getRepository()->getClassName();

        return $this->createForm($this->getFormType($className), $object, $options);
    }
}

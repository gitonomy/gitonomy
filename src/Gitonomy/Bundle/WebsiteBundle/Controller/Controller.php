<?php

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class Controller extends BaseController
{
    protected function mail($to, $template, $context)
    {
        $this->get('gitonomy.mailer')->mail($to, $template, $context);
    }

    protected function setFlash($name, $value)
    {
        $this->get('session')->setFlash($name, $value);
    }

    protected function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->get('translator')->trans($id, $parameters, $domain, $locale);
    }

    protected function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->get('translator')->transChoice($id, $number, $parameters, $domain, $locale);
    }

    protected function isAuthenticated()
    {
        return $this->get('security.context')->getToken()->getUser() instanceof User;
    }

    protected function assertGranted($attributes, $object = null)
    {
        if (!$this->get('security.context')->isGranted($attributes, $object)) {
            throw $this->createAccessDeniedException();
        }
    }

    protected function getRepository($name)
    {
        return $this->get('doctrine')->getEntityManager()->getRepository($name);
    }

    /**
     * @return Repository
     */
    protected function getGitRepository(Project $project)
    {
        return $this
            ->get('gitonomy_core.git.repository_pool')
            ->getGitRepository($project)
        ;
    }

    protected function persistEntity($entity)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    protected function createAccessDeniedException($message = null)
    {
        return new AccessDeniedException($message);
    }
}

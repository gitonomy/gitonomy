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

namespace Gitonomy\Bundle\WebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;
use Gitonomy\Bundle\CoreBundle\Entity\Email;

class UserController extends Controller
{
    public function showAction($username)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->findByUsername($username);

        if ($this->get('security.context')->isGranted('ROLE_PROJECT_READ_ALL')) {
            $projects = $this->getRepository('GitonomyCoreBundle:Project')->findByUser($user);
        } else {
            $projects = $this->getRepository('GitonomyCoreBundle:Project')->findByUsers(array($user, $this->getUser()));
        }

        $newsfeed = $this->getRepository('GitonomyCoreBundle:Message')->findByUser($user, $projects);

        return $this->render('GitonomyWebsiteBundle:User:show.html.twig', array(
            'user'     => $user,
            'projects' => $projects,
            'newsfeed' => $newsfeed,
        ));
    }

    protected function findByUsername($username)
    {
        $repo = $this->getRepository('GitonomyCoreBundle:User');
        if (!$user = $repo->findOneBy(array('username' => $username))) {
            throw $this->createNotFoundException(sprintf('No User found with username "%s".', $username));
        }

        return $user;
    }
}

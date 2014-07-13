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
    public function showAction(Request $request, $username)
    {
        $this->assertGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->findByUsername($username);
        $page = $request->query->get('page', 1);

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $projects = $this->getRepository('GitonomyCoreBundle:Project')->findByUser($user);
        } else {
            $projects = $this->getRepository('GitonomyCoreBundle:Project')->findByUsers(array($user, $this->getUser()));
        }

        $newsfeed = $this->getRepository('GitonomyCoreBundle:Message')->getPagerForUser($user, $projects)->setPage($page);

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

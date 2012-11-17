<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

use Gitonomy\QA\KernelFactory;
use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;
use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;

class ApiContext extends BehatContext
{
    protected $kernelFactory;

    public function setKernelFactory(KernelFactory $kernelFactory)
    {
        $this->kernelFactory = $kernelFactory;
    }

    protected function getKernelFactory()
    {
        if (null === $this->kernelFactory) {
            throw new \RuntimeException('Context has no kernel to process your request');
        }

        return $this->kernelFactory;
    }

    /**
     * @Given /^administration has reinitialized parameters$/
     */
    public function administrationHasReinitializedParameters()
    {
        $this->getKernelFactory()->changeParameters(array());
    }

    /**
     * @Given /^administrator has enabled registration$/
     */
    public function administratorHasEnabledRegistration()
    {
        $params = $this->getKernelFactory()->getParameters();

        if (isset($params['open_registration']) && !$params['open_registration']) {
            $params['open_registration'] = true;
            $this->getKernelFactory()->changeParameters($params);
        }
    }

    /**
     * @Given /^user "([^"]*)" does not exist$/
     */
    public function userDoesNotExist($username)
    {
        $this->kernelFactory->run(function ($kernel) use ($username) {
            $em = $kernel->getContainer()->get('doctrine')->getEntityManager();
            $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

            if (!$user) {
                return;
            }

            $em->remove($user);
            $em->flush();
        });
    }

    /**
     * @Given /^project "([^"]*)" does not exist$/
     */
    public function projectDoesNotExist($slug)
    {
        $this->kernelFactory->run(function ($kernel) use ($slug) {
            $em      = $kernel->getContainer()->get('doctrine')->getEntityManager();
            $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($slug);

            if (!$project) {
                return;
            }

            $em->remove($project);
            $em->flush();
            $kernel->getContainer()->get('gitonomy_core.event_dispatcher')->dispatch(GitonomyEvents::PROJECT_DELETE, new ProjectEvent($project));
        });
    }

    /**
     * @Given /^user "([^"]*)" exists$/
     */
    public function userExists($username)
    {
        $this->kernelFactory->run(function ($kernel) use ($username) {
            $em = $kernel->getContainer()->get('doctrine')->getEntityManager();
            $factory = $kernel->getContainer()->get('security.encoder_factory');
            $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
            if ($user) {
                $em->remove($user);
                $em->flush();
            }

            $user = new User($username, ucfirst($username));
            $user->setPassword($username, $factory->getEncoder($user));
            $user->createEmail($username.'@example.org', true);

            $em->persist($user);
            $em->flush();
        });
    }

    /**
     * @Given /^user "([^"]*)" has SSH key named "([^"]*)", content "(.*)"$/
     */
    public function userHasSshKeyNamedKeyAContent($username, $title, $content)
    {
        $this->kernelFactory->run(function ($kernel) use ($username, $title, $content) {
            $em = $kernel->getContainer()->get('doctrine')->getEntityManager();

            $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
            $key = null;
            foreach ($user->getSshKeys() as $current) {
                if ($current->getTitle() === $title) {
                    $key = $current;
                }
            }

            if (!$key) {
                $key = new UserSshKey($user, $title, $content);
                $em->persist($key);
            } else {
                $key->setContent($content);
            }

            $em->flush();
        });
    }

    /**
     * @Given /^project "([^"]*)" exists$/
     */
    public function projectExists($slug)
    {
        $this->kernelFactory->run(function ($kernel) use ($slug) {
            $em = $kernel->getContainer()->get('doctrine')->getEntityManager();
            $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($slug);
            if ($project) {
                $em->remove($project);
                $em->flush();
                $kernel->getContainer()->get('gitonomy_core.event_dispatcher')->dispatch(GitonomyEvents::PROJECT_DELETE, new ProjectEvent($project));
            }

            $project = new Project($slug, $slug);
            $em->persist($project);
            $em->flush();
            $kernel->getContainer()->get('gitonomy_core.event_dispatcher')->dispatch(GitonomyEvents::PROJECT_CREATE, new ProjectEvent($project));
        });
    }

    /**
     * @Given /^user "([^"]*)" is "([^"]*)" on "([^"]*)"$/
     */
    public function userIsOn($username, $role, $project)
    {
        $this->kernelFactory->run(function ($kernel) use ($username, $role, $project) {
            $em = $kernel->getContainer()->get('doctrine')->getEntityManager();

            $user    = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
            $role    = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName($role);
            $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneByName($project);

            $existing = $em->getRepository('GitonomyCoreBundle:UserRoleProject')->findOneBy(array(
                'user'    => $user,
                'project' => $project
            ));

            if ($existing) {
                $em->remove($existing);
            }
            $em->flush();

            $userRole = new UserRoleProject($user, $project, $role);
            $em->persist($userRole);
            $em->flush();
        });
    }
}

<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Gitonomy\Bundle\CoreBundle\Entity\Email;
use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\Role;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject;
use Gitonomy\Bundle\CoreBundle\Entity\UserSshKey;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\QA\KernelFactory;

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
     * @Given /^administrator has enabled registration$/
     */
    public function administratorHasEnabledRegistration()
    {
        $this->setConfig('open_registation', true);
    }

    /**
     * @Given /^locale is "(.*)"$/
     */
    public function localeIs($value)
    {
        $this->setConfig('locale', $value);
    }

    /**
     * @Given /^user "([^"]*)" does not exist$/
     */
    public function userDoesNotExist($username)
    {
        $this->kernelFactory->run(function ($kernel) use ($username) {
            $em = $kernel->getContainer()->get('doctrine')->getManager();
            $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

            if (!$user) {
                return;
            }

            $em->remove($user);
            $em->flush();
        });
    }

    /**
     * @Given /^user "([^"]*)" has an (inactive )?email "([^"]*)"$/
     */
    public function userHasAnEmail($username, $verb, $email)
    {
        $isActive = $verb === '';

        $this->kernelFactory->run(function ($kernel) use ($username, $isActive, $email) {
            $em = $kernel->getContainer()->get('doctrine')->getManager();
            $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

            if ($mail = $em->getRepository('GitonomyCoreBundle:Email')->findOneBy(array('email' => $email))) {
                $em->remove($mail);
                $em->flush();
            }

            $email = new Email($user, $email, $isActive);
            $em->persist($email);

            $em->flush();
        });
    }

    /**
     * @Given /^user "([^"]*)" has no email "([^"]*)"$/
     */
    public function userHasNoEmail($username, $email)
    {
        $this->kernelFactory->run(function ($kernel) use ($username, $email) {
            $em = $kernel->getContainer()->get('doctrine')->getManager();
            $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);

            if (!$user) {
                throw new \RuntimeException(sprintf('User "%s" does not exist.', $username));
            }

            if ($email = $user->getEmail($email)) {
                $em->remove($email);
                $em->flush();
            }
        });
    }

    /**
     * @Given /^user "([^"]*)" has locale "([^"]*)"$/
     */
    public function userHasLocale($username, $locale)
    {
        $this->kernelFactory->run(function ($kernel) use ($username, $locale) {
            $em = $kernel->getContainer()->get('doctrine')->getManager();
            $user = $em->getRepository('GitonomyCoreBundle:User')->findOneByUsername($username);
            if (!$user) {
                throw new \RuntimeException("User is missing : ".$username);
            }
            $user->setLocale($locale);
            $em->flush();
        });
    }

    /**
     * @Given /^project "([^"]*)" does not exist$/
     */
    public function projectDoesNotExist($slug)
    {
        $this->kernelFactory->run(function ($kernel) use ($slug) {
            $em      = $kernel->getContainer()->get('doctrine')->getManager();
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
            $em = $kernel->getContainer()->get('doctrine')->getManager();
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
            $em = $kernel->getContainer()->get('doctrine')->getManager();

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
            $em = $kernel->getContainer()->get('doctrine')->getManager();
            $project = $em->getRepository('GitonomyCoreBundle:Project')->findOneBySlug($slug);
            if ($project) {
                $kernel->getContainer()->get('gitonomy_core.event_dispatcher')->dispatch(GitonomyEvents::PROJECT_DELETE, new ProjectEvent($project));
                $em->remove($project);
                $em->flush();
            }

            $project = new Project(ucfirst($slug), $slug);
            $kernel->getContainer()->get('gitonomy_core.event_dispatcher')->dispatch(GitonomyEvents::PROJECT_CREATE, new ProjectEvent($project));
            $em->persist($project);
            $em->flush();
        });
    }

    /**
     * @Given /^user "([^"]*)" is "([^"]*)" on "([^"]*)"$/
     */
    public function userIsOn($username, $role, $project)
    {
        $this->kernelFactory->run(function ($kernel) use ($username, $role, $project) {
            $em = $kernel->getContainer()->get('doctrine')->getManager();

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

    /**
     * @Given /^role "([^"]*)" does not exist$/
     */
    public function roleDoesNotExist($role)
    {
        $this->kernelFactory->run(function ($kernel) use ($role) {
            $em = $kernel->getContainer()->get('doctrine')->getManager();
            $existing = $em->getRepository('GitonomyCoreBundle:Role')->findOneByName($role);
            if ($existing) {
                $em->remove($existing);
            }
            $em->flush();
        });
    }

    /**
     * @Given /^(project |global )?role "([^"]*)" exists$/
     */
    public function roleExists($type, $slug)
    {
        $isGlobal = $type !== 'project ';

        $this->kernelFactory->run(function ($kernel) use ($slug, $isGlobal) {
            $em = $kernel->getContainer()->get('doctrine')->getManager();
            $role = $em->getRepository('GitonomyCoreBundle:Role')->findOneBySlug($slug);
            if ($role) {
                $em->remove($role);
                $em->flush();
            }

            $role = new Role(ucfirst($slug), $slug, $slug, $isGlobal);
            $em->persist($role);
            $em->flush();
        });
    }

    protected function setConfig($key, $value)
    {
        $this->kernelFactory->run(function ($kernel) use($key, $value) {
            $kernel->getContainer()->get('gitonomy_core.config')->set($key, $value);
        });
    }
}

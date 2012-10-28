<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

use Gitonomy\QA\KernelFactory;
use Gitonomy\Bundle\CoreBundle\Entity\User;

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
     * @Given /^user "([^"]*)" has SSH key named "([^"]*)", content "(.*)"$/
     */
    public function userHasSshKeyNamedKeyAContent($username, $keyname, $content)
    {
        throw new PendingException();
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
}

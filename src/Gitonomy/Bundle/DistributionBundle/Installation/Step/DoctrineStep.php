<?php

namespace Gitonomy\Bundle\DistributionBundle\Installation\Step;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

use Gitonomy\Bundle\DistributionBundle\Installation\StepInterface;

class DoctrineStep implements StepInterface
{
    public function testValues(array $parameters)
    {
        $driver   = isset($parameters['database_driver'])   ? $parameters['database_driver']   : null;
        $host     = isset($parameters['database_host'])     ? $parameters['database_host']     : null;
        $port     = isset($parameters['database_port'])     ? $parameters['database_port']     : null;
        $user     = isset($parameters['database_user'])     ? $parameters['database_user']     : null;
        $password = isset($parameters['database_password']) ? $parameters['database_password'] : null;
        $database = isset($parameters['database_name'])     ? $parameters['database_name']     : null;
        $path     = isset($parameters['database_path'])     ? $parameters['database_path']     : null;

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'user'     => $user,
            'password' => $password,
            'host'     => $host,
            'driver'   => $driver,
            'port'     => $port,
            'path'     => $path
        );
        $conn = DriverManager::getConnection($connectionParams, $config);

        try {
            $conn->connect();
        } catch (\PDOException $e) {
            return array(self::STATUS_ERROR, "Unable to connect: ".$e->getMessage());
        }

        return array(self::STATUS_OK, "OK");
    }

    public function getStatus(array $parameters)
    {
        list($status, $message) = $this->testValues($parameters);

        return $status;
    }

    public function getSlug()
    {
        return 'database';
    }

    public function getName()
    {
        return 'Database';
    }

    public function getTemplate()
    {
        return 'GitonomyDistributionBundle:Configuration:step_doctrine.html.twig';
    }

    public function getForm()
    {
        return 'installation_step_doctrine';
    }
}

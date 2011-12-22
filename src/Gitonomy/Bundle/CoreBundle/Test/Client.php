<?php

namespace Gitonomy\Bundle\CoreBundle\Test;

use Symfony\Bundle\FrameworkBundle\Client as BaseClient;

use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;

/**
 * Test client for Gitonomy application.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * The current DBAL connection.
     */
    protected $connection;

    /**
     * The repository pool
     */
    protected $repositoryPool;

    /**
     * Was this client already requested?
     */
    protected $requested = false;

    /**
     * Connects as a given user to the application.
     *
     * @param string $username Username
     * @param string $password Password. If not set, will use the username
     */
    public function connect($username = 'alice', $password = null)
    {
        $password = null !== $password ? $password : $username;

        $crawler = $this->request('GET', '/en_US/login');

        $form = $crawler->filter('form input[type=submit][value="Login"]')->form(array(
            '_username' => $username,
            '_password' => $password
        ));

        $this->submit($form);

        return $this->followRedirect();
    }

    /**
     * @inheritdoc
     */
    protected function doRequest($request)
    {
        if (true === $this->requested) {
            $this->kernel->shutdown();
            $this->kernel->boot();
        }

        $this->startIsolation();
        $this->requested = true;

        return $this->kernel->handle($request);
    }

    /**
     * Defines the repository pool to use for the client.
     *
     * @param Gitonomy\Bundle\CoreBundle\Git\RepositoryPool $repositoryPool The
     * repository pool to set
     */
    public function setRepositoryPool(RepositoryPool $repositoryPool)
    {
        $this->repositoryPool = $repositoryPool;
    }

    /**
     * Starts the isolation process of the client.
     */
    public function startIsolation()
    {
        if (null === $this->connection) {
            $this->connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        } else {
            $this->getContainer()->set('doctrine.dbal.default_connection', $this->connection);
        }

        if (null !== $this->repositoryPool) {
            $this->getContainer()->set('gitonomy_core.git.repository_pool', $this->repositoryPool);
        }

        if (false === $this->requested) {
            $this->connection->beginTransaction();
        }
    }

    /**
     * Stops the isolation process of the client.
     */
    public function stopIsolation()
    {
        if (null !== $this->connection) {
            $this->connection->rollback();
            $this->connection->close();
        }

        $this->connection = null;
    }
}

<?php

namespace Gitonomy\Bundle\TwigBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;

use Monolog\Handler\TestHandler;
use Monolog\Logger;

use Gitonomy\Git\Repository;

class GitDataCollector extends DataCollector
{
    protected $handlers = array();

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array();

        foreach ($this->handlers as $name => $handler) {
            $this->data[$name] = $handler->getRecords();
        }
    }

    public function getName()
    {
        return 'git';
    }

    /**
     * Injects Logger inside the repository.
     */
    public function addRepository(Repository $repository)
    {
        if (null !== $repository->getLogger()) {
            throw new \RuntimeException('A logger is already injected in repository.');
        }

        $name = $repository->getGitDir();
        $logger = new Logger($name);
        $handler = new TestHandler();
        $logger->pushHandler($handler);

        $this->handlers[$name] = $handler;

        $repository->setLogger($logger);
    }

    public function getCount($name = null)
    {
        $count = 0;

        // How to count run commands
        $callback = function ($entry) {
            return preg_match('/^run command/', $entry['message']);
        };

        if ($name) {
            return array_sum(array_map($callback, $this->data[$name]));
        }

        foreach ($this->data as $channel) {
            $count += array_sum(array_map($callback, $channel));
        }

        return $count;
    }

    public function getDuration($name = null)
    {
        $count = 0;

        // How to count duration
        $callback = function ($entry) {
            if (preg_match('/duration: ([\d.]+)ms$/', $entry['message'], $vars)) {
                return (float) $vars[1];
            }

            return 0;
        };

        if ($name) {
            return array_sum(array_map($callback, $this->data[$name]));
        }

        foreach ($this->data as $channel) {
            $count += array_sum(array_map($callback, $channel));
        }

        return $count;
    }

    public function getData()
    {
        return null === $this->data ? array() : $this->data;
    }
}

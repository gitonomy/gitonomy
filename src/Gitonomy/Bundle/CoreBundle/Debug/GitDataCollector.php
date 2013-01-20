<?php

namespace Gitonomy\Bundle\CoreBundle\Debug;

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

    public function createLogger(Repository $repository)
    {
        $name = $repository->getGitDir();
        $logger = new Logger($name);
        $handler = new TestHandler();
        $logger->pushHandler($handler);

        $this->handlers[$name] = $handler;

        return $logger;
    }

    public function getCount()
    {
        $count = 0;
        foreach ($this->data as $channel) {
            $count += array_sum(array_map(function ($entry) { return preg_match('/^run command/', $entry['message']); }, $channel));
        }

        return $count;
    }

    public function getData()
    {
        return null === $this->data ? array() : $this->data;
    }
}

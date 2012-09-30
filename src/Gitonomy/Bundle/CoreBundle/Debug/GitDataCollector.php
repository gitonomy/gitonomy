<?php

namespace Gitonomy\Bundle\CoreBundle\Debug;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;

use Gitonomy\Git\Event\PreCommandEvent;
use Gitonomy\Git\Event\PostCommandEvent;

class GitDataCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    public function getName()
    {
        return 'git';
    }

    public function onPreCommand(PreCommandEvent $event)
    {
        $signature = $event->getSignature();

        $this->data[] = array(
            'command'  => $event->getCommand(),
            'args'     => $event->getArgs(),
            'duration' => null,
            'success'  => null
        );
    }

    public function onPostCommand(PostCommandEvent $event)
    {
        $i = count($this->data) - 1;
        if ($i == -1) {
            throw new LogicException('Need a pre before a post');
        }
        $this->data[$i]['duration'] = $event->getDuration();
        $this->data[$i]['success']  = $event->isSuccessful();
    }

    public function getCount()
    {
        return count($this->data);
    }

    public function getTime()
    {
        $t = 0;
        foreach ($this->data as $row) {
            $t += $row['duration'];
        }

        return $t;
    }

    public function getData()
    {
        return $this->data;
    }
}

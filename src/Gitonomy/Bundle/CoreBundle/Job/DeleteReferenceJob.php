<?php

namespace Gitonomy\Bundle\CoreBundle\Job;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\PushReferenceEvent;
use Gitonomy\Bundle\CoreBundle\EventDispatcher\GitonomyEvents;
use Gitonomy\Bundle\JobBundle\Job\Job;
use Gitonomy\Git\PushReference;
use Gitonomy\Git\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteReferenceJob extends Job
{
    public static function create(Project $project, Reference $reference, User $user)
    {
        return new self(array(
            'project_id' => $project->getId(),
            'user_id'    => $user->getId(),
            'ref'        => $reference->getFullname(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $em = $this->getService('doctrine')->getManager();

        $project = $em->find('GitonomyCoreBundle:Project', $this->getParameter('project_id'));
        if (!$project) {
            throw new \InvalidArgumentException(sprintf(
                'Project#%s not found.',
                $this->getParameter('project_id')
            ));
        }

        $user = $em->find('GitonomyCoreBundle:User', $this->getParameter('user_id'));
        if (!$user) {
            throw new \InvalidArgumentException(sprintf(
                'User#%s not found.',
                $this->getParameter('user_id')
            ));
        }

        $repository = $project->getRepository();
        $ref        = $this->getParameter('ref');
        $before     = $repository->getRevision($ref)->getCommit()->getHash();
        $after      = PushReference::ZERO;

        $repository->run('update-ref', array('-d', $ref));

        $pushReference = new PushReference($repository, $ref, $before, $after);
        $event         = new PushReferenceEvent($project, $user, $pushReference);

        $this->getService('gitonomy_core.event_dispatcher')->dispatch(
            GitonomyEvents::PROJECT_PUSH,
            $event
        );
    }
}

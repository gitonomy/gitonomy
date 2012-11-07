<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class MessageRepository extends EntityRepository
{
    public function findByProject(Project $project, $reference = null, $limit = 100)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.feed', 'f')
            ->where('f.project = :project')
            ->setParameter('project', $project)
            ->orderBy('m.publishedAt', 'DESC')
            ->setMaxResults($limit)
        ;

        if ($reference) {
            $qb
                ->andWhere('f.reference = :reference')
                ->setParameter('reference', 'refs/heads/'.$reference)
            ;
        }

        return $qb->getQuery()->execute();
    }

    public function findByUser(User $user, array $projects, $limit = 10)
    {
        $ids = array_map(function($project) { return $project->getId(); }, $projects);

        return $this->createQueryBuilder('m')
            ->leftJoin('m.feed', 'f')
            ->where('m.user = :user')
            ->andWhere('f.project IN (:projects)')
            ->setParameter('user', $user)
            ->setParameter('projects', $ids)
            ->orderBy('m.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute()
        ;
    }
}

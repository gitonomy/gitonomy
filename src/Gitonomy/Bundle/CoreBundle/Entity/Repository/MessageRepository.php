<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class MessageRepository extends EntityRepository
{
    public function findByProject(Project $project, $branch = null, $limit = 100)
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.feed', 'f')
            ->where('f.project = :project')
            ->setParameter('project', $project)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
        ;

        if ($branch) {
            $qb
                ->andWhere('f.reference = :reference')
                ->setParameter('reference', 'refs/heads/'.$branch)
            ;
        }

        return $qb->getQuery()->execute();
    }

    public function findByUser(User $user, array $projects, $limit = 10)
    {
        $ids = array_map(function($project) { return $project->getId(); }, $projects);

        if (0 === count($ids)) {
            return;
        }

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

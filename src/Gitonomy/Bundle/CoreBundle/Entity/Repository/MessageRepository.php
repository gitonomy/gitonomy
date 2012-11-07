<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Gitonomy\Bundle\CoreBundle\Entity\Project;

class MessageRepository extends EntityRepository
{
    public function findByProject(Project $project, $reference = null)
    {
        $qb = $this
            ->createQueryBuilder('m')
            ->leftJoin('m.feed', 'f')
            ->where('f.project = :project')
            ->setParameter('project', $project)
            ->orderBy('m.publishedAt', 'DESC')
        ;

        if ($reference) {
            $qb
                ->andWhere('f.reference = :reference')
                ->setParameter('reference', 'refs/heads/'.$reference)
            ;
        }

        return $qb->getQuery()->execute();
    }

    public function findByUser(User $user, $limit = 10)
    {
        return $this
            ->createQueryBuilder('m')
            ->leftJoin('m.feed', 'f')
            ->where('f.project = :project')
            ->setParameter('project', $project)
            ->orderBy('m.publishedAt', 'DESC')
            ->getQuery()
            ->getResults()
        ;
    }
}

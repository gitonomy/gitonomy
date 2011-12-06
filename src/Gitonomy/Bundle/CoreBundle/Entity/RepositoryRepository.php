<?php

namespace Gitonomy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RepositoryRepository extends EntityRepository
{
    public function exists($namespace, $name)
    {
        $count = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.namespace = :namespace AND r.name = :name')
            ->setParameters(array(
                'namespace' => $namespace,
                'name'      => $name
            ))
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return 1 == $count;
    }

    public function markAllAsInstalled()
    {
        $this->createQueryBuilder('k')
            ->update()
            ->set('k.isInstalled', true)
            ->getQuery()
            ->execute()
        ;
    }
}

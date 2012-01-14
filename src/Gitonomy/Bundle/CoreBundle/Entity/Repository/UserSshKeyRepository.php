<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserSshKeyRepository extends EntityRepository
{
    public function getKeyList()
    {
        return $this->createQueryBuilder('k')
            ->leftJoin('k.user', 'u')
            ->select('u.username, k.content')
            ->getQuery()
            ->setHydrationMode(Query::HYDRATE_SCALAR)
            ->execute()
        ;
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

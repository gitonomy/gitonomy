<?php

namespace Gitonomy\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Gitonomy\Bundle\CoreBundle\Entity;

class EmailRepository extends EntityRepository
{
    public function getEmailFromActivation($username, $hash)
    {
        $queryBuilder = $this
            ->createQueryBuilder('email')
            ->leftJoin('email.user', 'user')
            ->where('user.username = :username')
            ->andWhere('email.activation = :hash')
            ->setParameters(array(
                'username' => $username,
                'hash'     => $hash,
            ))
        ;

        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
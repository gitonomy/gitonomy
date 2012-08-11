<?php

namespace Gitonomy\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Gitonomy\Bundle\CoreBundle\Entity\User;

class UserForgotPasswordRepository extends EntityRepository
{
    public function getToken(User $user)
    {
        $queryBuilder = $this
            ->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameters(array(
                'user' => $user,
            ))
        ;

        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return $user->createForgotPassword();
        }
    }
}

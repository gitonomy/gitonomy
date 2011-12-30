<?php

namespace Gitonomy\Bundle\FrontendBundle\Validation\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Bundle\DoctrineBundle\Registry;

class UserEmailValidator extends ConstraintValidator
{
    /**
     * @var Symfony\Bundle\DoctrineBundle\Registry
     */
    protected $doctrineRegistry;

    function __construct(Registry $doctrineRegistry)
    {
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public function isValid($value, Constraint $constraint)
    {
        if (null === $value) {
            return true;
        }

        $count = $this->doctrineRegistry
            ->getRepository('GitonomyCoreBundle:Email')
            ->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->where('e.email = :email')
            ->setParameters(array(
                'email' => $value
            ))
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if ($count == 0) {
            $this->setMessage($constraint->message);

            return false;
        }

        return true;
    }
}

<?php

namespace Gitonomy\Bundle\FrontendBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Doctrine\ORM\EntityManager;

/**
 * Unique constraint validator
 *
 * @author Julien DIDIER <julien@jdidier.net>
 */
class UniqueValidator extends ConstraintValidator
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Checks if the passed value is unique.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constrain for the validation
     *
     * @return Boolean Whether or not the value is valid
     *
     * @api
     */
    public function isValid($value, Constraint $constraint)
    {
        $field      = $constraint->field;
        $value      = (string) $value;
        $repository = $this->entityManager->getRepository($constraint->class);
        $object     = $repository->findOneBy(array($field => $value));
        $valid      = null === $object;
        if (!$valid) {
            $this->setMessage($constraint->message, array('{{ value }}' => $value));

            return false;
        }

        return true;
    }
}

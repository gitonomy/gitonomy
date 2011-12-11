<?php

namespace Gitonomy\Bundle\CoreBundle\Validator\Constraints;

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
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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
        $className  = $this->context->getCurrentClass();
        $property   = $this->context->getCurrentProperty();
        $value      = (string) $value;
        $repository = $this->em->getRepository($className);
        $object     = $repository->findOneBy(array($property => $value));

        if (null !== $object) {
            $this->setMessage($constraint->message, array('{{ value }}' => $value));

            return false;
        }

        return true;
    }
}

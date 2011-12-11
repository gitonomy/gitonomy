<?php

namespace Gitonomy\Bundle\FrontendBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Unique onstraint instance
 *
 * @author Julien DIDIER <julien@jdidier.net>
 *
 * @Annotation
 */
class Unique extends Constraint
{
    public $message = 'This value should be unique';

    public function validatedBy()
    {
        return 'unique';
    }
}

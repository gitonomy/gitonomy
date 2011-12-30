<?php

namespace Gitonomy\Bundle\FrontendBundle\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserEmail extends Constraint
{
    public $message = 'This e-mail is not present in our database';

    public function validatedBy()
    {
        return 'user_email';
    }
}

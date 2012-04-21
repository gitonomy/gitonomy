<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints as Assert;

use Gitonomy\Bundle\FrontendBundle\Validation\Constraints as GitonomyAssert;

class ForgotPasswordRequestType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('email', 'email');
    }

    public function getDefaultOptions()
    {
        $collectionConstraint = new  GitonomyAssert\UserEmail();

        return array('validation_constraint' => $collectionConstraint);
    }

    public function getName()
    {
        return 'forgot_password_request';
    }
}

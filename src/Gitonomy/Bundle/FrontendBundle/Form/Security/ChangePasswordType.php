<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('password', 'repeated',array('type' => 'password'))
        ;
    }

    public function getName()
    {
        return 'change_password';
    }
}

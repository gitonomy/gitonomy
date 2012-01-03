<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ChangeUsernameType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username', 'text')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'validation_groups' => array('change_username')
        );
    }

    public function getName()
    {
        return 'change_username';
    }
}

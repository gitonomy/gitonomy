<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('password', 'repeated', array(
                'type'     => 'password',
            ))
        ;
    }

    public function getDefaultOptions()
    {
        return array(
            'validation_groups' => array('change_password'),
            'data_class'        => 'Gitonomy\Bundle\CoreBundle\Entity\User',
        );
    }

    public function getName()
    {
        return 'change_password';
    }
}

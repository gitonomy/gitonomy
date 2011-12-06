<?php

namespace Gitonomy\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label' => 'Your username'
            ))
            ->add('fullname', 'text', array(
                'label' => 'Your fullname'
            ))
            ->add('email', 'email', array(
                'label' => 'Your e-mail'
            ))
            ->add('password', 'repeated',array(
                'type' => 'password',
                'first_options' => array(
                    'label' => 'Choose a password'
                ),
                'second_options' => array(
                    'label' => 'And repeat it'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'registration';
    }
}

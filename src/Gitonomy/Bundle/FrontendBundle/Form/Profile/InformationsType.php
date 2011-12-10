<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class InformationsType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('fullname', 'text')
            ->add('timezone', 'timezone')
        ;
    }

    public function getName()
    {
        return 'profile_informations';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'validation_groups' => array('profile_informations')
        );
    }
}

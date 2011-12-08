<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserInformationsType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('fullname', 'text')
        ;
    }

    public function getName()
    {
        return 'informations';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'validation_groups' => array('informations')
        );
    }
}

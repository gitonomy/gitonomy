<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChangeUsernameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('change_username'),
            'data_class'        => 'Gitonomy\Bundle\CoreBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'change_username';
    }
}

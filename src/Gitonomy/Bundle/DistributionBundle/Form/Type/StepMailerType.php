<?php

namespace Gitonomy\Bundle\DistributionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StepMailerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transport',  'choice', array('choices' => array('smtp' => 'smtp', 'mail' => 'mail')))
            ->add('host',       'text',   array('required' => false))
            ->add('user',       'text',   array('required' => false))
            ->add('password',   'text',   array('required' => false))
            ->add('encryption', 'choice', array('choices' => array('ssl' => 'ssl')))
            ->add('authMode',   'choice', array('choices' => array('login' => 'login')))
        ;
    }

    public function getName()
    {
        return 'configurator_step_mailer';
    }
}

<?php

namespace Gitonomy\Bundle\DistributionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transport', 'choice', array('choices' => array('smtp', 'mail')))
            ->add('host',      'text', array('required' => false))
            ->add('user',      'text', array('required' => false))
            ->add('password',  'text', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'configurator_step_mailer';
    }
}

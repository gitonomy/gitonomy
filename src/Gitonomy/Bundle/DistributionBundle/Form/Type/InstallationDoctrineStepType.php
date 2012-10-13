<?php

namespace Gitonomy\Bundle\DistributionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InstallationDoctrineStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('database_driver', 'choice', array(
                'choices' => array(
                    'pdo_mysql' => 'MySQL (PDO)'
                )
            ))
            ->add('database_host', 'text')
            ->add('database_port', 'integer', array('required' => false))
            ->add('database_name', 'text')
            ->add('database_user', 'text')
            ->add('database_password', 'text', array('required' => false))
        ;
    }

    public function getName()
    {
        return 'installation_step_doctrine';
    }
}

<?php

namespace Gitonomy\Bundle\DistributionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InstallationSecretStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('secret', 'text')
            ->add('remember_secret', 'text')
        ;
    }

    public function getName()
    {
        return 'installation_step_secret';
    }
}

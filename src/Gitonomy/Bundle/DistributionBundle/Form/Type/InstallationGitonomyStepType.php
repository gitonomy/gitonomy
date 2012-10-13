<?php

namespace Gitonomy\Bundle\DistributionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InstallationGitonomyStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', 'text')
            ->add('repository_path', 'text')
            ->add('ssh_access', 'text')
            ->add('project_name', 'text')
            ->add('project_baseline', 'text')
            ->add('open_registration', 'checkbox')
        ;
    }

    public function getName()
    {
        return 'installation_step_gitonomy';
    }
}

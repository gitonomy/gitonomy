<?php

namespace Gitonomy\Bundle\DistributionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StepGitonomyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('repository_path', 'repository_path')
        ;
    }

    public function getName()
    {
        return 'configurator_step_gitonomy';
    }
}

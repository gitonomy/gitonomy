<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('slug', 'text')
            ->add('userRoles', 'entity', array(
                'class'    => 'Gitonomy\Bundle\CoreBundle\Entity\Role',
                'multiple' => true,
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Gitonomy\Bundle\CoreBundle\Entity\Project',
        );
    }

    public function getName()
    {
        return 'adminproject';
    }
}

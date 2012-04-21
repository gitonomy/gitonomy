<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text');
        if ('create' === $options['action']) {
            $builder->add('slug', 'text');
        }
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => 'Gitonomy\Bundle\CoreBundle\Entity\Project',
        );
    }

    public function getParent(array $options)
    {
        return 'baseadmin';
    }

    public function getName()
    {
        return 'adminproject';
    }
}

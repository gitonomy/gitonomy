<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProjectGitAccessesType extends AbstractType
{
    public function getDefaultOptions(array $options)
    {
        return array(
            'type'         => 'project_git_access',
            'allow_add'    => true,
            'allow_delete' => true
        );
    }

    public function getParent(array $options)
    {
        return 'collection';
    }

    public function getName()
    {
        return 'project_git_accesses';
    }
}

<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BaseAdminType extends AbstractType
{
    public function getDefaultOptions(array $options)
    {
        return array(
            'validation_groups' => array('admin')
        );
    }

    public function getName()
    {
        return 'base_admin';
    }
}

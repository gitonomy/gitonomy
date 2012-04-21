<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BaseAdminType extends AbstractType
{
    public function getDefaultOptions()
    {
        return array(
            'validation_groups' => array('admin'),
            'action'            => null,
        );
    }

    public function getName()
    {
        return 'baseadmin';
    }
}

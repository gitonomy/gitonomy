<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('fullname', 'text')
            ->add('email', 'email')
            ->add('timezone', 'timezone')
            ->add('projectUserRoles', 'collection', array(
                'type'            => 'adminuserrole',
                'prototype'       => true,
                'by_reference'    => false,
                'label'           => 'Project user roles',
                'allow_add'       => true,
                'allow_delete'    => true,
                'options'         => array('from_adminuser' => true, 'global' => false),
            ))
            ->add('globalUserRoles', 'collection', array(
                'type'            => 'adminuserrole',
                'by_reference'    => false,
                'label'           => 'Global user roles',
                'options'         => array('from_adminuser' => true, 'global' => true),
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Gitonomy\Bundle\CoreBundle\Entity\User',
        );
    }

    public function getParent(array $options)
    {
        return 'baseadmin';
    }

    public function getName()
    {
        return 'adminuser';
    }
}

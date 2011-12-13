<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('description', 'text')
            ->add('permissions', 'entity', array(
                'class'    => 'Gitonomy\Bundle\CoreBundle\Entity\Permission',
                'multiple' => true,
                'translation_domain' => 'admin_roles',
                'group_by' => 'parent',
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('p')->where('p.parent is not null');
                }
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Gitonomy\Bundle\CoreBundle\Entity\Role',
        );
    }

    public function getParent(array $options)
    {
        return 'baseadmin';
    }

    public function getName()
    {
        return 'adminrole';
    }
}

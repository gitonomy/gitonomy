<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('description', 'text')
            ->add('permissions', 'entity', array(
                'class'    => 'Gitonomy\Bundle\CoreBundle\Entity\Permission',
                'multiple' => true,
                'expanded' => true,
                'translation_domain' => 'admin_roles',
                // 'group_by' => 'parent.name',
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('p')->where('p.parent is not null');
                }
            ))
            ->add('isGlobal', 'checkbox')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gitonomy\Bundle\CoreBundle\Entity\Role',
        ));
    }

    public function getParent()
    {
        return 'baseadmin';
    }

    public function getName()
    {
        return 'adminrole';
    }
}

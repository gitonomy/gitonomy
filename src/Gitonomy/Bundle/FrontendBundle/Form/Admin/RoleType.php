<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormEvents;

use Doctrine\ORM\EntityRepository;

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
                'expanded' => true,
                'translation_domain' => 'permissions',
                'query_builder' => function (EntityRepository $repository) use ($options) {
                    return $repository
                        ->createQueryBuilder('r')
                        ->where('r.isGlobal = :isGlobal')
                        ->setParameters(array('isGlobal' => $options['is_global']))
                    ;
                }
            ))
            ->add('isGlobal', 'checkbox')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Gitonomy\Bundle\CoreBundle\Entity\Role',
            'is_global'  => true
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

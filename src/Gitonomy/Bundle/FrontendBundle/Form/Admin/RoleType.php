<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

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
                'query_builder' => function (Options $options) use ($options) {
                    return function (EntityRepository $repository) use ($options) {
                        return $repository
                            ->createQueryBuilder('r')
                            ->where('r.isGlobal = :isGlobal')
                            ->setParameters(array('isGlobal' => $options['is_global']))
                        ;
                    };
                }
            ))
            ->add('isGlobal', 'checkbox')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gitonomy\Bundle\CoreBundle\Entity\Role',
            'is_global'  => true
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

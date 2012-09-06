<?php

/**
 * This file is part of Gitonomy.
 *
 * (c) Alexandre Salomé <alexandre.salome@gmail.com>
 * (c) Julien DIDIER <genzo.wm@gmail.com>
 *
 * This source file is subject to the GPL license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('fullname', 'text')
            ->add('timezone', 'timezone')
            ->add('globalRoles', 'entity', array(
                'class'   => 'Gitonomy\Bundle\CoreBundle\Entity\Role',
                'query_builder' => function(EntityRepository $er) {
                    $query = $er
                        ->createQueryBuilder('R')
                        ->where('R.isGlobal = true')
                        ->orderBy('R.name', 'ASC');

                    return $query;
                },
                'property' => 'name',
                'multiple' => true,
                'expanded' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gitonomy\Bundle\CoreBundle\Entity\User',
            'user'       => null,
        ));
    }

    public function getParent()
    {
        return 'baseadmin';
    }

    public function getName()
    {
        return 'adminuser';
    }
}

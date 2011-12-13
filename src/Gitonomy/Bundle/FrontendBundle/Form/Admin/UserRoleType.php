<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;

class UserRoleType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $isGlobal = array_key_exists('global', $options) && $options['global'] === true;

        $builder
            ->add('role', 'entity', array(
                'class'   => 'Gitonomy\Bundle\CoreBundle\Entity\Role',
                'query_builder' => function(EntityRepository $er) use ($isGlobal) {
                    return $er->createQueryBuilder('r')
                        ->where('r.isGlobal = :isGlobal')
                        ->orderBy('r.name', 'ASC')
                        ->setParameter('isGlobal', $isGlobal);
                },
            ))
        ;
        if (!$options['global']) {
            $builder
                ->add('project', 'entity', array(
                    'class'   => 'Gitonomy\Bundle\CoreBundle\Entity\Project',
                ))
            ;
        }
        if (!$options['from_adminuser']) {
            $builder
                ->add('user', 'entity', array(
                    'class'   => 'Gitonomy\Bundle\CoreBundle\Entity\User',
                ))
            ;
        }
    }

    public function buildView(FormView $view, FormInterface $form)
    {
        parent::buildView($view, $form);

        $view
            ->set('label', '')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class'     => 'Gitonomy\Bundle\CoreBundle\Entity\UserRole',
            'from_adminuser' => false,
            'global'         => false,
        );
    }

    public function getParent(array $options)
    {
        return 'baseadmin';
    }

    public function getName()
    {
        return 'adminuserrole';
    }
}

<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;

class UserRoleProjectType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $projects = $options['usedProjects'];

        $usedProjects = array();
        foreach ($projects as $project) {
            $usedProjects[] = $project->getId();
        }

        $builder
            ->add('project', 'entity', array(
                'class'   => 'Gitonomy\Bundle\CoreBundle\Entity\Project',
                'query_builder' => function(EntityRepository $er) use ($usedProjects) {
                    $query = $er
                        ->createQueryBuilder('P')
                        ->orderBy('P.name', 'ASC');
                    if (count($usedProjects) > 0) {
                        $query
                            ->where('P.id NOT IN (:projectIds)')
                            ->setParameter('projectIds', $usedProjects);
                    }

                    return $query;
                },
            ))
            ->add('role', 'entity', array(
                'class'   => 'Gitonomy\Bundle\CoreBundle\Entity\Role',
            ))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class'   => 'Gitonomy\Bundle\CoreBundle\Entity\UserRoleProject',
            'usedProjects' => array(),
        );
    }

    public function getParent(array $options)
    {
        return 'baseadmin';
    }

    public function getName()
    {
        return 'adminuserroleproject';
    }
}

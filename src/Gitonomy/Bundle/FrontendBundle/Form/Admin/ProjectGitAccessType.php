<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class ProjectGitAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', 'entity', array(
            'class' => 'Gitonomy\Bundle\CoreBundle\Entity\Role'
        ));
        $builder->add('reference', 'text');
        $builder->add('is_read',  'checkbox');
        $builder->add('is_write', 'checkbox');
        $builder->add('is_admin', 'checkbox');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gitonomy\Bundle\CoreBundle\Entity\ProjectGitAccess',
        ));
    }

    public function getName()
    {
        return 'project_git_access';
    }
}

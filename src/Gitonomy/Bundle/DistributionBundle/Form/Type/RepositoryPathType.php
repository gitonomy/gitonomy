<?php

namespace Gitonomy\Bundle\DistributionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Gitonomy\Bundle\DistributionBundle\Form\Transformer\RepositoryPathTransformer;

class RepositoryPathType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new RepositoryPathTransformer())
            ->add('type', 'choice', array('choices' => array(
                'app'    => 'Relative: app/ folder',
                'custom' => 'Absolute: Custom path'
            )))
            ->add('value', 'text')
        ;
    }

    public function getName()
    {
        return 'repository_path';
    }
}

<?php

namespace Gitonomy\Bundle\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CreateSshKeyType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('content', 'textarea')
        ;
    }

    public function getName()
    {
        return 'create_ssh_key';
    }
}

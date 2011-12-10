<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SshKeyType extends AbstractType
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
        return 'profile_ssh_key';
    }
}

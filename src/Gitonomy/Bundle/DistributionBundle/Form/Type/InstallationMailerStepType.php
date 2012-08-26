<?php

namespace Gitonomy\Bundle\DistributionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class InstallationMailerStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mailer_transport', 'text')
            ->add('mailer_host', 'text')
            ->add('mailer_user', 'text')
            ->add('mailer_password', 'text')
            ->add('mailer_auth_mode', 'text')
            ->add('mailer_encryption', 'text')
            ->add('mailer_from_name', 'text')
            ->add('mailer_from_email', 'email')
        ;
    }

    public function getName()
    {
        return 'installation_step_mailer';
    }
}

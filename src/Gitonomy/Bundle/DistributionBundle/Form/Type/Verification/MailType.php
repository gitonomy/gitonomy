<?php

namespace Gitonomy\Bundle\DistributionBundle\Form\Type\Verification;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email')
        ;
    }

    public function getName()
    {
        return 'verification_mail';
    }
}

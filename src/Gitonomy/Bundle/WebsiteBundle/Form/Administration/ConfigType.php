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

namespace Gitonomy\Bundle\WebsiteBundle\Form\Administration;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project = $builder->create('project', 'form', array('virtual' => true))
            ->add('locale', 'text')
            ->add('ssh_access', 'text')
            ->add('name', 'text')
            ->add('baseline', 'text')
            ->add('open_registration', 'checkbox', array('required' => false))
        ;

        $mailer = $builder->create('mailer', 'form', array('virtual' => true))
            ->add('mailer_transport', 'text', array('required' => false))
            ->add('mailer_host', 'text', array('required' => false))
            ->add('mailer_user', 'text', array('required' => false))
            ->add('mailer_password', 'text', array('required' => false))
            ->add('mailer_auth_mode', 'text', array('required' => false))
            ->add('mailer_encryption', 'text', array('required' => false))
            ->add('mailer_from_name', 'text', array('required' => false))
            ->add('mailer_from_email', 'email', array('required' => false))
        ;

        $builder
            ->add($project)
            ->add($mailer)
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'administration_config'
        ));
    }

    public function getName()
    {
        return 'administration_config';
    }
}

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
        $mailerTransportChoices = array(
            'null'  => 'Disabled',
            'smtp'  => 'SMTP',
            'gmail' => 'Gmail',
            'mail'  => 'PHP mail() function'
        );

        $project = $builder->create('project', 'form', array('virtual' => true))
            ->add('locale', 'gitonomy_locale', array('label' => 'form.project.locale'))
            ->add('ssh_access', 'text', array('label' => 'form.project.ssh_access'))
            ->add('name', 'text', array('label' => 'form.project.name'))
            ->add('baseline', 'text', array('label' => 'form.project.baseline'))
            ->add('open_registration', 'checkbox', array('required' => false, 'label' => 'form.project.open_registration'))
        ;

        $mailer = $builder->create('mailer', 'form', array('virtual' => true))
            ->add('mailer_transport', 'choice', array('required' => true, 'choices' => $mailerTransportChoices, 'label' => 'form.mailer.transport'))
            ->add('mailer_host', 'text', array('required' => false, 'label' => 'form.mailer.host'))
            ->add('mailer_port', 'number', array('required' => false, 'label' => 'form.mailer.port'))
            ->add('mailer_username', 'text', array('required' => false, 'label' => 'form.mailer.username'))
            ->add('mailer_password', 'text', array('required' => false, 'label' => 'form.mailer.password'))
            ->add('mailer_auth_mode', 'text', array('required' => false, 'label' => 'form.mailer.auth_mode'))
            ->add('mailer_encryption', 'text', array('required' => false, 'label' => 'form.mailer.encryption'))
            ->add('mailer_from_name', 'text', array('required' => false, 'label' => 'form.mailer.from_name'))
            ->add('mailer_from_email', 'email', array('required' => false, 'label' => 'form.mailer.from_email'))
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

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

namespace Gitonomy\Bundle\WebsiteBundle\Form\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InformationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array('label' => 'form.informations.username'))
            ->add('fullname', 'text', array('label' => 'form.informations.fullname'))
            ->add('timezone', 'timezone', array('label' => 'form.informations.timezone'))
            ->add('locale', 'gitonomy_locale', array('label' => 'form.informations.locale'))
        ;
    }

    public function getName()
    {
        return 'profile_informations';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups'  => array('profile'),
            'translation_domain' => 'profile_informations'
        ));
    }
}

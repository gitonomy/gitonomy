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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * @author Julien DIDIER <genzo.wm@gmail.com>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ProfilePasswordType extends AbstractType
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $encoderFactory = $this->encoderFactory;

        $builder
            ->add('old_password', 'password', array(
                'label'  => 'form.current_password',
                'mapped' => false,
                'constraints' => array(
                    new UserPassword()
                ),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'profile_password'
        ));
    }

    public function getParent()
    {
        return 'user_password';
    }

    public function getName()
    {
        return 'profile_password';
    }
}

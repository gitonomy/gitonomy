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

namespace Gitonomy\Bundle\WebsiteBundle\Form\Splash;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

use Gitonomy\Bundle\CoreBundle\Entity\User;

class ChangePasswordType extends AbstractType
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
            ->add('password', 'repeated', array(
                'type'           => 'password',
                'constraints'    => array(new NotBlank()),
                'mapped'         => false,
                'first_options'  => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirm'),
            ))
            ->addEventListener(FormEvents::BIND, function (FormEvent $event) use ($encoderFactory) {
                $user = $event->getData();
                if (!$user instanceof User) {
                    throw new \RuntimeException('Data for changing password form should be a user');
                }

                $password = $event->getForm()->get('password')->getData();
                if (null === $password) {
                    return;
                }

                $user->setPassword($password, $encoderFactory->getEncoder($user));
            });
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'Gitonomy\Bundle\CoreBundle\Entity\User',
            'translation_domain' => 'forgot_password'
        ));
    }

    public function getName()
    {
        return 'change_password';
    }
}

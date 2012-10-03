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

namespace Gitonomy\Bundle\FrontendBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Gitonomy\Bundle\CoreBundle\Entity\User;

class RegistrationType extends AbstractType
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
            ->add('username', 'text')
            ->add('fullname', 'text')
            ->add('defaultEmail', 'useremail')
            ->add('timezone', 'timezone')
            ->add('password', 'repeated',array(
                'type'   => 'password',
                'mapped' => false,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Confirm password')
            ))
            ->addEventListener(FormEvents::BIND, function (FormEvent $event) use ($encoderFactory) {
                $user = $event->getData();
                if (!$user instanceof User) {
                    throw new \RuntimeException('Data for registration form should be a user');
                }

                $password = $event->getForm()->get('password')->getData();
                if (null === $password) {
                    return;
                }

                $user->setPassword($password, $encoderFactory->getEncoder($user));
            });
        ;
    }

    public function getName()
    {
        return 'user_registration';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('registration')
        ));
    }
}

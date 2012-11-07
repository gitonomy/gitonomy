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

namespace Gitonomy\Bundle\WebsiteBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

use Gitonomy\Bundle\CoreBundle\Entity\User;

/**
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class UserPasswordType extends AbstractType
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
                'mapped'         => false,
                'first_options'  => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.confirm_password')
            ))
            ->addEventListener(FormEvents::POST_BIND, function (FormEvent $event) use ($encoderFactory) {
                if (!$event->getForm()->isValid()) {
                    return;
                }

                $user = $event->getData();
                if (!$user instanceof User) {
                    throw new \RuntimeException('Data for registration form should be a user');
                }

                $password = $event->getForm()->get('password')->getData();
                if (null === $password) {
                    return;
                }

                $user->setPassword($password, $encoderFactory->getEncoder($user));
            })
        ;
    }

    public function getName()
    {
        return 'user_password';
    }
}

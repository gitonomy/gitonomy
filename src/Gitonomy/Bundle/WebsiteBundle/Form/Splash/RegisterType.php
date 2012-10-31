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

use Gitonomy\Bundle\CoreBundle\Validation\Constraints\NewEmail;
use Gitonomy\Bundle\CoreBundle\Entity\User;
use Gitonomy\Bundle\CoreBundle\Entity\Email;

class RegisterType extends AbstractType
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
            ->add('username', 'text',     array('label' => 'form.username'))
            ->add('fullname', 'text',     array('label' => 'form.fullname'))
            ->add('email',    'email',    array(
                'mapped'      => false,
                'constraints' => array(new NewEmail(array('groups' => array('registration'))))
            ))
            ->add('timezone', 'timezone', array('label' => 'form.timezone'))
            ->add('password', 'repeated', array(
                'type'           => 'password',
                'mapped'         => false,
                'first_options'  => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirm')
            ))
            ->addEventListener(FormEvents::BIND, function (FormEvent $event) use ($encoderFactory) {
                $user = $event->getData();
                $form = $event->getForm();

                if (!$user instanceof User) {
                    throw new \RuntimeException('Data for registration form should be a user');
                }

                $password = $form->get('password')->getData();
                if (null === $password) {
                    return;
                }

                $user->createEmail($form->get('email')->getData(), true);
                $user->setPassword($password, $encoderFactory->getEncoder($user));
            });
        ;
    }

    public function getName()
    {
        return 'register';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'Gitonomy\Bundle\CoreBundle\Entity\User',
            'translation_domain' => 'register',
            'validation_groups'  => array('registration')
        ));
    }
}

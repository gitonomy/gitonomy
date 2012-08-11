<?php

namespace Gitonomy\Bundle\FrontendBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

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
                'type'     => 'password',
                'mapped'   => false,
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
            'validation_groups' => array('change_password'),
            'data_class'        => 'Gitonomy\Bundle\CoreBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'change_password';
    }
}

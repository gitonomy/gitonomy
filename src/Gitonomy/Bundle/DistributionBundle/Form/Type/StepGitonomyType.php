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

namespace Gitonomy\Bundle\DistributionBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StepGitonomyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project_name',      'text')
            ->add('project_baseline',  'text')
            ->add('open_registration', 'checkbox', array(
                'label'    => 'Allow registration in application',
                'required' => false
            ))
            ->add('repository_path',   'repository_path')
            ->add('ssh_access',        'text')
            ->add('mailer_from_email', 'text')
            ->add('mailer_from_name',  'text')
        ;
    }

    public function getName()
    {
        return 'configurator_step_gitonomy';
    }
}

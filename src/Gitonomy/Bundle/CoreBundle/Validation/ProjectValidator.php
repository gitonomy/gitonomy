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

namespace Gitonomy\Bundle\CoreBundle\Validation;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * @author Francis Besset <francis.besset@gmail.com>
 */
class ProjectValidator
{
    public static function isSlugValid(Project $project, ExecutionContextInterface $context)
    {
        if (!preg_match('#^'.Project::SLUG_PATTERN.'$#', $project->getSlug())) {
            $context->addViolationAt('slug', 'This value is not valid.', array(), null);
        }
    }
}

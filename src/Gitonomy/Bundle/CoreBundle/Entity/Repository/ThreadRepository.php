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

namespace Gitonomy\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Gitonomy\Bundle\CoreBundle\Entity\Project;
use Gitonomy\Bundle\CoreBundle\Entity\Thread;

class ThreadRepository extends EntityRepository
{
    public function findOneOrCreate(Project $project, $reference)
    {
        // throw new \Exception($reference->getReference());
        try {
            $thread = $this->findOneBy(array(
                'project'   => $project,
                'reference' => $reference
            ));
        } catch (\Exception $e) {
            throw $e;
        }
        if (null === $thread) {
            $thread = new Thread($project, $reference);
        }

        return $thread;
    }
}

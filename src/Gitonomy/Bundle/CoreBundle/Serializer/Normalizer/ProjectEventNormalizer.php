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

namespace Gitonomy\Bundle\CoreBundle\Serializer\Normalizer;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;
use Symfony\Component\Serializer\SerializerAware;

use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class ProjectEventNormalizer extends SerializerAwareNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize($object, $format = null)
    {
        return array(
            'project' => $this->serializer->serialize($object->getProject(), $format),
            'user'    => $this->serializer->serialize($object->getUser(), $format)
        );
    }

    public function denormalize($data, $class, $format = null)
    {
        $project = $this->serializer->deserialize($data['project'], 'Gitonomy\Bundle\CoreBundle\Entity\Project', $format);

        if (is_array($data['user'])) {
            $user    = $this->serializer->deserialize($data['user'], 'Gitonomy\Bundle\CoreBundle\Entity\User', $format);
        } else {
            $user = null;
        }

        return new ProjectEvent($project, $user);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ProjectEvent;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type == 'Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\ProjectEvent';
    }
}

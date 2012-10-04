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

use Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\PushReferenceEvent;
use Gitonomy\Bundle\CoreBundle\Git\RepositoryPool;
use Gitonomy\Git\PushReference;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class PushReferenceEventNormalizer extends SerializerAwareNormalizer implements NormalizerInterface, DenormalizerInterface
{
    protected $repositoryPool;

    public function __construct(RepositoryPool $repositoryPool)
    {
        $this->repositoryPool = $repositoryPool;
    }

    public function normalize($object, $format = null)
    {
        $pushReference = $object->getReference();

        return array(
            'project' => $this->serializer->normalize($object->getProject(), $format),
            'user'    => $this->serializer->normalize($object->getUser(), $format),
            'push'    => array(
                $pushReference->getReference(),
                $pushReference->getBefore(),
                $pushReference->getAfter()
            )
        );
    }

    public function denormalize($data, $class, $format = null)
    {
        $project = $this->serializer->denormalize($data['project'], 'Gitonomy\Bundle\CoreBundle\Entity\Project', $format);

        if (is_array($data['user'])) {
            $user    = $this->serializer->denormalize($data['user'], 'Gitonomy\Bundle\CoreBundle\Entity\User', $format);
        } else {
            $user = null;
        }

        $repository = $this->repositoryPool->getGitRepository($project);
        $pushReference = new PushReference($repository, $data['push'][0], $data['push'][1], $data['push'][2]);

        return new PushReferenceEvent($project, $user, $pushReference);
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof PushReferenceEvent;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type == 'Gitonomy\Bundle\CoreBundle\EventDispatcher\Event\PushReferenceEvent';
    }
}

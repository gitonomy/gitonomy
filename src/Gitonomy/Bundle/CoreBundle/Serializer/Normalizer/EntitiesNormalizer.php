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

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * This normalizer only gives primary key informations as
 * normalized data.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class EntitiesNormalizer implements NormalizerInterface, DenormalizerInterface
{
    protected $doctrineRegistry;

    public function __construct(ManagerRegistry $doctrineRegistry)
    {
        $this->doctrineRegistry = $doctrineRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null)
    {
        $class = get_class($object);

        return $this->doctrineRegistry
            ->getManagerForClass($class)
            ->getMetadataFactory()
            ->getMetadataFor($class)
            ->getIdentifierValues($object)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null)
    {
        return $this->doctrineRegistry
            ->getManagerForClass($class)
            ->getRepository($class)
            ->find($data)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return null !== $this->doctrineRegistry->getManagerForClass(get_class($data));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return null !== $this->doctrineRegistry->getManagerForClass($type);
    }
}

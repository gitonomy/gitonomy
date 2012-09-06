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

namespace Gitonomy\Bundle\DistributionBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class RepositoryPathTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value || $value === '') {
            return array(
                'type'  => 'app',
                'value' => 'repositories'
            );
        } elseif (preg_match('#^%kernel\.root_dir%/(.*)$#', $value, $vars)) {
            return array(
                'type'  => 'app',
                'value' => $vars[1]
            );
        } else {
            return array(
                'type'  => 'custom',
                'value' => $value
            );
        }
    }

    public function reverseTransform($value)
    {
        if (!is_array($value) || !isset($value['type']) || !isset($value['value'])) {
            throw new UnexpectedTypeException($value, 'array with keys type & value');
        }

        if ($value['type'] === 'app') {
            return '%kernel.root_dir%/'.$value['value'];
        } elseif ($value['type'] === 'custom') {
            return $value['value'];
        } else {
            throw new \RuntimeException('Unknown repository path type: '.$value['type']);
        }
    }
}

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

namespace Gitonomy\Component\Buzz\Message;

use Buzz\Message\Response;

/**
 * Json Response
 *
 * @author Julien DIDIER <genzo.wm@gmail.com>
 */
class JsonResponse extends Response
{
    protected $json;

    public function getJsonContent()
    {
        if (null !== $this->json) {
            return $this->json;
        }

        $content = $this->getContent();

        if (null == $content) {
            return null;
        }

        $this->json = json_decode($content, true);

        return $this->json;
    }
}

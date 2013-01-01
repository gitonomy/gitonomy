<?php

namespace Gitonomy\Component\Buzz\Message;

use Buzz\Message\Response;

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

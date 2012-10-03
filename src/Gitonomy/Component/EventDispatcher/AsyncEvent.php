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

namespace Gitonomy\Component\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class AsyncEvent
{
    protected $signature;
    protected $eventName;
    protected $event;

    public function __construct($eventName, Event $event, $signature = null)
    {
        $this->signature = $signature ?: $this->createSignature();
        $this->eventName = $eventName;
        $this->event     = $event;
    }

    public function getEventName()
    {
        return $this->eventName;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    protected function createSignature()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

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

namespace Gitonomy\Bundle\CoreBundle\Mailer;

/**
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class DebugMailer extends Mailer
{
    protected $counter = 0;

    public function getMessageCount()
    {
        return $this->counter;
    }

    public function mail($to, $template, $context = array())
    {
        $this->counter++;

        return parent::mail($to, $template, $context);
    }
}

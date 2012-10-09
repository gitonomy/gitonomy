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

require_once __DIR__.'/../vendor/autoload.php';

use Gitonomy\Component\Requirements\GitonomyRequirements;

$requirement = new GitonomyRequirements();

if ($requirement->isValid()) {
    echo "All indicators are in the green, looks fine\n";
    echo "\n";
    echo "Please web-access file install.php in a browser\n";

    return;
}

echo "Roger, we have problems:\n";

foreach ($requirement->getErrors() as $error) {
    $msg = $error->getRequirement();
    echo " - $msg\n";
}

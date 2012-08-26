<?php

require_once __DIR__.'/../vendor/autoload.php';

$requirement = new Gitonomy\Component\Requirements\GitonomyRequirements();
if ($requirement->isValid()) {
    echo "All indicators are in the green, looks fine\n";

    return;
}

echo "Roger, we have problems:\n";

foreach ($requirement->getErrors() as $error) {
    $msg = $error->getRequirement();
    echo " - $msg\n";
}

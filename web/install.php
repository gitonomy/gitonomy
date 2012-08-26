<?php

require_once __DIR__.'/../app/bootstrap.php.cache';

use Symfony\Component\HttpFoundation\Request;
use Gitonomy\Component\Requirements\GitonomyRequirements;

$requirements = new GitonomyRequirements();

if ($requirements->isValid()) {
    echo '<p>Everything is OK, <a href="app_dev.php/install/welcome">continue</a>.</p>';

    exit;
}


echo '<p>Errors found:</p>';
echo '<ul>';
foreach ($requirements->getErrors() as $error) {
    echo '<li>'.$error->getRequirement().'</li>';
}
echo '</ul>';

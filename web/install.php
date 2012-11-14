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

if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die;
}

require_once __DIR__.'/../app/bootstrap.php.cache';

use Symfony\Component\HttpFoundation\Request;
use Gitonomy\Component\Requirements\WebGitonomyRequirements;

$requirements = new WebGitonomyRequirements();

?>
<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="bundles/gitonomydistribution/css/main.css" />
    </head>
    <body>
        <div class="gitonomy-install">
            <header>
                <h1>Gitonomy <small>requirements</small></h1>
            </header>
            <section>
                <?php
                    if ($requirements->isValid()) {
                        echo '<p>Everything is OK</p>';
                        echo '<p class="welcome-buttons"><a class="btn" href="app_dev.php/install">Continue &raquo;</a>.</p>';
                    } else {
                        echo '<p>Errors found:</p>';
                        echo '<ul>';
                        foreach ($requirements->getErrors() as $error) {
                            echo '<li>'.$error->getRequirement().'</li>';
                        }
                        echo '</ul>';
                    }
                ?>
            </section>
            <footer>
                <p>Gitonomy is beautiful and you are beautiful, too.</p>
            </footer>
        </div>
    </body>
</html>

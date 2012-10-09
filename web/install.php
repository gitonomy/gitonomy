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
                    require_once __DIR__.'/../app/bootstrap.php.cache';

                    use Symfony\Component\HttpFoundation\Request;
                    use Gitonomy\Component\Requirements\WebGitonomyRequirements;

                    $requirements = new WebGitonomyRequirements();

                    if ($requirements->isValid()) {
                        echo '<p>Everything is OK</p>';
                        echo '<p class="welcome-buttons"><a class="btn" href="app_dev.php/install/welcome">Continue &raquo;</a>.</p>';
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

# Gitonomy

[![Build Status](https://secure.travis-ci.org/gitonomy/gitonomy.png)](https://travis-ci.org/gitonomy/gitonomy)


Gitonomy is a git repository management solution. It acts as a git server and
offer you a web-interface to manage and browse your repositories.

  * download: http://gitonomy.com/download
  * documentation: http://gitonomy.com/doc/gitonomy/master
  * demo: http://gitonomy.com/demo
  * backlog: https://trello.com/b/j53zyw57

## How to install?

Go to the [download page](http://gitonomy.com/download) and get the last stable
release of Gitonomy.

Uncompress the archive, and you will have a project with this structure:

    app/
    src/
    vendor/
    web/
    README.md
    install.sh

Put this folder where-ever you want and make your web-server use the `web/`
folder as document root.

You need to make sure that the application has full write-access to git
repositories, even via web.

Two front controllers are used for administration: `app_dev.php` and
`install.php`. Those two files, as default, are secured to only accept
connections from localhost.

If you want to setup the application remotely, edit those files to fit with your
policy. It's your responsability to secure those scripts. When you're done,
access http://localhost/install.php and continue with step-by-step.

When it's done, you need to setup `CRONTAB` for recurring tasks:

    * * * * * php /path/to/gitonomy/app/console authorized:keys -i > ~/.ssh/authorized_keys

That's it, your are now ready to use Gitonomy.

## How to contribute?

If you are a developer and plan to contribute on Gitonomy, you need to checkout
code on your computer and run the `reset.sh` script located at root of
repository:

    git clone git@github.com:gitonomy/gitonomy.git gitonomy
    cd gitonomy
    ./reset.sh

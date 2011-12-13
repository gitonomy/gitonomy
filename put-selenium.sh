#!/bin/bash
./app/console gitonomy:project-create Selenium selenium
if [ -d app/cache/repositories/projects/selenium.git ]; then
    rm -Rf app/cache/repositories/projects/selenium.git
fi
mkdir -p app/cache/repositories/projects
if [ -d /var/www/PHPSelenium2 ]; then
    git clone --bare /var/www/PHPSelenium2 app/cache/repositories/projects/selenium.git
fi


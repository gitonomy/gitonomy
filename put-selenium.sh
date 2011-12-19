#!/bin/bash
if [ -d app/cache/repositories/selenium.git ]; then
    rm -Rf app/cache/repositories/selenium.git
fi
mkdir -p app/cache/repositories
if [ -d /var/www/PHPSelenium2 ]; then
    ./app/console gitonomy:project-create --main-branch=webdriver Selenium selenium
    git clone --bare /var/www/PHPSelenium2 app/cache/repositories/selenium.git
    ./app/console gitonomy:user-role-create alex   "Lead developer" selenium
fi


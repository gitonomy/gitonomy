#!/bin/bash
php app/console doctrine:schema:create
php app/console doctrine:fixtures:load --append --fixtures=src/Gitonomy/Bundle/CoreBundle/DataFixtures/ORM/Load

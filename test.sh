#!/bin/bash
set -e
./reset.sh
bin/phpunit -c app/
bin/behat --stop-on-failure

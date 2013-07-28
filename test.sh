#!/bin/bash
set -e
./reset.sh
phpunit -c app/
bin/behat --stop-on-failure

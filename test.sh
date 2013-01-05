#!/bin/bash
set -e
./reset.sh test
phpunit -c app/


#!/bin/bash
./reset.sh test
phpunit -c app/
exit $?

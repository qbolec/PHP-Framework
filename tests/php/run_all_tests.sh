#!/bin/bash
for x in `find simple/ -name '*.php'`;do echo $x;run_php $x || exit 1;done
./pU phpunit

#!/bin/sh

# Run the tests from ./tests
#
# You can run all the test (that are not excluded)
#   ./runTests.sh .
# or a subset (often by directory)
#   ./runTests.sh basics/

clear
date
#cd tests

mkdir -p ./build/logs ./build/coverage/ #./logs/ ./tmp/

VERBOSE="--verbose "   # --testdox --debug
#COVERAGE="--coverage-html=build/coverage"
COLORS="--colors"
# config run by default, includes bootstrap
CONF="--configuration app/phpunit.xml.dist  -d memory_limit=1024M"
# Setting exclude-group here overrides the config
#GROUP=" --group __nogroup__"
#GROUP=" --group only"
#GROUPEXCLUDE=" --exclude-group huge"  #,large,proved,webtest

# Use the phpunit brought in by Composer
PHPUNIT="vendor/bin/phpunit"

time \
  $PHPUNIT $CONF $GROUP $GROUPEXCLUDE $COLORS $VERBOSE $MORE $MORE2 $COVERAGE $TEST

# http://stackoverflow.com/questions/911168/how-to-detect-if-my-shell-script-is-running-through-a-pipe
if [ -t 1 ] ; then
    # we are running in a TTY - under human control. Allow easy running again
    echo "#$PHPUNIT $GROUP $COLORS $VERBOSE $CONF $MORE $COVERAGE $TEST"
    echo ""
    echo ""
    echo -n "press [Enter] to re-run:> "
    read x
    #cd ..

    exec $0 $@
fi

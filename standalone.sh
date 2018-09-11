#!/bin/sh
echo '##########################################################'
echo '##                                                      ##'
echo '##    Welcome to grima!                                 ##'
echo '##                                                      ##'
echo '##    Open your browser to http://localhost:32900/      ##'
echo '##                                                      ##'
echo '##########################################################'
echo ''
DATABASE_URL=sqlite:$PWD/standalone-config.sql php -S 0.0.0.0:32900

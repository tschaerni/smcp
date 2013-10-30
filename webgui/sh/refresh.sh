#!/bin/bash

BINPHP=/usr/bin/php5
CHATLOGFILE=/var/www/feedupyourbeast.de/chat/inc/logs.php

while :
do

$BINPHP $CHATLOGFILE

sleep 3s
done
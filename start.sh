#!/bin/bash
trap "kill -TERM -$$" INT

BASEDIR=$(dirname `readlink -f $0`)
source $BASEDIR/smcp.conf
source $BASEDIR/functionlib.sh
PID=$$
SCRIPTNAME=$(basename $0)
PIDFILE=/tmp/starmade$PORT.pid

currentSecs(){
	dt=$(date +%Y-%m-%d\ %H:%M:%S)
	echo $(date --date="$dt" +%s)
}

shutdowncmd(){
	echo "$(timestamp) Initiate user-shutdown." | tee -a $doglog
	echo "stop $(currentSecs)" > $STATE
	echo "" > $ADMINS
	rm $LOCK
	rm $PIDFILE
	screen -S $SCREENSESSION -X kill
	exit 0
}

cd $BASEDIR/server
doglog=$BASEDIR/server/logs/watchdog.log

# PIDfile check
# ENDEXECUTION, if 1, stop script, if 0 proceed.
ENDEXECUTION=0

if [ -f "$PIDFILE" ] ; then

	RUNNINGPID=$(cat "$PIDFILE")
	PROGRAMPID=$(ps ax | grep "$SCRIPTNAME" | grep -v grep | awk '{print $1;}')

	for PIDEL in $PROGRAMPID
	do

		if [ "$PIDEL" == "$RUNNINGPID" ] ; then

			ENDEXECUTION=1
			break

		fi

    done

fi

if [ "$ENDEXECUTION" == "1" ] ; then

	echo "It is already a server running on port $PORT. Finish..."
	exit 1

fi
# write PID in pidfile
echo $PID > $PIDFILE

while true; do

	if [ -f $LOCK ] ; then

		shutdowncmd

	else

		echo "start $(currentSecs)" > $STATE
		echo "$(timestamp) Running command: '$cmd'" | tee -a $doglog
		$cmd
		echo "$(timestamp) Server 'StarMade' crashed with exit code $?.  Restarting..." | tee -a $doglog
		echo "" > $ADMINS

	fi

	sleep 1

done

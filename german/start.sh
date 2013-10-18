#!/bin/bash
trap "kill -TERM -$$" INT

BASEDIR=$(dirname `readlink -f $0`)
STATE=$BASEDIR/state
LOCK=$BASEDIR/shutdown.lock
ADMINS=$BASEDIR/server/admins.txt
SCREENSESSION=starmade
PORT=2424
PID=$$
SCRIPTNAME=`basename $0`
PIDFILE=/tmp/starmade$PORT.pid
cmd="ionice -c2 -n0 nice -n -10 rlwrap java -Xms512m -Xmx3g -XX:ParallelGCThreads=4 -d64 -jar StarMade.jar -server -port:$PORT"

function currentSecs {
	dt=`date +%Y-%m-%d\ %H:%M:%S`
	echo `date --date="$dt" +%s`
}

function shutdowncmd {
	echo "`timestamp` Initiate user-shutdown." | tee -a $log
	echo "stop `currentSecs`" > $STATE
	echo "" > $ADMINS
	rm $LOCK
	rm $PIDFILE
	screen -S $SCREENSESSION -X kill
	exit 0
}

cd $BASEDIR/server
log=$BASEDIR/server/logs/watchdog.log

function timestamp {
	echo "`date +%d.%m.%y` `date +%H:%M:%S`"
}

# PIDfile abfrage
# ENDEXECUTION, ist 1, stoppe Skript, ist 0, fahre fort
ENDEXECUTION=0

if [ -f "$PIDFILE" ] ; then

	RUNNINGPID=`cat "$PIDFILE"`
    PROGRAMPID=`ps ax | grep "$SCRIPTNAME" | grep -v grep | awk '{print $1;}'`
    
    for PIDEL in $PROGRAMPID
    do
	
		if [ "$PIDEL" == "$RUNNINGPID" ] ; then
		
            ENDEXECUTION=1
            break
    
		fi
		
    done

fi

if [ "$ENDEXECUTION" == "1" ] ; then

    echo "Es läuft schon ein StarMade Server auf Port $PORT. Beende..."
    exit 1
    
fi
# schreibe PID ins pidfile
echo $PID > $PIDFILE

while true; do

    echo "start `currentSecs`" > $STATE
    echo "`timestamp` Running command: '$cmd'" | tee -a $log
    $cmd
    if [ -f $LOCK ] ; then
		
		shutdowncmd
    
    else
        
    	echo "`timestamp` Server 'StarMade' crashed with exit code $?.  Restarting..." | tee -a $log
		echo "" > $ADMINS
    
    fi
    
		sleep 1

done

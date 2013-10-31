#!/bin/bash
BASEDIR=$(dirname `readlink -f $0`)
source $BASEDIR/smcp.conf

# functions

restart(){

	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat        ATTENTION ATTENTION!!!$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat    Server-Restart in 5 minutes$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	echo "restart in: 5min"
	sleep 60

	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat preparation warp-core shutdown$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat    Server-Restart in 4 minutes$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	echo "restart in: 4min"
	sleep 60

	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat        reduce speed!$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat     Server-Restart in 3 minutes$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	echo "restart in: 3min"
	sleep 60

	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat     engine shutdown!$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat    Server-Restart in 2 minutes$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	echo "restart in: 2min"
	sleep 60

	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat       all engines: STOP!$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat  Server-Restart in 60 seconds$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/shutdown 60$(printf \\r)"
	echo "restart in: 60s"
	sleep 60

	echo "restart was performed, please check..."
	sleep 5

}

shutdowncmd(){
	touch $LOCK
	screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server for Backup or Update in 2 minutes!$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/force_save$(printf \\r)"
	echo "Initiate shutdown. Time left: 120s"
	sleep 60
	screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server Backup or Update in 60 seconds!$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/shutdown 60$(printf \\r)"
	echo "Shutdown in 60s"
	sleep 30
	echo "Shutdown in 30s"
	sleep 30
	echo "Shutdown was performed, please check..."
	sleep 5
}

serverAlive(){

	status=$(echo "" | netcat -v -w 1 localhost $PORT 2>&1|tail -1|awk '{print $5}')

	#status=$(tcptraceroute -S -w 10 localhost $PORT 2> /dev/null|tail -1|awk '{print $4}')
	if [ "$status" == "open" ]; then

		echo online

	else

		echo offline

	fi

}

players(){

	probe="\x00\x00\x00\x09\x2a\xff\xff\x01\x6f\x00\x00\x00\x00"
	echo -n -e "$probe" | netcat -o $BASEDIR/packet/hex.tmp -v -w 1 localhost $PORT > /dev/null 2>&1
	xxd -r $BASEDIR/packet/hex.tmp > $BASEDIR/packet/bin.tmp
	xxd -p $BASEDIR/packet/bin.tmp | paste -sd '' > $BASEDIR/packet/hex.tmp
	usersHex=$(cat $BASEDIR/packet/hex.tmp | awk '{print substr ($0, length($0)-11, 2)}')
	users=$(echo "ibase=16;obase=A;${usersHex^^}" | bc)
	echo "$users player"
}

timestamp(){

	echo "$(date +%d.%m.%y) $(date +%H:%M:%S)"
}

mobcount(){

	MOB=$(ls -l $BASEDIR/server/server-database/ | grep ENTITY_SHIP_MOB | wc -l)
	if [ $MOB = 0 ] ; then

		echo "no"

	else

		echo "$MOB"

	fi
}

cleanmob(){

	screen -S $SCREENSESSION -p 0 -X stuff "/force_save$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat prepare for lag spike$(printf \\r)"
	sleep 5
	screen -S $SCREENSESSION -p 0 -X stuff "/despawn_all MOB_ unused true$(printf \\r)"
	echo "$(timestamp) Despawn all Mobs." | tee -a $LOG«

}

# PID grep, for the StarMade Server
pid(){

		user=$(whoami)
		#pids=$(pidof java)
		pids=$(ps aux | grep java | grep StarMade.jar | grep $PORT | grep $user | grep -v rlwrap | awk -F" " '{print $2}')
		echo "$pids"

}

#distinctive case
case $1 in

	start)	# start

		if [[ -z $(screen -ls | grep $SCREENSESSION | grep tached) ]] ; then
			# session doesn't exist
			echo "Starting screen session '$SCREENSESSION'..."
			screen -d -m -S $SCREENSESSION
			echo "now, i'm just crawling under the couch to find the start.sh"
			sleep 1
			echo "aaaand..."
			screen -S $SCREENSESSION -p 0 -X stuff "$BASEDIR/start.sh$(printf \\r)"
			echo "...I found it! Now, starting..."
			sleep 1
			echo "Starmade Server is now running in screen session '$SCREENSESSION', you can attach the session with 'screen -r $SCREENSESSION'."
			sleep 1
			echo "My job is done. Greetings from Zodiak!"

		else

			echo "There is already a running '$SCREENSESSION' screen session, please check..."
			sleep 5

		fi
		;;

	stop)	# shutdown
			touch $LOCK
			screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server for Backup or Update in 2 minutes!$(printf \\r)"
			screen -S $SCREENSESSION -p 0 -X stuff "/force_save$(printf \\r)"
			echo "Initiate shutdown. Time left: 120s"
			sleep 60
			screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server Backup or Update in 60 seconds!$(printf \\r)"
			screen -S $SCREENSESSION -p 0 -X stuff "/shutdown 60$(printf \\r)"
			echo "Shutdown in 60s"
			sleep 30
			echo "Shutdown in 30s"
			sleep 30
			echo "Shutdown was performed, please check..."
			sleep 5
		;;

	restart)	# restart
		restart
	;;

	status)	# status
		echo "Server is $(serverAlive) with $(players) of max $MAXPLAYERS players."
	;;

	update)	# update
		java -jar StarMade-Starter.jar -nogui
	;;  

	emergency)	# emergency shutdown
		kill $SMPID
	;;

	kill)	# kill the server process
		echo "execute 'kill -9 $SMPID'"
		kill -9 $SMPID
	;;

	mobs)
		echo "There are $(mobcount) mobs in the database."
	;;

	cleanmob) # mob clean
		cleanmob
	;;

	screen)	# reattach the screensession
		screen -rx $SCREENSESSION
	;;

	dbsize)	# Database size
		du -sch $BASEDIR/server/server-database/ | tail -n 1
	;;

	command)	# send command
		screen -S $SCREENSESSION -p 0 -X stuff "$2 $(printf \\r)"
	;;

	msg)	# send message
		screen -S $SCREENSESSION -p 0 -X stuff "/chat $2 $(printf \\r)"
	;;

	admins)
		cat $ADMINS
	;;

	addadmin)	# make admin
		screen -S $SCREENSESSION -p 0 -X stuff "/add_admin $2 $(printf \\r)"
	;;

	rmadmin)	# remove admin
		screen -S $SCREENSESSION -p 0 -X stuff "/remove_admin $2 $(printf \\r)"
	;;

	addwhite)	# add a name to the whitelist
		screen -S $SCREENSESSION -p 0 -X stuff "/whitelist_name $2 $(printf \\r)"
	;;

	editwhite) # edit whitelist
		$EDITOR $BASEDIR/server/whitelist.txt
	;;

	editcfg)	# edit server.cfg
		$EDITOR $BASEDIR/server/server.cfg
	;;

	editcron)	# edit crontab
		crontab -e
	;;

	editblack)	# edit blacklist
		$EDITOR $BASEDIR/server/blacklist.txt
	;;

	editmotd)	# edit welcome message
		$EDITOR $BASEDIR/server/server-message.txt
	;;

	exit)	# exit the script
		exit 0
	;;

	*) # i think this functions is clear like water ;)
		exit 0
	;;
# end of case
esac

exit 0

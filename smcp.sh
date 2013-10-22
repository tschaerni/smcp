#!/bin/bash
#	StarMade Control Panel V1.1
#
#	10.10.2013 - Erstellt
#	11.10.2013 - PID ueberpruefung eingebaut
#	13.10.2013 - Release
#	13.10.2013 - screen session überprüfung einkommentiert	
#
#	Copyright 2013 Robin Cerny <tschaerni@gmail.com> or <robin@cerny.li>
#
#	This program is free software; you can redistribute it and/or modify
#	it under the terms of the GNU General Public License as published by
#	the Free Software Foundation; either version 2 of the License or
#	any later version.
#
#	This program is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#	GNU General Public License for more details.
#
#	You should have received a copy of the GNU General Public License
#	along with this program; if not, write to the Free Software
#	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
#	MA 02110-1301, USA.


# Settings
BASEDIR=$(dirname `readlink -f $0`)
SMCPVERSION=1.1
SMVERSION=$(cat $BASEDIR/server/version.txt)
SCREENSESSION=starmade
EDITOR=nano
#EDITOR=vi
PORT=4242
MAXPLAYERS=96
LOG=$BASEDIR/server/logs/serverlog.txt.0
LOCK=$BASEDIR/shutdown.lock
PID=$$
SCRIPTNAME=$(basename $0)
PIDFILE=/tmp/$SCRIPTNAME.pid

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

serverAlive()
{
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

	screen -S $SCREENSESSION -p 0 -X stuff "/despawn_all MOB unused true$(printf \\r)"«
	echo "$(timestamp) Despawn all Mobs." | tee -a $LOG«

}
# mobclean over parameter
if [ "$1" = "-c" ] ; then

	cleanmob
	exit 0

else

	# restart over parameter
	if [ "$1" = "-r" ] ; then

		restart
		exit 0

	else

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

			echo "The StarMade Control Panel V$SMCPVERSION is already running. Abort..."
			exit 1

		fi
		# write PID in pidfile
		echo $PID > $PIDFILE

	# end fi for 145
	fi

# end if for 137
fi
#versioncheck 
CURRENTSMCPVERSION=$(curl --silent http://smcp.cerny.li/version)
comparateversion=$(echo $CURRENTSMCPVERSION'>'$SMCPVERSION | bc -l)
if [ $comparateversion == 1 ] ; then

	clear
	echo -e "\nThere is a new version available!\n\nThere can be found on http://smcp.cerny.li/"
	sleep 10

fi

# PID grep, for the StarMade Server
pid(){

		user=$(whoami)
		pids=$(ps aux | grep java | grep StarMade.jar | grep $PORT | grep $user | grep -v rlwrap | awk -F" " '{print $2}')
		echo "$pids"

}

# Menu
while true; do

# refresh starmade pid
SMPID=$(pid)

clear

echo ""
echo -e "\e[31m		   _____\e[34m____ ___  _________       \e[0m"
echo -e "\e[31m		  / ___/\e[34m __ '__ \/ ___/ __ \      \e[0m"
echo -e "\e[31m		 (__  ) \e[34m/ / / / / /__/ /_/ /      \e[0m"
echo -e "\e[31m		/____/\e[34m_/ /_/ /_/\___/ .___/       \e[0m"
echo -e "\e[34m		                   /_/            \e[0m"
echo -e "
            StarMade Control Panel V$SMCPVERSION by \e[31mZ\e[34modiak\e[0m

StarMade Version: 		$SMVERSION
PID of the StarMade Server:	$SMPID

 [1]	start		Start the StarMade Server
 [2]	stop		Stop the StarMade Server
 [3]	restart		Restart the StarMade Server
 [4]	status		Retrieves the status of the server
 [5]	update		Execute an update
 [6]	emerg		Try a 'emergency shutdown'
 [7]	kill		Kills the StarMade server with violent
 [8]	mobclean	Deletes all 'mob' records from the database
 [9]	reattach	Reattach the screensession '$SCREENSESSION'
 [10]	DBSize		Shows the current size of the database
 [11]	order		Send a custom command to the StarMade server
 [12]	message		Send a custom message to all players
 [13]	mkadmin		Make a player admin
 [14]	rmadmin		Remove a admin
 [15]	mkwhite		Add a Player to the whitelist
 [16]	editwhite	Edit the whitelist
 [17]	editconfig	Edit the server.cfg
 [18]	editcron	Edit the crontab
 [19]	editblack	Edit the blacklist
 [20]	editmsg		Edit the welcome message

 [r]	reload		Press 'r' to update the Control Panel
 [0]	abort		Return to the shell\n"

read -p "choice: " answer

case $answer in

	1)	# start

		#if [[ -z $(screen -ls $SCREENSESSION | grep $SCREENSESSION) ]] ; then
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

	    #else

			#echo "It is already a running '$SCREENSESSION' screen session, please check..."
			#sleep 5

		#fi
		;;

	2)	# shutdown

		read -p "Should the server be shut down? y/n: " stopanswer
		if [ "$stopanswer" = "y" ] ; then

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

		else

			echo "Abort..."
			sleep 5

		fi
		;;

	3)	# restart

		read -p "Should the server be restarting? y/n: " restartanswer
		if [ "$restartanswer" = "y" ] ; then

			restart

		else

		echo "Abort..."
		sleep 5

		fi
		;;

	4)	# status

		echo "Server is $(serverAlive) with $(players) of max $MAXPLAYERS players."
		sleep 8
		;;

	5)	# update
	
		echo "It is recommended before updating to make a backup!"
		read -p "Should the server be updating? y/n: " updateanswer
		if [ "$updateanswer" = "y" ] ; then

			read -p "Should the server be shut down? y/n: " stopanswer
			if [ "$stopanswer" = "y" ] ; then

				touch $LOCK
				screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server for Backup or Update in 2 minutes!$(printf \\r)"
				screen -S $SCREENSESSION -p 0 -X stuff "/force_save$(printf \\r)"
				echo "Initiate shutdown. Time left: 120s"
				sleep 60
				screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server for Backup or Update in 60 seconds!$(printf \\r)"
				screen -S $SCREENSESSION -p 0 -X stuff "/shutdown 60$(printf \\r)"
				echo "Shutdown in 60s"
				sleep 30
				echo "Shutdown in 30s"
				sleep 30
				echo "Shutdown was perfomed. Please check..."
				echo "Checking server status..."
				sleep 1

				if [ "$(serverAlive)" = "offline" ] ; then

					echo "Server has been shut down properly. Proceed with the update..."
					sleep 3
					java -jar StarMade-Starter.jar -nogui
					sleep 15

				else

					echo "Server was not shut down. Please Check! Abort update..."
					sleep 5

				fi

			else

				echo "Abort..."
				sleep 5

			fi

		else

			echo "Abort..."
			sleep 5

		fi
		;;  

	6)	# emergency shutdown

		read -p "Should be a 'emergency shutdown' performed? y/n: " emerganswer
		if [ "$emerganswer" = "y" ] ; then

			echo "Send termination signal (SIGTERM)"
			sleep 1
			kill $SMPID
			echo "Was performed, please check..."
			sleep 5

		else

			echo "Abort..."
			sleep 5

		fi
		;;

	7)	# kill the server process

		echo -e "\n\e[31m\e[4mCAUTION!!! When using this function, it IS LOST DATA!\e[0m"
		read -p "Really proceed? If yes, write: Yes, I want to continue! : " killanswer
		if [ "$killanswer" = "Yes, I want to continue!" ] ; then

			echo "Grab the sledgehammer..."
			sleep 2
			echo "Beat the process down..."
			sleep 1
			echo "*dong*"
			sleep 1
			echo "*dong*"
			sleep 2
			echo "*klirr*"
			sleep 1
			echo "execute 'kill -9 $SMPID'"
			kill -9 $SMPID
			sleep 1
			echo "Termination of the process is successfully, restarting..."
			sleep 4

	    else

			echo "Wrong answer, abort..."
			sleep 5

		fi
		;;

	8) # mob clean

		screen -S $SCREENSESSION -p 0 -X stuff "/force_save$(printf \\r)"
		sleep 10
		echo "$(mobcount) records of mobs where found in the database."
		read -p "Proceed with the deletion? [y/n]:" answer
		if [ "$answer" = "y" ] ; then

			echo "Deleting Mob records..."
			screen -S $SCREENSESSION -p 0 -X stuff "/despawn_all MOB unused true$(printf \\r)"
			echo "$(timestamp) Despawn all Mobs." | tee -a $LOG
			sleep 5
			echo "Performed deletion. $(mobcount) records of mobs where found in the database."
			sleep 5

		else

			echo "Abort..."
			Sleep 3

		fi
		;;

	9)	# reattach the screensession

		screen -rx $SCREENSESSION
		sleep 2
		;;

	10)	# Database size

		echo "calculate..."
		sleep 1
		du -sch $BASEDIR/server/server-database/ | tail -n 1
		sleep 5
		;;

	11)	# send command

		echo -e "All StarMade commands are possible. Caution! no feedback .\nExample: /force_save"
		read -p "enter the command: " order
		screen -S $SCREENSESSION -p 0 -X stuff "$order $(printf \\r)"
		sleep 2
		;;

	12)	# send message

		echo "Attention, only one row is possible!"
		read -p "Broadcast Message: " msg
		screen -S $SCREENSESSION -p 0 -X stuff "/chat $msg $(printf \\r)"
		sleep 2
		;;

	13)	# make admin

		echo "current admins:"
		cat $BASEDIR/server/admins.txt
		echo ""
		echo "Pay attention to upper/lower case."
		read -p "Type username: " mkadmin
		screen -S $SCREENSESSION -p 0 -X stuff "/add_admin $mkadmin $(printf \\r)"
		sleep 5
		echo "current admins:"
		cat $BASEDIR/server/admins.txt
		sleep 5
		;;

	14)	# remove admin

		echo "current admins:"
		cat $BASEDIR/server/admins.txt
		echo ""
		echo "Pay attention to upper/lower case."
		read -p "Type username: " rmadmin
		screen -S $SCREENSESSION -p 0 -X stuff "/remove_admin $rmadmin $(printf \\r)"
		sleep 5
		echo "current admins:"
		cat $BASEDIR/server/admins.txt
		sleep 5
		;;

	15)	# add a name to the whitelist

		echo "Pay attention to upper/lower case."
		read -p "Type username: " mkwhite
		screen -S $SCREENSESSION -p 0 -X stuff "/whitelist_name $mkwhite $(printf \\r)"
		sleep 1
		;;

	16) # edit whitelist

		echo "open file..."
		sleep 2
		$EDITOR $BASEDIR/server/whitelist.txt
		sleep 1
		;;

	17)	# edit server.cfg

		echo "open file..."
		sleep 2
		$EDITOR $BASEDIR/server/server.cfg
		sleep 1
		read -p "Should the server be restarting? [y/n]: " restartanswer
		if [ "$restartanswer" = "y" ] ; then
			restart
		else
			echo "Don't restart. The server settings are read again until the next restart."
			sleep 5
		fi
		;;

	18)	# edit crontab

	    crontab -e
	    sleep 3
	    ;;

	19)	# edit blacklist

		echo "open file..."
		sleep 2
		$EDITOR $BASEDIR/server/blacklist.txt
		sleep 1
		;;

	20)	# edit welcome message

		echo "openfile..."
		sleep 2
		$EDITOR $BASEDIR/server/server-message.txt
		sleep 1
		;;

	0)

		echo "Exit the StarMade Control Panel..."
		sleep 1
		break
		;;

	r)

		echo "reload"
		sleep 1 
		;;

	*)

	    echo "unknown parameter, return back to the menu"
	    sleep 1

# end of case
esac
# end of pid check
done
# rm pidfile
rm $PIDFILE

exit 0

#!/bin/bash
# 	StarMade Control Panel V1.0
# 	
#	10.10.2013 - Erstellt
# 	11.10.2013 - PID ueberpruefung eingebaut
#	13.10.2013 - Release	
#
#	Copyright 2013 Robin Cerny <tschaerni@gmail.com> or <robin@cerny.li>
#
#	This program is free software; you can redistribute it and/or modify
#	it under the terms of the GNU General Public License as published by
#	the Free Software Foundation; either version 2 of the License.
#
#	This program is distributed in the hope that it will be useful,
#	but WITHOUT ANY WARRANTY; without even the implied warranty of
#	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#	GNU General Public License for more details.
#
#   	You should have received a copy of the GNU General Public License
#	along with this program; if not, write to the Free Software
#   	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
#   	MA 02110-1301, USA.


# Settings
BASEDIR=$(dirname `readlink -f $0`)
SMCPVERSION=1.0
SMVERSION=$(cat $BASEDIR/server/version.txt)
SCREENSESSION=starmade
EDITOR=nano
#EDITOR=vi
PORT=2424
MAXPLAYERS=96
LOG=$BASEDIR/server/logs/serverlog.txt.0
LOCK=$BASEDIR/shutdown.lock
PID=$$
SCRIPTNAME=`basename $0`
PIDFILE=/tmp/$SCRIPTNAME.pid

# functions
function restart {
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat        ACHTUNG ACHTUNG!!!$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat    Server-Restart in 5 Minuten$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	echo "Neustart in: 5min"
	sleep 60
	
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat Vorbereitung Warpkern-Abschaltung$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat    Server-Restart in 4 Minuten$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	echo "Neustart in: 4min"
	sleep 60
	
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat    Geschwindigkeit reduzieren!$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat     Server-Restart in 3 Minuten$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	echo "Neustart in: 3min"
	sleep 60
	
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat     Triebwerke runterfahren!$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat    Server-Restart in 2 Minuten$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	echo "Neustart in: 2min"
	sleep 60
	
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat         Maschinen alle STOP$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat  Server-Restart in 60 Sekunden$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/chat ##########################$(printf \\r)"
	screen -S $SCREENSESSION -p 0 -X stuff "/shutdown 60$(printf \\r)"
	echo "Neustart in: 60s"
	sleep 60
	
	echo "Restart wurde durchgefuehrt, bitte ueberpruefen..."
	sleep 5
}

function serverAlive {
	status=`echo "" | netcat -v -w 1 localhost $PORT 2>&1|tail -1|awk '{print $5}'`

	#status=`tcptraceroute -S -w 10 localhost $PORT 2> /dev/null|tail -1|awk '{print $4}'`
	if [ "$status" == "open" ]; then
		echo online
	else
		echo offline
	fi
}

function players {
	probe="\x00\x00\x00\x09\x2a\xff\xff\x01\x6f\x00\x00\x00\x00"
	echo -n -e "$probe" | netcat -o $BASEDIR/packet/hex.tmp -v -w 1 localhost $PORT > /dev/null 2>&1

	xxd -r $BASEDIR/packet/hex.tmp > $BASEDIR/packet/bin.tmp
	xxd -p $BASEDIR/packet/bin.tmp | paste -sd '' > $BASEDIR/packet/hex.tmp
	usersHex=`cat $BASEDIR/packet/hex.tmp | awk '{print substr ($0, length($0)-11, 2)}'`
	users=`echo "ibase=16;obase=A;${usersHex^^}" | bc`
	echo "$users Spielern"
}

function timestamp {
	echo "`date +%d.%m.%y` `date +%H:%M:%S`"
}

function mobcount {
	MOB=$(ls -l $BASEDIR/server/server-database/ | grep ENTITY_SHIP_MOB | wc -l)
	if [ $MOB = 0 ] ; then
		echo "keine"
	else
        echo "$MOB"
	fi
}

if [ "$1" = "-r" ] ; then
	
	restart
	exit 0
	
else
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

    	echo "Das StarMade Control Panel V$SMCPVERSION laeuft schon. Beende..."
    	exit 1
    	
	fi
	# schreibe PID ins pidfile
	echo $PID > $PIDFILE
fi

#versioncheck 
CURRENTSMCPVERSION=$(curl --silent http://smcp.cerny.li/version)
comparateversion=$(echo $CURRENTSMCPVERSION'>'$SMCPVERSION | bc -l)
if [ $comparateversion == 1 ] ; then
	
	clear
	echo -e "\nEs ist eine neue Version vorhanden!\n\nDiese Findest du auf http://smcp.cerny.li/"
  	sleep 10

fi

# PID grep, fuer den StarMade Server
function pid {

        user=$(whoami)
        pids=$(ps aux | grep java | grep StarMade.jar | grep $PORT | grep $user | grep -v rlwrap | awk -F" " '{print $2}')
        echo "$pids"
}

# Menu
while true; do

SMPID=$(pid)

clear

echo -e "
StarMade Control Panel V$SMCPVERSION by  \e[31mZ\e[34modiak\e[0m
StarMade Version: 		$SMVERSION
PID des StarMade Servers:	$SMPID

 [1]  Start			Startet den StarMade Server
 [2]  Stop			Stoppt den StarMade Server
 [3]  Restart			Startet dem StarMade Server neu
 [4]  Status			Ruft den Status des Servers ab
 [5]  Update			Fuehre ein Update aus
 [6]  emerg			Versuche einen 'emergency shutdown'
 [7]  Kill			Toetet den StarMade Server mit Gewalt
 [8]  Mobclean			Loescht alle Mob Eintraege aus der Datenbank
 [9]  Reattach			Reattached die screen Session '$SCREENSESSION'
 [10] DBSize			Zeigt die aktuelle Groesse der Datenbank
 [11] Befehl			Sende einen Benutzerdefinierten Befehl an den StarMade Server
 [12] Message			Sende eine Benutzerdefinierte Nachricht an alle Spieler
 [13] mkadmin			Vergebe einem Spieler den Adminrang
 [14] rmadmin			Entferne einem Spieler den Adminrang
 [15] mkwhite			Setze einen Spieler auf die whitelist
 [16] editwhite			Editiere die Whitelist
 [17] editserver.cfg		Editiere die server.cfg
 [18] editcron			Editiere crontab
 [19] editblack			Editiere Blacklist
 [20] editmsg			Editiere Welcome Message

 [r]  reload			Druecke 'r' um das StarMade Control Panel zu aktualisieren
 [0]  Abbruch			Kehre zur Shell zurueck\n"

read -p "Auswahl: " answer

case $answer in

	1)
	
	    if [[ -z $(screen -ls | grep $SCREENSESSION) ]] ; then
			# session existiert nicht
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

			echo "Es laeuft schon eine Screen session, bitte ueberpruefen..."
			sleep 5

	    fi
		;;

	2)
	
		read -p "Soll der Server wirklich heruntergefahren werden? j/n: " stopanswer
		if [ "$stopanswer" = "j" ] ; then

			touch $LOCK
			screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server/Backup/Update in 2 Minuten!$(printf \\r)"
			screen -S $SCREENSESSION -p 0 -X stuff "/force_save$(printf \\r)"
			echo "Initiate shutdown. Time left: 120s"
			sleep 60
			screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server/Backup/Update in 60 Sekunden!$(printf \\r)"
			screen -S $SCREENSESSION -p 0 -X stuff "/shutdown 60$(printf \\r)"
			echo "Shutdown in 60s"
			sleep 30
			echo "Shutdown in 30s"
			sleep 30
            echo "Shutdown wurde durchgefuehrt, bitte ueberpruefen..."
			sleep 5

		else

			echo "Breche ab..."
			sleep 5

		fi
	    ;;

	3)
	
		read -p "Soll der Server wirklich neugestartet werden? j/n: " restartanswer
		if [ "$restartanswer" = "j" ] ; then

			restart

		else

		echo "Breche ab..."
		sleep 5

		fi
		;;

	4)
	
	    echo "Server ist `serverAlive` mit `players` von maximal $MAXPLAYERS Spielern"
		sleep 8
	    ;;
	    
	5)
	
		echo "Es wird empfohlen vor einem Update ein Backup zu machen"
		read -p "Soll der Server wirklich geupdatet werden? j/n: " updateanswer
		if [ "$updateanswer" = "j" ] ; then
			
			read -p "Soll der Server wirklich heruntergefahren werden? j/n: " stopanswer
            if [ "$stopanswer" = "j" ] ; then

				touch $LOCK
                screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server/Backup/Update in 2 Minuten!$(printf \\r)"
                screen -S $SCREENSESSION -p 0 -X stuff "/force_save$(printf \\r)"
                echo "Initiate shutdown. Time left: 120s"
                sleep 60
                screen -S $SCREENSESSION -p 0 -X stuff "/chat SHUTDOWN Server/Backup/Update in 60 Sekunden!$(printf \\r)"
                screen -S $SCREENSESSION -p 0 -X stuff "/shutdown 60$(printf \\r)"
                echo "Shutdown in 60s"
                sleep 30
                echo "Shutdown in 30s"
                sleep 30
                echo "Shutdown wurde durchgefuehrt"
                echo "ueberpruefe..."
                sleep 1
                
				if [ "$(serverAlive)" = "offline" ] ; then
				
					echo "Server wurde Ordnungsgemaess heruntergefahren. Fahre mit dem Update fort..."
					sleep 3
					java -jar StarMade-Starter.jar -nogui
					sleep 15
				
				else
				
					echo "Server wurde nicht heruntergefahren. Bitte ueberpruefen! Breche ab..."
					sleep 5
				
				fi

            else

                echo "Breche ab..."
                sleep 5
			
			fi

		else
		
			echo "Breche ab..."
			sleep 5

		fi
	    ;;  
	    
	6)
	
	    read -p "Soll ein 'emergency shutdown' durchgefuehrt werden? j/n: " emerganswer
	    if [ "$emerganswer" = "j" ] ; then

			echo "Schicke Terminierungssignal (SIGTERM)"
			sleep 1
			kill $SMPID
			echo "Wurde durchgefuehrt, bitte ueberpruefen..."
			sleep 5

	    else
		
			echo "Breche ab..."
			sleep 5

	    fi
	    ;;
	    
	7)
	
		echo -e "\n\e[31m\e[4mACHTUNG!!! Bei der Nutzung dieser Funktion WIRD es DATENVERLUST geben!\e[0m"
	    read -p "Wirklich fortfahren? Falls ja schreibe: Ja, ich will fortfahren! : " killanswer
		if [ "$killanswer" = "Ja, ich will fortfahren!" ] ; then
		
			echo "Packe den Vorschlaghammer aus..."
			sleep 2
			echo "Fange an auf den Prozess zu haemmern"
			sleep 1
			echo "*dong*"
			sleep 1
			echo "*dong*"
	        sleep 2
			echo "*klirr*"
			sleep 1
			echo "fuere 'kill -9 $SMPID' aus"
	    	kill -9 $SMPID
			sleep 1
			echo "Der Prozess wurde erfolgreich getoetet, starte neu..."
			sleep 4

	    else

			echo "Falsche Antwort, breche ab..."
			sleep 5

	    fi
		;;
		
	8)
	
	    screen -S $SCREENSESSION -p 0 -X stuff "/force_save$(printf \\r)"
	    sleep 10
	    echo "In der Datenbank wurden `mobcount` Eintraege zu Mobs gefunden."
	    read -p "Fortfahren mit der Loeschung? [j/n]:" answer
		if [ "$answer" = "j" ] ; then

			echo "Loesche Mobeintraege aus der Datenbank..."
			screen -S $SCREENSESSION -p 0 -X stuff "/despawn_all MOB unused true$(printf \\r)"
			echo "`timestamp` Despawn all Mobs." | tee -a $LOG
			sleep 5
			echo "Loeschung der Eintraege durchgefuehrt. Aktuell sind `mobcount` Eintraege zu Mobs vorhanden."
			sleep 5

	    else

			echo "Abbruch"
			Sleep 3

	    fi
	    ;;
	    
	9)
	
		screen -r $SCREENSESSION
	    sleep 2
        ;;
	
	10)

		echo "Berechne..."
	    sleep 1
	    du -sch $BASEDIR/server/server-database/ | tail -n 1
	    sleep 5
		;;
		
	11)
	
	    echo -e "Alle StarMade Befehle moeglich. Achtung! keine Rueckmeldung.\nBeispiel: /force_save"
	    read -p "Befehl eingeben: " order
	    screen -S $SCREENSESSION -p 0 -X stuff "$order $(printf \\r)"
	    sleep 2
		;;
	
	12)
	
	    echo "Achtung, keine Umlaute und nur eine Zeile Moeglich!"
	    read -p "Broadcast Message: " msg
	    screen -S $SCREENSESSION -p 0 -X stuff "/chat $msg $(printf \\r)"
	    sleep 2
		;;
		
	13) 
	
	    echo "Aktuelle Admins:"
	    cat $BASEDIR/server/admins.txt
	    echo ""
	    echo "Unbedingt auf Gross/kleinschreibung achten."
	    read -p "Benutzername eingeben: " mkadmin
	    screen -S $SCREENSESSION -p 0 -X stuff "/add_admin $mkadmin $(printf \\r)"
		sleep 5
		echo "Aktuelle Admins:"
		cat $BASEDIR/server/admins.txt
		sleep 5
		;;
       
	14)
    
		echo "Aktuelle Admins:"
		cat $BASEDIR/server/admins.txt
		echo ""
		echo "Unbedingt auf Gross/kleinschreibung achten."
		read -p "Benutzername eingeben: " rmadmin
		screen -S $SCREENSESSION -p 0 -X stuff "/remove_admin $rmadmin $(printf \\r)"
		sleep 5
		echo "Aktuelle Admins:"
		cat $BASEDIR/server/admins.txt
		sleep 5
		;;
		
	15)
		echo "Unbedingt auf Gross/kleinschreibung achten."
		read -p "Benutzername eingeben: " mkwhite
		screen -S $SCREENSESSION -p 0 -X stuff "/whitelist_name $mkwhite $(printf \\r)"
		sleep 1
		;;

	16)

		echo "Oeffne Datei..."
	    sleep 2
		$EDITOR $BASEDIR/server/whitelist.txt
		sleep 1
	    ;;

	17)

		echo "Oeffne Datei..."
		sleep 2
		$EDITOR $BASEDIR/server/server.cfg
		sleep 1
		read -p "Soll der Server neugestartet werden? [j/n]: " restartanswer
		if [ "$restartanswer" = "j" ] ; then
			restart
		else
			echo "Starte nicht neu. Die Server Settings werden erst beim naechsten Neustart wieder eingelesen."
			sleep 5
		fi
		;;
		
	18)
	
	    crontab -e
	    sleep 3
	    ;;
	
	19)
    
		echo "Oeffne Datei..."
		sleep 2
		$EDITOR $BASEDIR/server/blacklist.txt
		sleep 1
		;;

	20)
		echo "Oeffne Datei..."
		sleep 2
		$EDITOR $BASEDIR/server/server-message.txt
		sleep 1
		;;

	0)
	
		echo "Verlasse das StarMade Control Panel"
		sleep 1
	    break
		;;
    
	r)
    
        echo "reload"
        sleep 1 
		;;
	
	*)
	
	    echo "unbekannter Parameter, kehre zum Menu zurueck"
	    sleep 1
esac

done

rm $PIDFILE

exit 0

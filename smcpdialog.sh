#!/bin/bash

BASEDIR=$(dirname `readlink -f $0`)
#source $BASEDIR/smcp.conf 2
#source $BASEDIR/functionlib.sh 2

main_menu(){
	dialog --backtitle "SMCP - StarMade Control Panel" --title " Commandline interface - V$SMCPVERSION "\
		--cancel-label "Quit" \
		--menu "Move using [UP] [DOWN], [ENTER] to select" 20 60 13\
		manage "Submenu 'Server management'"\
		edit "Submenu 'File edit'"\
		mobs "Retrieves the number of mob entity"\
		cleanmob "Deletes all mobs from the Database"\
		screen "Reattach the screensession '$SCREENSESSION'"\
		dbsize "Shows the current size of the database"\
		command "send a custom command to the StarMade server"\
		msg "send a custom message to the StarMade server"\
		admins "Shows the current admins"\
		addadmin "Add an admin"\
		rmaddmin "Remove an admin"\
		addwhite "Add a Player to the whitelist"\
		exit "Exit the SMCP" 3>&1 1>&2 2>&3
}

manage_menu(){
	dialog --backtitle "SMCP - StarMade Control Panel" --title " Server Management "\
		--menu "Move using [UP] [DOWN], [ENTER] to select" 15 60 8\
		start "Start the StarMade server"\
		stop "Stop the StarMade server"\
		restart "Restart the StarMade server"\
		status "Retrieves the status of the Server"\
		update "Execute an update"\
		emergency "Try a 'emergency shutdown'"\
		kill "Kill the StarMade process"\
		exit "Return to the SMCP" 3>&1 1>&2 2>&3
}

edit_menu(){
	dialog --backtitle "SMCP - StarMade Control Panel" --title " File edit "\
		--menu "Move using [UP] [DOWN], [ENTER] to select" 13 60 6\
		editwhite "Edit the whitelist"\
		editcfg "Edit the server.cfg"\
		editcron "Edit the crontab"\
		editblack "Edit the blacklist"\
		editmotd "Edit the welcome message"\
		exit "Return to the SMCP" 3>&1 1>&2 2>&3
}

gauge(){
	{ for I in $(seq 1 100) ; do
		echo $I
		sleep $GAUGETIME
	done
	echo 100; } | dialog --backtitle "SMCP - StarMade Control Panel" \
						--gauge "$GAUGEINFO" 6 60 0
}

infobox(){
	dialog --backtitle "SMCP - StarMade Control Panel" --infobox "$INFOVAR" 0 0
}

msgbox(){
	dialog --backtitle "SMCP - StarMade Control Panel" --msgbox "$MSGVAR" 0 0
}

yesno(){
	dialog --backtitle "SMCP - StarMade Control Panel" --yesno "$YESNOVAR" 0 0
	ANSWER=$?
}

while true ; do

menu_answer=$(main_menu)

opt=${?}
if [ $opt != 0 ] ; then
	exit
fi

case $menu_answer in

	manage)
		manage_menu_answer=$(manage_menu)
		
		opt=${?}
		if [ $opt != 0 ] ; then
			continue
		fi

		case $manage_menu_answer in

			start)
				$BASEDIR/functionlib.sh start
				INFOVAR="Start performed. Please Check"
			;;

			stop)
				YESNOVAR="Should the server be shutdown?"
				yesno
				if [ "$ANSWER" = "0" ] ; then
					$BASEDIR/functionlib.sh stop
					GAUGETIME="1.2"
					GAUGEINFO="Shutdown in progress"
					gauge
					INFOVAR="Shutdown performed."
					infobox
					sleep 5
				fi
			;;

			restart)
				YESNOVAR="Should the server be restarting?"
				yesno
				if [ "$ANSWER" = "0" ] ; then
					$BASEDIR/functionlib.sh restart
					GAUGETIME="3"
					GAUGEINFO="Restart in progress"
					gauge
					INFOVAR="Restart performed."
					infobox
					sleep 5
				fi
			;;

			status)
				MSGVAR=$($BASEDIR/functionlib.sh status)
				msgbox
			;;

			update)
				YESNOVAR="Should the server be updating?"
				yesno
				if [ "$ANSWER" = "0" ] ; then
					$BASEDIR/functionlib.sh stop
					GAUGETIME="1.2"
					GAUGEINFO="Shutdown in progress."
					gauge
					INFOVAR="Shutdown performed."
					infobox
					sleep 3

					until [[ -z pid ]] ; do
						INFOVAR="Checking shutdown."
						infobox
						sleep 0.3
						INFOVAR="Checking shutdown.."
						infobox
						sleep 0.3
						INFOVAR="Checking shutdown..."
						infobox
						sleep 0.3
					done

					if [ "$(serverAlive)" = "offline" ] ; then
						INFOVAR="Final check."
						infobox
						sleep 3
					else
						MSGVAR="Server isn't down. Please Check. Abort update."
						msgbox
						continue
					fi

					INFOVAR="Starting update"
					infobox
					sleep 3
					$BASEDIR/functionlib.sh update
				fi

				INFOVAR="Aborting..."
				infobox
				sleep 3
			;;
		esac
		;;

	edit)
		edit_menu_answer=$(edit_menu)

		opt=${?}
		if [ $opt != 0 ] ; then
			continue
		fi
		
		case $edit_menu_answer in

			editwhite)
			
			;;
		esac
		;;

	exit)
		exit 0
		;;
esac

done

exit 0

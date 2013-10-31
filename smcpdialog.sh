#!/bin/bash

BASEDIR=$(dirname `readlink -f $0`)
#source $BASEDIR/smcp.conf 2
#source $BASEDIR/functionlib.sh 2

main_menu(){
	dialog --backtitle "StarMade Control Panel" --title " Commandline interface - V$SMCPVERSION "\
		--cancel-label "Quit" \
		--menu "Move using [UP] [DOWN], [ENTER] to select" 30 60 23\
		start "beschreibung"\
		stop "beschreibung"\
		restart "beschreibung"\
		status "beschreibung"\
		update "beschreibung"\
		emergency "beschreibung"\
		kill "beschreibung"\
		mobs "beschreibung"\
		cleanmob "beschreibung"\
		screen "beschreibung"\
		dbsize "beschreibung"\
		command "beschreibung"\
		msg "beschreibung"\
		admins "beschreibung"\
		addadmin "beschreibung"\
		rmaddmin "beschreibung"\
		addwhite "beschreibung"\
		editwhite "beschreibung"\
		editcfg "beschreibung"\
		editcron "beschreibung"\
		editblack "beschreibung"\
		editmotd "beschreibung"\
		exit "beschreibung" 3>&1 1>&2 2>&3
}
menu_answer=$(main_menu)

case $menu_answer in

	start)
		echo "starten"
		;;

	stop)
		echo "stoppen"
		;;

	restart)
		echo "neustart"
		;;

	status)
		echo "statusabruf"
		;;

esac



$BASEDIR/functionlib.sh $VAR 2>/dev/null
exit 0

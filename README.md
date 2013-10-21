smcp
====

A tiny control panel for administration of a StarMade Server

	Copyright 2013 Robin Cerny <tschaerni@gmail.com> or <robin@cerny.li>

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
	MA 02110-1301, USA.
	
English:

A small Preface,

I am neither a programmer nor do I have very good knowledge of Unix-like systems and shell / bash script. I'm still learning and have learned, among other things with writing this script very much so. The script I've written primarily for the StarMade Server of feedupyourbeast.de. A part of the script I got from the official Star Made forum.

Find on: http://star-made.org/content/server-scripts

These were written by Mikuz, and I hereby would like to express my thanks to him.


system Requirements:

 - bash (not sh)
 - bc
 - screen
 - netcat
 - awk
 - rlwrap
 - ionice

installation

Should be started shortly, first of the Star Made server so it downloads all the files.
	Example: java -jar StarMade-starter.jar -nogui
In the same directory, a symlink called "server" must be created.
	Example: ln -s /path/to/StarMadeServer/StarMade /path/to/StarMadeServer/server

Settings in the two scripts "smcp.sh" and "start.sh" are made.

	smcp.sh	
		Between lines 30 and 34:
		- line 30: Name of the Screensession (Standard: starmade)
		- line 31: the editor to use (Standard: nano)
		- line 33: The Port to use (Standard: 4242)
		- line 34:	The maximum number of player slots (The same value is entered in the server.cfg.)
	
	Start.sh
		Between lines 8 and 12:
		- line 8: 	The name of the screen session (Standard: starmade)
		- line 9:	The Port to use (Standard: 4242)
		- line 10:	The CPU Threads to use (Dabei kommt es auf den Prozessor an.)
		- line 11:	Initialmemory for Java (Standard: 512m)
		- line 12:	Maximum memory usage by Java (Standard: 4g)

Functions:
	
	Start
	Stop
	Restart
	Polling Server Status (number of player)
	update function
	emergency shutdown
	Immediately terminate the StarMade Server (Kill)
	Cleaning the database of all NPC's
	Resuming the screen session
	View the database size
	Send a custom command to the StarMade Server
	Send a custom message to all Players
	Appoint someone to admin
	Someone escape the Admin Rank
	Set a user to the whitelist
	Edit the whitelist
	Edit the server.cfg
	Edit the crontab
	Edit the blacklist
	Edit the welcome message

For regular reboots, you can switch the script with the "-r" call.
	Example: /path/to/StarMadeServer/smcp.sh -r
If this should happen as a crontab entry, you can make the entry as follows to get informed about the output of the script rather than mail.
	Example: /path/to/StarMadeServer/smcp.sh -r > /dev/null


Both scripts (smcp.sh and start.sh) put both a pid file that they be called multiple times to to prevent. 
The StarMade Control Panel calls each time from the current version and displays a message if it is a new version available.
In addition, the start.sh function after a restart or shutdown all admins to delete from the admins.txt. This is what I have installed to prevent any exploit uses.

For questions, suggestions, and note, I am always open, I reach under tschaerni@gmail.com or robin@cerny.li

Have fun with the StarMade Control Panel!

Cheers
Robin

PS: I know my english is bad. So I used google translate. forgive me :)

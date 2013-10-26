<div id="commands">
<a href="#">&times;</a>
<div class="inner">
<table>
  <tr>
    <td class="head"><strong>Command</strong></td>
    <td class="head"><strong>Description</strong></td>
    <td class="head"><strong>Parameters</strong></td>
    <td class="head"><strong>Sample</strong></td>
  </tr>
  <tr>
    <td>last_changed</td>
    <td>shows the unique id of the players that spawned and/or last modified the selected structure</td>
    <td></td>
    <td>/last_changed</td>
  <tr>
    <td>teleport_to</td>
    <td>teleports the current controlled entity</td>
    <td>PlayerName(String), X(Float), Y(Float), Z(Float)</td>
    <td>/teleport_to schema 0.0 1.0 3.5</td>
  <tr>
    <td>kill_character</td>
    <td>kills the entity with that name</td>
    <td>PlayerName(String)</td>
    <td>/kill_character schema</td>
  <tr>
    <td>teleport_self_to</td>
    <td>teleports the current controlled entity</td>
    <td>X(Float), Y(Float), Z(Float)</td>
    <td>/teleport_self_to 0.0 1.0 3.5</td>
  <tr>
    <td>change_sector</td>
    <td>teleports the current player to another sector</td>
    <td>X(Integer), Y(Integer), Z(Integer)</td>
    <td>/change_sector 2 3 4</td>
  <tr>
    <td>export_sector</td>
    <td>exports the whole sector. be sure to use /force_save before</td>
    <td>X(Integer), Y(Integer), Z(Integer), name(String)</td>
    <td>/export_sector 2 3 4 mySavedSector</td>
  <tr>
    <td>import_sector</td>
    <td>make sure that the target sector is unloaded</td>
    <td>toX(Integer), toY(Integer), toZ(Integer), name(String)</td>
    <td>/import_sector 2 3 4 mySavedSector</td>
  <tr>
    <td>change_sector_for</td>
    <td>teleports any player to another sector</td>
    <td>player(String), X(Integer), Y(Integer), Z(Integer)</td>
    <td>/change_sector_for schema 2 3 4</td>
  <tr>
    <td>repair_sector</td>
    <td>attempts to correct the regitry of the sector</td>
    <td>X(Integer), Y(Integer), Z(Integer)</td>
    <td>/repair_sector 2 3 4</td>
  <tr>
    <td>teleport_self_home</td>
    <td>teleports the current controlled entity to the spawning point of the player controlling it</td>
    <td></td>
    <td>/teleport_self_home</td>
  <tr>
    <td>destroy_entity</td>
    <td>Destroys the selected Entity</td>
    <td></td>
    <td>/destroy_entity</td>
  <tr>
    <td>destroy_entity_dock</td>
    <td>Destroys the selected Entity and all docked ships</td>
    <td></td>
    <td>/destroy_entity_dock</td>
  <tr>
    <td>giveid</td>
    <td>Gives player elements by ID</td>
    <td>PlayerName(String), ElementID(Short), Count(Integer)</td>
    <td>/giveid schema 2 10</td>
  <tr>
    <td>give</td>
    <td>Gives player elements by NAME</td>
    <td>PlayerName(String), ElementName(String), Count(Integer)</td>
    <td>/give schema Power 10</td>
  <tr>
    <td>give_logbook</td>
    <td>Gives player logbook)</td>
    <td>PlayerName(String)</td>
    <td>/give_logbook schema</td>
  <tr>
    <td>give_laser_weapon</td>
    <td>Gives player logbook)</td>
    <td>PlayerName(String)</td>
    <td>/give_laser_weapon schema</td>
  <tr>
    <td>give_recipe</td>
    <td>Gives player recipe)</td>
    <td>PlayerName(String), TypeOutput(Integer)</td>
    <td>/give_recipe schema 1</td>
  <tr>
    <td>give_credits</td>
    <td>Gives player credits)</td>
    <td>PlayerName(String), Count(Integer)</td>
    <td>/give_credits schema 1000</td>
  <tr>
    <td>start_countdown</td>
    <td>Starts a countdown visible for everyone)</td>
    <td>Seconds(Integer), Message(String)</td>
    <td>/start_countdown 180 may contain spaces</td>
  <tr>
    <td>jump</td>
    <td>Jump to an object in line of sight if possible</td>
    <td></td>
    <td>/jump</td>
  <tr>
    <td>tp_to</td>
    <td>warp to player's position</td>
    <td>PlayerName(String)</td>
    <td>/tp_to schema</td>
  <tr>
    <td>tp</td>
    <td>warp a player to your position</td>
    <td>PlayerName(String)</td>
    <td>/tp schema</td>
  <tr>
    <td>ignore_docking_area</td>
    <td>enables/disables docking area validation (default off)</td>
    <td>enable(Boolean)</td>
    <td>/ignore_docking_area false</td>
  <tr>
    <td>shutdown</td>
    <td>shutsdown the server in specified seconds (neg values will stop any active countdown)</td>
    <td>TimeToShutdown(Integer)</td>
    <td>/shutdown 120</td>
  <tr>
    <td>force_save</td>
    <td>The server will save all data to disk</td>
    <td></td>
    <td>/force_save</td>
  <tr>
    <td>add_admin</td>
    <td>Gives admin rights to (param0(String)))</td>
    <td>PlayerName(String)</td>
    <td>/add_admin schema</td>
  <tr>
    <td>list_admins</td>
    <td>Lists all admins</td>
    <td></td>
    <td>/list_admins</td>
  <tr>
    <td>status</td>
    <td>Displays server status</td>
    <td></td>
    <td>/status</td>
  <tr>
    <td>list_banned_ip</td>
    <td>Lists all banned IPs</td>
    <td></td>
    <td>/list_banned_ip</td>
  <tr>
    <td>list_banned_name</td>
    <td>Lists all banned names</td>
    <td></td>
    <td>/list_banned_name</td>
  <tr>
    <td>list_whitelist_ip</td>
    <td>Lists all whitelisted IPs</td>
    <td></td>
    <td>/list_whitelist_ip</td>
  <tr>
    <td>list_whitelist_name</td>
    <td>Lists all whitelisted names</td>
    <td></td>
    <td>/list_whitelist_name</td>
  <tr>
    <td>ban_name</td>
    <td>bans a playername from this server</td>
    <td>PlayerName(String)</td>
    <td>/ban_name schema</td>
  <tr>
    <td>ban_ip</td>
    <td>bans a ip from this server</td>
    <td>PlayerIP(String)</td>
    <td>/ban_ip 192.0.0.1</td>
  <tr>
    <td>whitelist_name</td>
    <td>add a playername to the white list</td>
    <td>PlayerName(String)</td>
    <td>/whitelist_name schema</td>
  <tr>
    <td>whitelist_ip</td>
    <td>add an IP to the white list</td>
    <td>PlayerIP(String)</td>
    <td>/whitelist_ip 192.0.0.1</td>
  <tr>
    <td>whitelist_activate</td>
    <td>Turns white list on/off (will be saved in server.cfg)</td>
    <td>enable(Boolean)</td>
    <td>/whitelist_activate false</td>
  <tr>
    <td>unban_name</td>
    <td>unbans a playername from this server</td>
    <td>PlayerName(String)</td>
    <td>/unban_name schema</td>
  <tr>
    <td>unban_ip</td>
    <td>unbans a ip from this server</td>
    <td>PlayerIP(String)</td>
    <td>/unban_ip 192.0.0.1</td>
  <tr>
    <td>kick</td>
    <td>kicks a player from the server</td>
    <td>PlayerName(String)</td>
    <td>/kick schema</td>
  <tr>
    <td>update_shop_prices</td>
    <td>Updates the prices of all shops instantly</td>
    <td></td>
    <td>/update_shop_prices</td>
  <tr>
    <td>remove_admin</td>
    <td>Removes admin rights of player</td>
    <td>PlayerName(String)</td>
    <td>/remove_admin schema</td>
  <tr>
    <td>search</td>
    <td>Returns the sector of a ship of station with that uid </td>
    <td>ShipOrStationName(String)</td>
    <td>/search myLostShip</td>
  <tr>
    <td>sector_chmod</td>
    <td>Changes the sector mode: example '/sector_chmod 8 8 8 + peace', available modes are 'peace'(no enemy spawn), 'protect'(no attacking possible)</td>
    <td>SectorX(Integer), SectorY(Integer), SectorZ(Integer), +/-(String), peace/protect(String)</td>
    <td>/sector_chmod 10 12 15 10 10</td>
  <tr>
    <td>shop_restock</td>
    <td>Restocks the selected shop with items</td>
    <td></td>
    <td>/shop_restock</td>
  <tr>
    <td>god_mode</td>
    <td>enables god mode for a player</td>
    <td>PlayerName(String), active(Boolean)</td>
    <td>/god_mode schema true/false</td>
  <tr>
    <td>invisibility_mode</td>
    <td>enables invisibility mode for a player</td>
    <td>PlayerName(String), active(Boolean)</td>
    <td>/invisibility_mode schema true/false</td>
</table>
</div>
</div>
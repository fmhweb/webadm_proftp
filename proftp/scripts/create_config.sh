#!/bin/bash

CONFIGPATH="/var/www/html/webadm/proftp/share/config/config.xml"
CMDWHICH="which"

CONFIG="<?xml version='1.0' standalone='yes'?>
<config>
	<ftphome>
		<path>/data</path>
		<user>webadm</user>
		<group>webadm</group>
		<mode>0770</mode>
	</ftphome>
	<mysql>
		<host>localhost</host>
		<db>webadm_proftp</db>
		<user>webadm_proftp</user>
		<pass>webadm_proftp</pass>
	</mysql>
	<cmd>
		<ftpwho>`$CMDWHICH ftpwho`</ftpwho>
		<proftpd>`$CMDWHICH proftpd`</proftpd>
		<useradd>`$CMDWHICH useradd`</useradd>
	</cmd>
	<default>
		<min_passwd_length>6</min_passwd_length>
		<max_passwd_length>12</max_passwd_length>
		<min_gid>10000</min_gid>
		<min_uid>10000</min_uid>
	</default>
</config>
"

echo "$CONFIG"
echo "$CONFIG" > $CONFIGPATH

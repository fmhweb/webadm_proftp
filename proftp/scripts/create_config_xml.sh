#!/bin/bash

CONFIGDIR="/var/www/html/webadm/proftp/conf"
CONFIGFILE=$CONFIGDIR"/config.xml"
CMDWHICH="/usr/bin/which"


XML="<?xml version='1.0' standalone='yes'?>
<config>
	<default>
		<passwdlength>12</passwdlength>
		<shell>/bin/sh</shell>
		<dir>
			<mode>0750</mode>
			<user>webadm</user>
			<group>ftp</group>
		</dir>
		<min>
			<uid>10000</uid>
			<gid>10000</gid>
		</min>
	</default>
	<path>
		<ftphome>/data</ftphome>
		<passwd>/etc/passwd</passwd>
		<group>/etc/group</group>
	</path>
	<cmd>
		<cat>`$CMDWHICH cat`</cat>
		<df>`$CMDWHICH df`</df>
		<chown>`$CMDWHICH chown`</chown>
		<chmod>`$CMDWHICH chmod`</chmod>
		<chpasswd>`$CMDWHICH chpasswd`</chpasswd>
		<grep>`$CMDWHICH grep`</grep>
		<id>`$CMDWHICH id`</id>
		<ftpwho>`$CMDWHICH ftpwho`</ftpwho>
		<proftpd>`$CMDWHICH proftpd`</proftpd>
		<useradd>`$CMDWHICH useradd`</useradd>
		<usermod>`$CMDWHICH usermod`</usermod>
		<userdel>`$CMDWHICH userdel`</userdel>
		<groupadd>`$CMDWHICH groupadd`</groupadd>
		<groupmod>`$CMDWHICH groupmod`</groupmod>
		<groupdel>`$CMDWHICH groupdel`</groupdel>
		<blockdev>`$CMDWHICH blockdev`</blockdev>
		<setquota>`$CMDWHICH setquota`</setquota>
		<repquota>`$CMDWHICH repquota`</repquota>
	</cmd>
	<mysql>
		<host>localhost</host>
		<db>webadm_proftp</db>
		<user>webadm_proftp</user>
		<pass>webadm_proftp</pass>
	</mysql>
</config>"

echo "$XML"
echo "$XML" > $CONFIGFILE

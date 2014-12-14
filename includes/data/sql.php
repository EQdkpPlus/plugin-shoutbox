<?php
/*	Project:	EQdkp-Plus
 *	Package:	Shoutbox Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

$shoutboxSQL = array(

	'uninstall' => array(
		'1'		=> 'DROP TABLE IF EXISTS `__shoutbox`',
	),

	'install'	=> array(
		'1'	=> 'CREATE TABLE IF NOT EXISTS `__shoutbox` (
				`shoutbox_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
				`user_or_member_id` SMALLINT(5) NOT NULL DEFAULT \'-1\',
				`shoutbox_date` INT(11) UNSIGNED NOT NULL DEFAULT \'0\',
				`shoutbox_text` TEXT COLLATE utf8_bin DEFAULT NULL,
				PRIMARY KEY (`shoutbox_id`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_bin;',
	),
);

?>

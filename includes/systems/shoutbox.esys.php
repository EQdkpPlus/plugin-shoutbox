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

$systems_shoutbox = array(
	'pages' => array(
		'manage' => array(
			'name' => 'hptt_shoutbox_manage',
			'table_main_sub' => '%shoutbox_id%',
			'table_sort_dir' => 'desc',
			'page_ref' => 'manage.php',
			'show_select_boxes' => registry::fetch('user')->check_auth('a_shoutbox_delete', false),
			'selectboxes_checkall'	=> true,
			'table_presets' => array(
				array('name' => 'sbdate', 'sort' => true,	'th_add' => 'align="center" width="120px"',	'td_add' => 'align="center" nowrap="nowrap"'),
				array('name' => 'sbname', 'sort' => true,	'th_add' => 'align="center" width="20%"',	'td_add' => 'nowrap="nowrap"'),
				array('name' => 'sbtext', 'sort' => false,	'th_add' => 'align="center"',				'td_add' => '')
			),
		),
		)
);
?>

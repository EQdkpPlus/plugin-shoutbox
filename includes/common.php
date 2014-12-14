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

// -- Pluskernel common.php ---------------------------------------------------
if (!isset($eqdkp_root_path)){
	$eqdkp_root_path = './';
}
include_once($eqdkp_root_path.'common.php');


// -- Used Classes ------------------------------------------------------------
include_once(registry::get_const('root_path').'plugins/shoutbox/includes/shoutbox.class.php');
$shoutbox = registry::register('ShoutboxClass');


// -- Check requirements ------------------------------------------------------
if (is_object($shoutbox)){
	$sb_req_check = $shoutbox->checkRequirements();
	if ($sb_req_check !== true){
		message_die($sb_req_check);
	}
}
?>

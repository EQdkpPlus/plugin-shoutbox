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

// EQdkp required files/vars
define('EQDKP_INC', true);
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../';
include_once('includes/common.php');


// -- Plugin installed? -------------------------------------------------------
if (!register('plugin_manager')->check('shoutbox', PLUGIN_INSTALLED)){
	message_die(register('user')->lang('sb_plugin_not_installed'));
}


// -- Get content -------------------------------------------------------------
$content =register('ShoutboxClass')->showShoutbox();

// -- Template ----------------------------------------------------------------
register('template')->assign_vars(array (
	// Form
	'CONTENT' => $content
));


// -- EQDKP -------------------------------------------------------------------
register('core')->set_vars(array (
	'page_title'	=> register('user')->lang('shoutbox'),
	'template_path'	=> register('plugin_manager')->get_data('shoutbox', 'template_path'),
	'template_file'	=> 'show.html',
	'header_format'	=> 'simple',
	'display'		=> true
));

?>

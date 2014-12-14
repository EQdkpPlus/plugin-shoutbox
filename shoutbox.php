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

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once('./includes/common.php');


// Be sure plugin is installed
if (registry::register('plugin_manager')->check('shoutbox', PLUGIN_INSTALLED)){
	$in = registry::register('input');

	// get post/get values
	$sb_text			= $in->get('sb_text');
	$sb_usermember_id	= $in->get('sb_usermember_id', -1);
	$sb_delete			= $in->get('sb_delete', 0);
	$sb_root			= $in->get('sb_root');
	$sb_orientation		= $in->get('sb_orientation');

	// -- Insert? ---------------------------------------------
	if ($sb_text && $sb_usermember_id != -1){
		$shoutbox->insertShoutboxEntry($sb_usermember_id, $sb_text);
	}
	// -- Delete? ---------------------------------------------
	else if ($sb_delete){
		$shoutbox->deleteShoutboxEntry($sb_delete);
	}

	// -- Output ----------------------------------------------
	echo $shoutbox->getContent($sb_orientation, urldecode($sb_root), true);
}else{
	$error = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline colorswitch">
		<tr>
			<td><div class="center">'.registry::fetch('user')->lang('sb_plugin_not_installed').'</div></td>
		</tr>
		</table>';
	echo $error;
}

?>

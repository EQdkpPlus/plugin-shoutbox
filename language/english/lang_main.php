<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-Plus Language File
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

 
if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

//Language: English	
//Created by EQdkp Plus Translation Tool on  2014-12-17 21:28
//File: plugins/shoutbox/language/english/lang_main.php
//Source-Language: german

$lang = array( 
	"shoutbox" => 'Shoutbox',
	"sb_shoutbox" => 'Shoutbox',
	"shoutbox_name" => 'Shoutbox',
	"shoutbox_desc" => 'Shoutbox is a Plugin users are able to exchange short messages with.',
	"sb_short_desc" => 'Shoutbox',
	"sb_long_desc" => 'Shoutbox is a Plugin users are able to exchange short messages with.',
	"sb_plugin_not_installed" => 'Shoutbox Plugin not installed',
	"sb_php_version" => 'Shoutbox requires PHP %1$s or higher. Your server runs PHP %2$s',
	"sb_plus_version" => 'Shoutbox requires EQDKP-PLUS %1$s or higher. Your installed Version is %2$s',
	"sb_no_view_permission" => 'You don\'t have the permission to view shouts.',
	"sb_manage_archive" => 'Manage Archive',
	"sb_written_by" => 'written by',
	"sb_written_at" => 'at',
	"sb_delete_success" => 'Successfully deleted entries',
	"sb_settings_info" => 'Further Shoutbox settings could be found within the <a href="'.registry::get_const('root_path').'.'.registry::get_const('root_path').'admin/manage_portal.php'.registry::get_const('SID').'">Portalmodule settings</a>',
	"sb_use_users" => 'Use usernames instead of membernames',
	"sb_use_users_help" => 'On changing membernames to usernames all entries will be updated.<br/>On changing usernames to membernames all entries will be deleted!',
	"sb_convert_member_user_success" => 'All membernames within the entries have been successfully updated to usernames.',
	"sb_convert_user_member_success" => 'All entries were deleted.',
	"sb_config_saved" => 'Settings saved successfully',
	"sb_header_general" => 'General Shoutbox settings',
	"sb_output_count_limit" => 'Maximum number of shown Shoutbox entries.',
	"sb_show_date" => 'Show date also?',
	"sb_f_show_archive" => 'Show Archive?',
	"sb_f_max_text_length" => 'Maximum length of a text entry',
	"sb_f_input_box_location" => 'Location of input box',
	"sb_location_top" => 'Above entries',
	"sb_location_bottom" => 'Below entries',
	"sb_f_autoreload" => 'Time in seconds to wait for automatic reload of Shoutbox (Default 0 = Off)',
	"sb_f_help_autoreload" => 'Set to 0 to disable automatic reload',
	"sb_no_character_assigned" => 'No characters are connected yet. At least one character has to be connected to be able to post.',
	"sb_submit_text" => 'Send',
	"sb_save_wait" => 'Saving, please wait...',
	"sb_reload" => 'Reload',
	"sb_no_entries" => 'No entries',
	"sb_archive" => 'Archive',
	"sb_shoutbox_archive" => 'Shoutbox Archive',
	"sb_missing_char_id" => 'Invalid Member ID entered',
	"sb_missing_text" => 'Missing text to insert',
	
);

?>
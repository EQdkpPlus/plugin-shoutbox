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

$lang = array(
	'shoutbox'							=> 'Shoutbox',
	'sb_shoutbox'						=> 'Shoutbox',

	// Portal
	'shoutbox_name'						=> 'Shoutbox',
	'shoutbox_desc'						=> 'Shoutbox ist ein Plugin mit dem User kleine Mitteilungen austauschen können.',

	// Description
	'sb_short_desc'						=> 'Shoutbox',
	'sb_long_desc'						=> 'Shoutbox ist ein Plugin mit dem User kleine Mitteilungen austauschen können.',

	// General
	'sb_plugin_not_installed'			=> 'Das Shoutbox Plugin ist nicht installiert',
	'sb_php_version'					=> "Shoutbox benötigt PHP %1\$s oder höher. Dein Server läuft mit PHP %2\$s",
	'sb_plus_version'					=> "Shoutbox benötigt EQDKP-PLUS %1\$s oder höher. Die installierte Version ist %2\$s",
	'sb_no_view_permission'				=> "Du hast leider keine Berechtigung, um Shouts zu sehen.",

	// Menu
	'sb_manage_archive'					=> 'Archiv Verwalten',

	// Archive
	'sb_written_by'						=> 'geschrieben von',
	'sb_written_at'						=> 'um',

	// Admin
	'sb_delete_success'					=> 'Einträge erfolgreich gelöscht',
	'sb_settings_info'					=> 'Weitere Einstellungen für die Shoutbox findet Ihr unter den <a href="'.registry::get_const('root_path').'admin/manage_portal.php'.registry::get_const('SID').'">Portalmodul Einstellungen</a>',
	'sb_use_users'						=> 'Benutzernamen anstatt der Charakternamen verwenden',
	'sb_use_users_help'					=> 'Beim Ändern von Charakteren zu Benutzern werden die bestehenden Einträge aktualisiert.<br/>Beim Ändern von Benutzern zu Charakteren werden die bestehenden Einträge gelöscht!',
	'sb_convert_member_user_success'	=> 'Alle Charaktere in den Einträgen wurden erfolgreich zu Benutzern aktualisiert.',
	'sb_convert_user_member_success'	=> 'Alle bestehenden Einträge wurden gelöscht',

	// Configuration
	'sb_config_saved'					=> 'Einstellungen wurden gespeichert',
	'sb_header_general'					=> 'Allgemeine Shoutbox Einstellungen',

	// Portal Modules
	'sb_f_output_count_limit'			=> 'Maximale Anzahl an Shoutbox Einträgen.',
	'sb_show_date'						=> 'Zusätzlich das Datum anzeigen?',
	'sb_f_show_archive'					=> 'Archiv anzeigen?',
	'sb_f_max_text_length'				=> 'Maximal erlaubte Textlänge eines Eintrags',
	'sb_f_input_box_location'			=> 'Position des Eingabefeldes',
	'sb_location_top'					=> 'Oberhalb der Einträge',
	'sb_location_bottom'				=> 'Unterhalb der Einträge',
	'sb_f_autoreload'					=> 'Zeit in Sekunden nach der die Shoutbox automatisch neu geladen werden soll (Standard 0 = Aus)',
	'sb_f_help_autoreload'				=> 'Wird 0 eingetragen so wird das automatische Neu Laden abgeschalten',
	'sb_no_character_assigned'			=> 'Es wurde kein Charakter verknüpft. Es muss ein Charakter verknüpft sein bevor Einträge gemacht werden können.',
	'sb_submit_text'					=> 'Absenden',
	'sb_save_wait'						=> 'Speichern, bitte warten...',
	'sb_reload'							=> 'Neu laden',
	'sb_no_entries'						=> 'Keine Einträge',
	'sb_archive'						=> 'Archiv',
	'sb_shoutbox_archive'				=> 'Shoutbox Archiv',
	'sb_f_box_height'					=> 'Höhe des Portalmoduls',

	// Exchange
	'sb_missing_char_id'				=> 'Es wurde keine gültige Charakter ID angegeben',
	'sb_missing_text'					=> 'Es wurde kein Text angegeben',
	
	'sb_write_post'						=> 'Nachricht schreiben',	
);

?>

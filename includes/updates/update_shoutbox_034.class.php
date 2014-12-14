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

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

if (!class_exists('update_shoutbox_034')){
	class update_shoutbox_034 extends sql_update_task{

		public $author		= 'Aderyn';
		public $version		= '0.3.4';    // new version
		public $name		= 'Shoutbox 0.3.4 Update';
		public $type		= 'plugin_update';
		public $plugin_path	= 'shoutbox'; // important!

		/**
		* Constructor
		*/
		public function __construct(){
			parent::__construct();

			// init language
			$this->langs = array(
				'english' => array(
					'update_shoutbox_034' => 'Shoutbox 0.3.4 Update Package',
					1 => 'Add new permission',
				),
				'german' => array(
					'update_shoutbox_034' => 'Shoutbox 0.3.4 Update Paket',
					1 => 'Füge neue Berechtigung hinzu',
				),
			);

			// init SQL querys
			$this->sqls = array(
				1 => "INSERT INTO `__auth_options` (`auth_value`, `auth_default`) VALUES ('u_shoutbox_view', 'Y');",
			);
		}
	}
}
?>

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

if (!class_exists('update_shoutbox_033')){
	class update_shoutbox_033 extends sql_update_task{

		public $author		= 'Aderyn';
		public $version		= '0.3.3';    // new version
		public $name		= 'Shoutbox 0.3.3 Update';
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
					'update_shoutbox_033'	=> 'Shoutbox 0.3.3 Update Package',
					'update_function'		=> 'Insert new max text length setting',
				),
				'german' => array(
					'update_shoutbox_033'	=> 'Shoutbox 0.3.3 Update Paket',
					'update_function'		=> 'Füge Einstellung für die Maximale Textlänge hinzu',
				),
			);

			// init SQL querys
			$this->sqls = array(
			);
		}

		/**
		* update_function
		* Execute update function
		*
		* @returns  true/false
		*/
		public function update_function(){
			// set as 'core' config
			$this->config->set('sb_max_text_length', '160');
			return true;
		}
	}
}
?>

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
	header('HTTP/1.0 404 Not Found'); exit;
}


/*+----------------------------------------------------------------------------
  | shoutbox
  +--------------------------------------------------------------------------*/
class shoutbox extends plugin_generic{

	public $version		= '0.4.3';
	public $build		= '11404';
	public $copyright	= 'Aderyn';
	public $vstatus		= 'Beta';

	protected static $apiLevel = 20;

	/**
	* Constructor
	* Initialize all informations for installing/uninstalling plugin
	*/
	public function __construct(){
		parent::__construct();

		$this->add_data(array (
			'name'				=> 'Shoutbox',
			'code'				=> 'shoutbox',
			'path'				=> 'shoutbox',
			'contact'			=> 'Aderyn@gmx.net',
			'template_path'		=> 'plugins/shoutbox/templates/',
			'icon'				=> 'fa-bullhorn',
			'version'			=> $this->version,
			'author'			=> $this->copyright,
			'description'		=> $this->user->lang('sb_short_desc'),
			'long_description'	=> $this->user->lang('sb_long_desc'),
			'homepage'			=> EQDKP_PROJECT_URL,
			'plus_version'		=> '1.0',
			'build'				=> $this->build,
		));

		$this->add_dependency(array(
			'plus_version'		=> '0.7'
		));

		// -- Register our permissions ------------------------
		// permissions: 'a'=admins, 'u'=user
		// ('a'/'u', Permission-Name, Enable? 'Y'/'N', Language string, array of user-group-ids that should have this permission)
		// Groups: 2 = Super-Admin, 3 = Admin, 4 = Member
		$this->add_permission('a', 'delete',	'N', $this->user->lang('delete'),	array(2,3));
		$this->add_permission('u', 'view',		'Y', $this->user->lang('view'),		array(1,2,3,4));
		$this->add_permission('u', 'add',		'Y', $this->user->lang('add'),		array(2,3,4));

		// -- Menu --------------------------------------------
		$this->add_menu('admin', $this->gen_admin_menu());

		// -- Portal Module -----------------------------------
		$this->add_portal_module('shoutbox');

		// -- PDH Modules -------------------------------------
		$this->add_pdh_read_module('shoutbox');
		$this->add_pdh_write_module('shoutbox');

		// -- Exchange Modules --------------------------------
		$this->add_exchange_module('shoutbox_add');
		$this->add_exchange_module('shoutbox_list');


		// -- Hooks -------------------------------------------
		$this->add_hook('search', 'shoutbox_search_hook', 'search');
	}

	/**
	* pre_install
	* Define Installation
	*/
	public function pre_install(){
		// include SQL and default configuration data for installation
		include($this->root_path.'plugins/shoutbox/includes/data/sql.php');
		include($this->root_path.'plugins/shoutbox/includes/data/config.php');

		// define installation
		for ($i = 1; $i <= count($shoutboxSQL['install']); $i++)
			$this->add_sql(SQL_INSTALL, $shoutboxSQL['install'][$i]);

		// insert configuration
		if (is_array($config_vars))
			$this->config->set($config_vars, '', 'shoutbox');
		
		$this->pdc->del_prefix('pdh_shoutbox_table');
	}

	/**
	* pre_uninstall
	* Define uninstallation
	*/
	public function pre_uninstall(){
		// include SQL data for uninstallation
		include($this->root_path.'plugins/shoutbox/includes/data/sql.php');

		for ($i = 1; $i <= count($shoutboxSQL['uninstall']); $i++)
			$this->add_sql(SQL_UNINSTALL, $shoutboxSQL['uninstall'][$i]);
		
		$this->pdc->del_prefix('pdh_shoutbox_table');
	}

	/**
	* post_uninstall
	* Define Post Uninstall
	*/
	public function post_uninstall(){
		// clear cache
		$this->pdc->del('pdh_shoutbox_table');
	}

	/**
	* gen_admin_menu
	* Generate the Admin Menu
	*/
	private function gen_admin_menu(){
		$admin_menu = array (array(
			'name'	=> $this->user->lang('shoutbox'),
			'icon'	=> 'fa-comment',
			1 => array (
				'link'	=> 'plugins/shoutbox/admin/settings.php'.$this->SID,
				'text'	=> $this->user->lang('settings'),
				'check'	=> 'a_shoutbox_',
				'icon'	=> 'fa-wrench'
			),
			2 => array (
				'link'	=> 'plugins/shoutbox/admin/manage.php'.$this->SID,
				'text'	=> $this->user->lang('sb_manage_archive'),
				'check'	=> 'a_shoutbox_delete',
				'icon'	=> 'fa-archive'
			)
		));
		return $admin_menu;
	}
}
?>

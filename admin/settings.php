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
define('IN_ADMIN', true);
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../../';
include_once('./../includes/common.php');


/*+----------------------------------------------------------------------------
  | ShoutboxSettings
  +--------------------------------------------------------------------------*/
class ShoutboxSettings extends page_generic{

	/**
	* Constructor
	*/
	public function __construct(){
		// plugin installed?
		if (!$this->pm->check('shoutbox', PLUGIN_INSTALLED))
			message_die($this->user->lang('sb_plugin_not_installed'));

		$handler = array(
			'sb_save'	=> array('process' => 'save', 'csrf' => true, 'check' => 'a_shoutbox_'),
		);
		parent::__construct('a_shoutbox_', $handler);

		$this->process();
	}

	/**
	* save
	* Save the configuration
	*/
	public function save(){
		// is use_user change?
		if ($this->in->get('sb_use_users', 0) != $this->config->get('sb_use_users', 'shoutbox')){
			$shoutbox = registry::register('ShoutboxClass');

			// convert to member?
			if ($this->in->get('sb_use_users', '0') == '1'){
				$shoutbox->convertFromMemberToUser();
				$messages[] = $this->user->lang('sb_convert_member_user_success');
			}else{
				$shoutbox->deleteAllEntries();
				$messages[] = $this->user->lang('sb_convert_user_member_success');
			}
		}

		// take over new values
		$savearray = array(
			'sb_use_users'	=> $this->in->get('sb_use_users', 0),
		);

		// update configuration
		$this->config->set($savearray, '', 'shoutbox');
		// Success message
		$messages[] = $this->user->lang('sb_config_saved');

		$this->display($messages);
	}

	/**
	* display
	* Display the page
	*
	* @param    array  $messages   Array of Messages to output
	*/
	public function display($messages=array()){
		// -- Messages ------------------------------------------------------------
		if ($messages){
			foreach($messages as $name)
				$this->core->message($name, $this->user->lang('shoutbox'), 'green');
		}

		// -- Template ------------------------------------------------------------
		$this->tpl->assign_vars(array (
			// form
			'F_USE_USERS'	=> new hradio('sb_use_users', array('value' => $this->config->get('sb_use_users', 'shoutbox'))),
		));

		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'	=> $this->user->lang('shoutbox').' '.$this->user->lang('settings'),
			'template_path'	=> $this->pm->get_data('shoutbox', 'template_path'),
			'template_file'	=> 'admin/settings.html',
			'display'		=> true
		));
	}
}
registry::register('ShoutboxSettings');
?>

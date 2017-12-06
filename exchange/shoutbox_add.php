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

/*+----------------------------------------------------------------------------
  | exchange_shoutbox_add
  +--------------------------------------------------------------------------*/
if (!class_exists('exchange_shoutbox_add')){
	class exchange_shoutbox_add extends gen_class{
		/* List of dependencies */
		public static $shortcuts = array('pex'=>'plus_exchange');

		/**
		* Additional options
		*/
		public $options = array();

		/**
		* post_shoutbox_add
		* POST Request to add shoutbox entry
		*
		* @param   array   $params   Parameters array
		* @param   string  $body     body-array of request
		*
		* @returns array
		*/
		function post_shoutbox_add($params, $arrBody){
			// be sure user is logged in
			if ($this->user->is_signedin()){
				// parse request
				$member_id	= (isset($arrBody['charid'])) ? intval($arrBody['charid']) : intval($this->pdh->get('user', 'mainchar', array($this->user->data['user_id'])));
				$text		= (isset($arrBody['text']))   ? trim($arrBody['text']) : '';

				// check if member id is valid for this user
				$valid_members = $this->pdh->get('member', 'connection_id', array($this->user->data['user_id']));
				$member_valid = (is_array($valid_members) && in_array($member_id, $valid_members)) ? true : false;

				// if we are in "user" mode OR member is valid, continue
				if ($this->config->get('sb_use_users', 'shoutbox') || $member_valid){
					// get usermember_id
					$usermember_id = ($this->config->get('sb_use_users', 'shoutbox') ? intval($this->user->data['user_id']) : $member_id);

					if (!empty($text) && $usermember_id > 0){
						// insert xml text
						include_once($this->root_path.'plugins/shoutbox/includes/common.php');
						$result = register('ShoutboxClass')->insertShoutboxEntry($usermember_id, trim($text));

						// return status
						$response = array('status' => ($result) ? 1 : 0);
					}else{
						// missing data
						if (empty($text))
							$response = $this->pex->error($this->user->lang('sb_missing_text'));
						else
							$response = $this->pex->error($this->user->lang('sb_missing_char_id'));
					}
				}else{
					$response = $this->pex->error($this->user->lang('sb_missing_char_id'));
				}
			}else{
				$response = $this->pex->error('access denied');
			}
			return $response;
		}
	}
}
?>

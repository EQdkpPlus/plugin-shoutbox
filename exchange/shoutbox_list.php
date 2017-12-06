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
  | exchange_shoutbox_list
  +--------------------------------------------------------------------------*/
if (!class_exists('exchange_shoutbox_list')){
	class exchange_shoutbox_list extends gen_class{
		/* List of dependencies */
		public static $shortcuts = array('pex'=>'plus_exchange');

		/**
		* Additional options
		*/
		public $options = array();

		/**
		* get_shoutbox_list
		* GET Request for shoutbox entries
		*
		* @param   array   $params   Parameters array
		* @param   string  $body     body-array of request
		*
		* @returns array
		*/
		public function get_shoutbox_list($params, $arrBody){
			// set response
			$response = array('entries' => array());

			// be sure user is logged in
			if ($this->user->is_signedin()){
				// get the number of shoutbox entries to return
				$max_count = (isset($params['get']['number']) && intval($params['get']['number']) > 0) ? intval($params['get']['number']) : 10;
				// get sort direction
				$sort = (isset($params['get']['sort']) && $params['get']['sort'] == 'desc') ? 'desc' : 'asc';

				// get all shoutbox id's
				$shoutbox_ids = $this->pdh->get('shoutbox', 'id_list');
				if (is_array($shoutbox_ids)){
					// slice array
					$shoutbox_ids = array_slice($shoutbox_ids, 0, $max_count);

					// sort sliced array
					$shoutbox_ids = $this->pdh->sort($shoutbox_ids, 'shoutbox', 'date', $sort);

					// set root path
					$root = $this->env->httpHost.$this->env->server_path;

					// build entry array
					foreach ($shoutbox_ids as $shoutbox_id){
						$avatarimg = $this->pdh->get('user', 'avatarimglink', array($this->pdh->get('shoutbox', 'userid', array($shoutbox_id))));
						$response['entries']['entry:'.$shoutbox_id] = array(
							'id'			=> $shoutbox_id,
							'member_id'		=> $this->pdh->get('shoutbox', 'memberid', array($shoutbox_id)),
							'user_id'		=> $this->pdh->get('shoutbox', 'userid', array($shoutbox_id)),
							'user_avatar'	=> $this->pfh->FileLink((($avatarimg != "") ? $avatarimg : 'images/global/avatar-default.svg'), false, 'absolute'),
							'name'			=> $this->pdh->get('shoutbox', 'usermembername', array($shoutbox_id)),
							'text'			=> $this->pdh->geth('shoutbox', 'text', array($shoutbox_id, $root)),
							'date'			=> $this->time->date('Y-m-d H:i', $this->pdh->get('shoutbox', 'date', array($shoutbox_id))),
							'timestamp'		=> $this->pdh->get('shoutbox', 'date', array($shoutbox_id)),
						);
					}
				}
			}else{
				$response = $this->pex->error('access denied');
			}
			return $response;
		}
	}
}
?>

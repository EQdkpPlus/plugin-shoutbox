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
  | ShoutboxClass
  +--------------------------------------------------------------------------*/
if (!class_exists("ShoutboxClass")){
	class ShoutboxClass extends gen_class{

		/**
		* RSS Feed object
		*/
		private $rssFeed;

		/**
		* Required versions
		*/
		private $reqVersions = array(
			'php'	=> '5.0.0',
			'eqdkp'	=> '2.0'
		);

		/**
		* Output limit (number of entries to display)
		*/
		private $output_limit;
	
		/**
		* Portal-Module ID of the shoutbox
		*/
		private $module_id = 0;

		/**
		* Constructor
		*/
		public function __construct($module_id=0){
			$this->module_id = $module_id;
			if($this->module_id < 1) {
				$ids = $this->pdh->get('portal', 'id_list', array(array('path' => 'shoutbox')));
				$this->module_id = $ids[0];
			}

			require_once($this->root_path.'core/feed.class.php');
			$this->rssFeed = registry::register('Feed');
			$this->rssFeed->title			= $this->user->lang('shoutbox');
			$this->rssFeed->description		= $this->config->get('main_title').' - '.$this->user->lang('shoutbox');
			$this->rssFeed->link			= $this->env->buildlink();
			$this->rssFeed->feedfile		= $this->pfh->FileLink('shoutbox.xml', 'shoutbox', 'absolute');
			$this->rssFeed->published		= $this->time->time;
			$this->rssFeed->language		= 'de-DE';

			// get output limit
			$this->output_limit = 20;
		}

		/**
		* checkRequirements
		* Check the shoutbox requirements
		*
		* @returns true if success, otherwise error string
		*/
		public function checkRequirements(){
			// set defult to OK
			$result = true;

			// compare
			if (version_compare(phpversion(), $this->reqVersions['php'], "<")){
				$result = sprintf($this->user->lang('sb_php_version'), $this->reqVersions['php'], phpversion());
			}else if (version_compare($this->config->get('plus_version'), $this->reqVersions['eqdkp'], "<")){
				$result = sprintf($this->user->lang('sb_plus_version'), $this->reqVersions['eqdkp'],
				(($this->config->get('plus_version') > 0) ? $this->config->get('plus_version') : '[non-PLUS]'));
			}
			return $result;
		}

		/**
		* insertShoutboxEntry
		* Insert a shoutbox entry for current user or member
		*
		* @param    int     $usermember_id   user or member id
		* @param    string  $text            text to insert
		*
		* @returns  true if success, otherwise false
		*/
		public function insertShoutboxEntry($usermember_id, $text){
			// is user allowed to add a shoutbox entry?
			if ($this->user->is_signedin() && $this->user->check_auth('u_shoutbox_add', false)){
				// insert
				$shoutbox_id = $this->pdh->put('shoutbox', 'add', array($usermember_id, $text));
				if ($shoutbox_id === false)
					return false;

				// process hook queue
				$this->pdh->process_hook_queue();

				// recreate RSS
				$this->createRSS();

				return true;
			}
			return false;
		}

		/**
		* deleteShoutboxEntry
		* delete a shoutbox entry
		*
		* @param  int  $shoutbox_id  shoutbox entry id
		*/
		public function deleteShoutboxEntry($shoutbox_id){
			// is user owner of the shoutbox entry or is admin?
			if (($this->user->is_signedin() && $this->user->data['user_id'] == $this->pdh->get('shoutbox', 'userid', array($shoutbox_id))) ||
			($this->user->check_auth('a_shoutbox_delete', false))){
				$result = $this->pdh->put('shoutbox', 'delete', array($shoutbox_id));
				if (!$result)
					return false;

				// process hook queue
				$this->pdh->process_hook_queue();

				// recreate RSS
				$this->createRSS();

				return $result;
			}
			return false;
		}

		/**
		* deleteAllEntries
		* delete all shoutbox entries
		*/
		public function deleteAllEntries(){
			// is user allowed to delete?
			if ($this->user->is_signedin() && $this->user->check_auth('a_shoutbox_delete', false)){
				// get all shoutbox ids
				$shoutbox_ids = $this->pdh->get('shoutbox', 'id_list');
				if (is_array($shoutbox_ids)){
					foreach ($shoutbox_ids as $shoutbox_id)
						$this->pdh->put('shoutbox', 'delete', array($shoutbox_id));

					// process hook queue
					$this->pdh->process_hook_queue();

					// recreate RSS
					$this->createRSS();
				}
			}
		}

		/**
		* showShoutbox
		* show the complete shoutbox
		*
		* @param  string  $orientation  orientation vertical/horizontal
		*
		* @returns  string
		*/
		public function showShoutbox($orientation='vertical'){
			$htmlOut = '';

			// check user view permission
			if (!$this->user->check_auth('u_shoutbox_view', false)) return $htmlOut;

			// get ids
			$shoutbox_ids = $this->getShoutboxOutEntries();

			// get the layout
			$layout_file = $this->root_path.'plugins/shoutbox/includes/styles/sb_'.$orientation.'.class.php';
			if (file_exists($layout_file)){
				include_once($layout_file);
				$class_name = 'sb_'.$orientation;
				$shoutbox_style = registry::register($class_name, array($this->module_id, $shoutbox_ids));
			}

			// show shoutbox
			if ($shoutbox_style)
				$htmlOut .= $shoutbox_style->showShoutbox();

			// create RSS feed if they do not exist
			$rss_file = 'shoutbox.xml';
			$rss_path = $this->pfh->FileLink('rss/shoutbox.xml', 'eqdkp', 'relative');
			if (!is_file($rss_path))
				$this->createRSS();

			// add link to RSS
			$this->tpl->add_rssfeed($this->config->get('guildtag').' - '.$this->user->lang('shoutbox'), $rss_file,  array('u_shoutbox_view'));

			return $htmlOut;
		}

		/**
		* getContent
		* get the content of the shoutbox
		*
		* @param  string   $orientation  orientation vertical/horizontal
		* @param  string   $rpath        root path
		*
		* @returns  string
		*/
		public function getContent($orientation){
			// get shoutbox ids to display
			$shoutbox_ids = $this->getShoutboxOutEntries();

			// empty output
			$htmlOut = '';

			// get the layout
			$layout_file = $this->root_path.'plugins/shoutbox/includes/styles/sb_'.$orientation.'.class.php';
			if (file_exists($layout_file)){
				include_once($layout_file);
				$class_name = 'sb_'.$orientation;
				$shoutbox_style = registry::register($class_name, array($this->module_id, $shoutbox_ids));
			}

			// get content
			if ($shoutbox_style)
				$htmlOut .= $shoutbox_style->getContent();

			return $htmlOut;
		}
		
		public function loadMore($orientation, $intCount){
			// get shoutbox ids to display
			$shoutbox_ids = $this->pdh->get('shoutbox', 'id_list');
			if (is_array($shoutbox_ids)){
				$shoutbox_ids = $this->pdh->limit($shoutbox_ids, $intCount+20, 20);
				$shoutbox_ids = array_reverse($shoutbox_ids);
			}
			
			// empty output
			$htmlOut = '';
			
			// get the layout
			$layout_file = $this->root_path.'plugins/shoutbox/includes/styles/sb_'.$orientation.'.class.php';
			if (file_exists($layout_file)){
				include_once($layout_file);
				$class_name = 'sb_'.$orientation;
				$shoutbox_style = registry::register($class_name, array($this->module_id, $shoutbox_ids));
			}
			
			// get content
			if ($shoutbox_style)
				$htmlOut .= $shoutbox_style->getPosts();
				
				return $htmlOut;
		}

		/**
		* convertFromMemberToUser
		* convert all entries from member entries to user entries
		*
		* @returns  true if success, otherwise false
		*/
		public function convertFromMemberToUser(){
			// get all shoutbox ids
			$shoutbox_ids = $this->pdh->get('shoutbox', 'id_list');
			if (is_array($shoutbox_ids)){
				// for each entry, get the current member id, look up the corresponding user id and
				// update with user id
				foreach ($shoutbox_ids as $shoutbox_id){
					// get member id
					$member_id = $this->pdh->get('shoutbox', 'usermemberid', array($shoutbox_id));
					// lookup the user id for this member
					$user_id = $this->pdh->get('member', 'userid', array($member_id));
					// update with new user id
					$this->pdh->put('shoutbox', 'set_user', array($shoutbox_id, $user_id));
				}

				// process hook queue
				$this->pdh->process_hook_queue();

				// recreate RSS
				$this->createRSS();
			}
			return true;
		}

		/**
		* getShoutboxOutEntries
		* get the id list to display
		*
		* @returns  array(ids)
		*/
		private function getShoutboxOutEntries(){
			$shoutbox_out = array();

			// get all shoutbox id's
			$shoutbox_ids = $this->pdh->get('shoutbox', 'id_list');
			if (is_array($shoutbox_ids)){
				$shoutbox_count = count($shoutbox_ids);
				$output_count = min($this->output_limit, $shoutbox_count);

				// copy the last n elements to the output entry
				for ($i = 0; $i < $output_count; $i++)
					$shoutbox_out[] = $shoutbox_ids[$i];
			}
			return $shoutbox_out;
		}

		/**
		* createRSS
		* create RSS feed
		*/
		private function createRSS(){
			// get shoutbox ids
			$shoutbox_ids = $this->getShoutboxOutEntries();
			if (is_array($shoutbox_ids)){
				// create RSS feed item
				foreach ($shoutbox_ids as $shoutbox_id){
					$rssitem = registry::register('feeditems', array(), $shoutbox_id);
					$rssitem->title			= $this->pdh->get('shoutbox', 'usermembername', array($shoutbox_id));
					$rssitem->description	= $this->pdh->geth('shoutbox', 'text', array($shoutbox_id));
					$rssitem->link			= $this->rssFeed->link;
					$rssitem->published		= $this->pdh->get('shoutbox', 'date', array($shoutbox_id));
					$rssitem->author		= $this->pdh->get('shoutbox', 'usermembername', array($shoutbox_id));
					$rssitem->source		= $this->rssFeed->link;
					$this->rssFeed->addItem($rssitem);
				}
			}
			// save RSS
			$this->rssFeed->save($this->pfh->FilePath('rss/shoutbox.xml', 'eqdkp'), false);
		}
	}
}
?>
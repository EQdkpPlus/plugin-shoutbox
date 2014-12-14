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
	die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_w_shoutbox
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_w_shoutbox')){
	class pdh_w_shoutbox extends pdh_w_generic{

		/*
		* Number of chars to wrap after
		*/
		private $wordwrap;

		/**
		* Constructor
		*/
		public function __construct(){
			parent::__construct();

			// set default wordwrap
			$this->wordwrap = 20;
		}

		/**
		* add
		* Add a new shoutbox entry
		*
		* @param  int     $usermember_id  User or Member ID
		* @param  string  $text           Text to insert
		*
		* @returns mixed, on success shoutbox id, else false
		*/
		public function add($usermember_id, $text){
			// cleanup text
			$text = $this->cleanupText($text);

			// add to database
			$sql_data = array(
				'user_or_member_id'	=> $usermember_id,
				'shoutbox_date'		=> $this->time->time,
				'shoutbox_text'		=> $text
			);

			$objQuery = $this->db->prepare('INSERT INTO `__shoutbox` :p')->set($sql_data)->execute();

			if (!$objQuery)
				return false;

			// do hooks
			$this->pdh->enqueue_hook('shoutbox_update');

			return $objQuery->insertId;
		}

		/**
		* delete
		* Delete a shoutbox entry from db
		*
		* @param  int   $shoutbox_id  Shoutbox ID
		*
		* @returns boolean
		*/
		public function delete($shoutbox_id){
			// delete from db
			$objQuery = $this->db->prepare('DELETE FROM `__shoutbox` WHERE shoutbox_id=?')->execute($shoutbox_id);
			if (!$objQuery)
				return false;

			// do hooks
			$this->pdh->enqueue_hook('shoutbox_update');

			return true;
		}

		/**
		* set_user
		* Updates an entry and sets member id to user id
		*
		* @param  int   $shoutbox_id  Shoutbox ID
		* @param  int   $user_id      User ID
		*
		* @returns boolean
		*/
		public function set_user($shoutbox_id, $user_id){
			$objQuery = $this->db->prepare('UPDATE `__shoutbox`
				SET `user_or_member_id`=?
				WHERE shoutbox_id=?')->execute($user_id, $shoutbox_id);

			if (!$objQuery)
				return false;

			// do hooks
			$this->pdh->enqueue_hook('shoutbox_update');

			return true;
		}

		/**
		* cleanupText
		* Cleans up the text to insert to database
		*
		* @param  string   $text  Text to insert
		*
		* @returns string
		*/
		private function cleanupText($text){
			// auto create url bbcode for URLs - by GodMod
			$text = $this->autolink($text);

			// wrap words (do own handling cause of bbcodes)
			//$cleanup_text = $this->shoutbox_wordwrap($text, $this->wordwrap, "\n", true);
			$cleanup_text = $text;

			return trim($cleanup_text);
		}

		/**
		* autolink
		* Converts an URL to appropriate BB-Code
		*
		* @param  string   $str Text to insert
		*
		* @returns string
		*/
		private function autolink($str) {
			$str = ' ' . $str;
			$str = preg_replace(
				'`([^"=\'>])(((http|https|ftp)://|www.)[^\s<]+[^\s<\.)])`i',
				'$1[url="$2"]$2[/url]',
				$str
			);
			$str = substr($str, 1);
			$str = preg_replace('`url=\"www`','url="http://www',$str);
			// fügt http:// hinzu, wenn nicht vorhanden
			return trim($str);
		}

		/**
		* shoutbox_wordwrap
		* Wrap words ignoring bb code
		*
		* @param   string   $text   Text to wrap
		* @param   integer  $width  Max length of one line
		* @param   string   $break  String to insert for line break, default '\n'
		* @param   boolean  $cut    cut inside of words?
		*
		* @return  string
		*/
		private function shoutbox_wordwrap($text, $width, $break="\n", $cut=false){
			// explode by spaces
			$element_array = explode(' ', $text);
			$count = count($element_array);

			// loop through all the elements
			$wraped_text = '';
			foreach($element_array as $org_text){
				// explode by \n
				$inner_element_array = explode("\n", $org_text);
				foreach($inner_element_array as $inner_org_text){
					// strip bbcode from text
					$striped_text = preg_replace('#\[[\w=]+\](.*?)\[/[\w]+\]#si', '\1', $inner_org_text);
					// get striped size
					$striped_size = strlen($striped_text);

					// do not wrap image/urls/emails
					$inner_cut = $cut;
					if (preg_match('#\[img\](.*?)\[/img\]#si', $inner_org_text) ||
						preg_match('#\[url=?"?(.*?)"?\](.*?)\[/url\]#si', $inner_org_text) ||
					preg_match('#\[email\](.*?)\[/email\]#si', $inner_org_text)){
						$inner_cut = false;
					}

					// fits?
					if ($striped_size > $width){
						$new_text = wordwrap($striped_text, $width, $break, $inner_cut);
						// replace in original text
						$new_text = str_replace($striped_text, $new_text, $inner_org_text);
					}else{
						// fit, so just take original text
						$new_text = $inner_org_text;
					}

					// append to output
					$wraped_text .= $new_text."\n";
				}
				// replace last char by space
				$wraped_text[strlen($wraped_text)-1]= ' ';
			}

			return $wraped_text;
		}
	} //end class
} //end if class not exists
?>

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

if (!class_exists('sb_style_base')){
	include_once(registry::get_const('root_path').'plugins/shoutbox/includes/styles/sb_style_base.class.php');
}

/*+----------------------------------------------------------------------------
  | sb_vertical
  +--------------------------------------------------------------------------*/
if (!class_exists("sb_vertical")){
	class sb_vertical extends sb_style_base{

		/**
		* layoutShoutbox
		* get the complete shoutbox layout
		*
		* @return  string
		*/
		protected function layoutShoutbox(){
			// default is empty output
			$htmlOut = '';

			// get location of form
			$form_location = 'bottom';

			// is input on top (and user can add entries) append form first
			if ($form_location == 'top' && $this->user->check_auth('u_shoutbox_add', false) && $this->user->is_signedin()){
				$htmlOut .= $this->getForm();
			}

			// content table
			$htmlOut .= '<div id="htmlShoutboxTable">';
			$htmlOut .= $this->getContent();
			$htmlOut .= '</div>';

			// is input below (and user can add entries) append form
			if ($form_location == 'bottom' && $this->user->check_auth('u_shoutbox_add', false)){
				$htmlOut .= $this->getForm();
			}

			return $htmlOut;
		}

		/**
		* layoutContent
		* layout the content only of the shoutbox
		*
		* @param  string  $this->server_path  root path
		*
		* @return  string
		*/
		protected function layoutContent(){
			// get location of form
			$form_location = 'bottom';
			// empty output
			$htmlOut = '';

			// display
			if (is_array($this->shoutbox_ids) && count($this->shoutbox_ids) > 0){

				if($form_location != 'top'){
					$this->shoutbox_ids = array_reverse($this->shoutbox_ids);
				}
				
				// output table header
				$htmlOut .= '<div class="sb sb_vertical" data-count="0" data-orientation="vertical">';

				$htmlOut .= $this->layoutPosts();
				

				// output table footer
				$htmlOut .= '</div>';
				
				$this->tpl->add_js('
			$(\'.sb\').scrollTop($(\'.sb\')[0].scrollHeight);
						
			shoutboxInfiniteScroll();
						
			function del_shoutbox_entry(postid){
				$(\'#del_shoutbox\').ajaxSubmit({
							target: \'#htmlShoutboxTable\',
							url:\''.$this->server_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'&sb_delete=\'+postid+\'&sb_orientation=vertical\',
							beforeSubmit: function(formData, jqForm, options) {
								deleteShoutboxRequest( postid, \''.$this->user->lang('delete').'\');
							},
							 success: function() {
						      	$(\'.sb\').scrollTop($(\'.sb\')[0].scrollHeight);
								shoutboxInfiniteScroll();
						    }
				});
						
			}
');
				
			}else{
				$htmlOut .= $this->user->lang('sb_no_entries');
			}
			return $htmlOut;
		}
		
		protected function layoutPosts(){
			$htmlOut = "";
			
			// output
			foreach ($this->shoutbox_ids as $shoutbox_id){
				
				$htmlOut .= '<div class="sb_entry_container">';
				
				$htmlOut .= '<div class="sb_entry">';
				// if admin or own entry, ouput delete link
				if ($this->user->data['user_id'] == $this->pdh->get('shoutbox', 'userid', array($shoutbox_id)) || $this->user->check_auth('a_shoutbox_delete', false)){
					
					// Java Script for delete
					$htmlOut .= '<span class="small bold floatRight hand" onclick="del_shoutbox_entry('.$shoutbox_id.')">
							<span id="shoutbox_delete_button_'.$shoutbox_id.'">
							<i class="fa fa-times-circle fa-lg icon-grey" title="'.$this->user->lang('delete').'"></i>
							</span>
							</span>';
				}
				
				// output date as well as User and text
				$useravatar = $this->pdh->geth('shoutbox', 'useravatar', array($shoutbox_id));
				if ($useravatar) $htmlOut .= '<div class="user-avatar-small user-avatar-border floatLeft">'.$useravatar.'</div>';
				$htmlOut .= '<div class="sb_date small'.(($useravatar) ? ' sb_text_margin' : '').'">'.$this->pdh->geth('shoutbox', 'usermembername', array($shoutbox_id)).'<br />'. $this->pdh->geth('shoutbox', 'date', array($shoutbox_id, true)).'</div>';
				$htmlOut .= '<div class="sb_text">'. $this->pdh->geth('shoutbox', 'text', array($shoutbox_id)).'</div>';
				$htmlOut .= '</div><div class="clear"></div>';
				
				$htmlOut .= '  </div>';
			}
			
			return $htmlOut;
		}

		/**
		* jCodeOrientation
		* get the orientation for the JCode output
		*
		* @return  string
		*/
		protected function jCodeOrientation(){
			return 'vertical';
		}

		/**
		* getForm
		* get the Shoutbox <form>
		*
		* @return  string
		*/
		private function getForm(){

			// get location and max text length
			$form_location = 'bottom';
			
			// only display form if user has members assigned to or if user modus is selected
			$members = $this->pdh->get('member', 'connection_id', array($this->user->data['user_id']));
			if ((is_array($members) && count($members) > 0) ||
			$this->config->get('sb_use_users', 'shoutbox')){
				$out = "";
				
				// html
				$out .= '<form id="reload_shoutbox" name="reload_shoutbox" action="'.$this->server_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'" method="post">
					</form>
					<form id="Shoutbox" name="Shoutbox" action="'.$this->server_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'" method="post">
					<div>'.$this->getFormName();

				// archive link? (User must be logged in to see archive link)
				if ($this->config->get('show_archive', 'pmod_'.$this->module_id) && $this->user->is_signedin()){
					$out .= $this->getArchiveLink();
				}

				$out .= '<span class="small bold hand floatRight" onclick="$(\'#reload_shoutbox\').ajaxSubmit({
						target: \'#htmlShoutboxTable\',
						url:\''.$this->server_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'&amp;sb_orientation=vertical\',
						beforeSubmit: function(formData, jqForm, options) {
							reloadShoutboxRequest();
							},
							success: function() {
								reloadShoutboxFinished(\''.$this->user->lang('sb_reload').'\');
							}
						});">
						<span id="shoutbox_reload_button"><i class="fa fa-refresh fa-lg" title="'.$this->user->lang('sb_reload').'"></i>
						</span>
						</span>
						</div>
						<div class="center">
							<textarea class="input" name="sb_text" style="width: 90%;" rows="1" cols="1" placeholder="'.$this->user->lang('sb_write_post').'"></textarea>
						</div>
						<div class="center">
							<input type="hidden" name="sb_orientation" value="vertical"/>
							<span id="shoutbox_button"></span>
						</div>
					</form>';
				if ($form_location == 'top'){
					$out .= '<div class="contentDivider"></div>';
				}
			}else if ($this->config->get('sb_use_users', 'shoutbox')){
				$out .= '<div class="center">'.$this->user->lang('sb_no_character_assigned').'</div>';
			}
			return $out;
		}

		/**
		* getFormName
		* get the Shoutbox <form> Names
		*
		* @return  string
		*/
		private function getFormName(){
			// for anonymous user, just return empty string
			$outHtml = '';

			// if we have users, just return the single user, otherwise use member dropdown
			if ($this->config->get('sb_use_users', 'shoutbox')){
				// show name as text and user id as hidden value
				$username = $this->pdh->get('user', 'name', array($this->user->data['user_id']));
				$outHtml .= '<input type="hidden" name="sb_usermember_id" value="'.$this->user->data['user_id'].'"/>';
			}else{
				// get member array
				$members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id']))));
				if (is_array($members)){
					$membercount = count($members);

					// if more than 1 member, show dropdown box
					if ($membercount > 1){
						// show dropdown box
						$outHtml .= (new hdropdown('sb_usermember_id', array('options' => $members, 'value' => $this->pdh->get('user', 'mainchar', array($this->user->id)))))->output();
					}
					// if only one member, show just member
					else if ($membercount == 1){
						// show name as text and member id as hidden value
						$outHtml .= '<input type="hidden" name="sb_usermember_id" value="'.key($members).'"/>'.
							current($members);
					}
				}
			}
			return $outHtml;
		}

		/**
		* getArchiveLink
		* get the archive link text
		*
		* @return  string
		*/
		private function getArchiveLink(){
			$html = '<i class="fa fa-history hand" onclick="window.location.href=\''.$this->server_path.'plugins/shoutbox/archive.php'.$this->SID.'\'" title="'.$this->user->lang('sb_archive').'"></i>';

			return $html;
		}
	}
}
?>
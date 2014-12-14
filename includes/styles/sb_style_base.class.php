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
  | sb_style_base
  +--------------------------------------------------------------------------*/
if (!class_exists("sb_style_base")){
	abstract class sb_style_base extends gen_class{
	
		/**
		* Portal-Module ID of the shoutbox
		*/
		protected $module_id = 0;

		/**
		* Output Shoutbox ids to display
		*/
		protected $shoutbox_ids = array();

		/**
		* Constructor
		*
		* @param  integer  $output_limit  Number of shoutbox id's to display
		*/
		public function __construct($module_id, $shoutbox_ids=array()){
			$this->module_id = $module_id;

			if (is_array($shoutbox_ids)){
				$this->shoutbox_ids = $shoutbox_ids;
			}
		}

		/**
		* showShoutbox
		* show the complete shoutbox
		*
		* @return  string
		*/
		public function showShoutbox(){
			// output javascript code
			$this->shoutboxJCode();

			// output layout
			return $this->layoutShoutbox();
		}

		/**
		* getContent
		* get the content only of the shoutbox
		*
		* @param  string  $rpath  root path
		*
		* @return  string
		*/
		public function getContent(){
			// the delete form
			$htmlOut = '<form id="del_shoutbox" name="del_shoutbox" action="'.$this->server_path.'plugins/shoutbox/shoutbox.php" method="post">
				</form>';

			// layout content
			$htmlOut .= $this->layoutContent();

			return $htmlOut;
		}

		/**
		* layoutShoutbox
		* get the complete shoutbox layout
		*
		* @return  string
		*/
		protected abstract function layoutShoutbox();

		/**
		* layoutContent
		* layout the content only of the shoutbox
		*
		* @return  string
		*/
		protected abstract function layoutContent();

		/**
		* jCodeOrientation
		* get the orientation for the JCode output
		*
		* @return  string
		*/
		protected abstract function jCodeOrientation();

		/**
		* shoutboxJCode
		* output the Java Code for the Shoutbox
		*/
		private function shoutboxJCode(){
			// set autoreload (0 = disable)
			$autoreload = ($this->config->get('autoreload', 'pmod_'.$this->module_id) != '') ? intval($this->config->get('autoreload', 'pmod_'.$this->module_id)) : 0;
			$autoreload = ($autoreload < 600 ? $autoreload : 0);
			$autoreload = $autoreload * 1000; // to ms

			// set maxlength
			$max_text_length = $this->config->get('max_text_length', 'pmod_'.$this->module_id);
			$max_text_length = (is_numeric($max_text_length)) ? intval($max_text_length) : 160;

			$jscode  = "$('#Shoutbox').ajaxForm({
							target: '#htmlShoutboxTable',
							beforeSubmit:  function(formData, jqForm, options) {
								showShoutboxRequest('".$this->user->lang('sb_save_wait')."');
							},
							success: function() {
								showShoutboxFinished('".$this->user->lang('sb_submit_text')."', '".$this->user->lang('sb_reload')."');
							}
						});

						$(document).on('keypress', 'textarea[name=sb_text]', function(e){
							var maxlength = ".$max_text_length.";
							var value = $(this).val();

							// Trim
							if (value.length > maxlength){
								$(this).val(value.slice(0, maxlength));
							}

							while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css(\"borderTopWidth\")) + parseFloat($(this).css(\"borderBottomWidth\"))) {
								$(this).height($(this).height()+5);
							};
							if (e.which == 13 && !e.shiftKey) {
								e.preventDefault();
								$('form#Shoutbox').submit();
								return false;
							}
						});";
			if ($autoreload > 0){
				$jscode .= "setInterval(function() {
					shoutboxAutoReload('".$this->user->lang('sb_reload')."', '".$this->jCodeOrientation()."');
					}, ".$autoreload.");";
			}

			$this->tpl->add_js($jscode, 'docready');
			$this->tpl->js_file($this->root_path.'plugins/shoutbox/includes/javascripts/shoutbox.js');
		}
	}
}
?>
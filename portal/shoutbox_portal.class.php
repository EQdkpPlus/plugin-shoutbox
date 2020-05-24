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
  | shoutbox_portal
  +--------------------------------------------------------------------------*/
class shoutbox_portal extends portal_generic{

	/**
	* Portal path
	*/
	protected static $path = 'shoutbox';
	/**
	* Portal data
	*/
	protected static $data = array(
		'name'			=> 'Shoutbox Module',
		'version'		=> '0.3.3',
		'author'		=> 'Aderyn',
		'contact'		=> 'Aderyn@gmx.net',
		'description'	=> 'Display a shoutbox',
		'lang_prefix'	=> 'sb_'
	);
	
	protected static $apiLevel = 20;

	/**
	* Positions this Module may appear
	*/
	protected static $positions = array('left1', 'left2', 'right', 'middle', 'bottom');
	/**
	* Settings
	*/
	protected $settings = array(
		'show_archive'	=> array(
			'type'		=> 'radio',
		),
		'box_height'	=> array(
			'type'		=> 'spinner',
			'default'	=> 250,
		),
		'max_text_length'	=> array(
			'type'		=> 'spinner',
			'default'	=> 160,
		),
		'autoreload' => array(
			'type'	=> 'text',
			'size'	=> '3',
		),
	);

	/**
	* Installation
	*/
	protected static $install = array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '1',
	);

	/**
	* output
	* Get the portal output
	*
	* @returns string
	*/
	public function output(){
		// initialize output
		$output = '';

		// check if shoutbox is installed
		if ($this->pm->check('shoutbox', PLUGIN_INSTALLED)){
			if (!class_exists('ShoutboxClass'))
				include_once($this->root_path.'plugins/shoutbox/includes/shoutbox.class.php');

			// create shoutbox
			$shoutbox = registry::register('ShoutboxClass', array($this->id));

			// do requirements check
			$requirementscheck = $shoutbox->checkRequirements();
			if ($requirementscheck !== true){
				$output = $requirementscheck;
			}
			//do permission check
			elseif (!$this->user->check_auth('u_shoutbox_view', false)){
				$output = $this->user->lang('sb_no_view_permission');
			}else{
				// output depending on position
				$orientation = '';
				switch ($this->position){
					case 'left':
					case 'right':
						$orientation = 'vertical';
					break;
					case 'middle':
					case 'bottom':
						$orientation = 'horizontal';
					break;
					default:
						$orientation = 'vertical';
					break;
				}

				// return the output for module
				$output = $shoutbox->showShoutbox($orientation);
				
				
				$intHeight = ($this->config('box_height')) ? $this->config('box_height') : 250;
				
				$this->tpl->add_css(
				".sb_vertical .sb_text_margin {
					margin-left: 38px;
				}


				.sb_horizontal .sb_content_container{
					padding: 2px 5px 2px 5px;
					background: #FFFFFF;
					border: #ccc solid 1px;
					-webkit-border-radius: 4px;
					-moz-border-radius: 4px;
					border-radius: 4px;
					position: relative;
				}

				.sb_text {
					margin-top: 5px;
					padding-bottom: 3px;
					word-break: normal;
    				overflow-wrap: anywhere;
					word-wrap: break-all;
				}

				@supports (-ms-ime-align:auto) {
				    .sb_text {
				        word-break: break-all;
				    }
				}

				.sb_entry_container {
					margin-top: 5px;
				} 

				.sb_with_avatar.sb_content_container {
					margin-left: 60px;
				}

				.sb {
					max-height: ".$intHeight."px;
					overflow-y: auto;
					display: block;
					padding-right: 10px;
				}

				.sb_vertical .sb_text {
					clear: both;
					padding-top: 5px;
					border-bottom: 1px solid ".$this->user->style['table_border_color']."; 
				}


				.sb_vertical .user-avatar-small, .sb_vertical .user-avatar-small, .sb_vertical .user-avatar.small {
					max-height: 26px;
				}

				.sb_horizontal .sb_with_avatar.sb_content_container:after{
					content: \"\";
					position: absolute;
					top: 10px;
					left: -15px;
					border-style: solid;
					border-width: 11px 15px 11px 0;
					border-color: transparent #FFFFFF;
					display: block;
					width: 0;
					z-index: 1;
				}

				.sb_horizontal .sb_with_avatar.sb_content_container:before{
					content: \"\";
					position: absolute;
					top: 10px;
					left: -16px;
					border-style: solid;
					border-width: 11px 15px 11px 0;
					border-color: transparent #ccc;
					display: block;
					width: 0;
					z-index: 0;
				}"
				);
			}
		}else{
			$output = $this->user->lang('sb_plugin_not_installed');
		}
		return $output;
	}
}

?>
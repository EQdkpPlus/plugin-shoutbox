<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2012-05-30 23:41:49 +0200 (Mi, 30. Mai 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11795 $
 *
 * $Id: sb_horizontal.class.php 11795 2012-05-30 21:41:49Z wallenium $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists('sb_style_base'))
{
  include_once(registry::get_const('root_path').'plugins/shoutbox/includes/styles/sb_style_base.class.php');
}

/*+----------------------------------------------------------------------------
  | sb_horizontal
  +--------------------------------------------------------------------------*/
if (!class_exists("sb_horizontal"))
{
  class sb_horizontal extends sb_style_base
  {
    /**
     * __dependencies
     * Get module dependencies
     */
    public static function __shortcuts()
    {
      $shortcuts = array('user', 'config', 'pdh', 'html');
      return array_merge(parent::$shortcuts, $shortcuts);
    }

    /**
     * layoutShoutbox
     * get the complete shoutbox layout
     *
     * @return  string
     */
    protected function layoutShoutbox()
    {
      // default is empty output
      $htmlOut = '';

      // get location of form
      $form_location = ($this->config->get('sb_input_box_location') != '') ? $this->config->get('sb_input_box_location') : 'top';

      // is input on top (and user can add entries) append form first
      if ($form_location == 'top' && $this->user->check_auth('u_shoutbox_add', false) && $this->user->is_signedin())
      {
        $htmlOut .= $this->getForm();
      }

      // content table
      $htmlOut .= '<div id="htmlShoutboxTable">';
      $htmlOut .= $this->getContent();
      $htmlOut .= '</div>';

      // archive link? (User must be logged in to see archive link)
      if ($this->config->get('sb_show_archive') && $this->user->is_signedin())
      {
        $htmlOut .= $this->getArchiveLink();
      }

      // is input below (and user can add entries) append form
      if ($form_location == 'bottom' && $this->user->check_auth('u_shoutbox_add', false))
      {
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
    protected function layoutContent()
    {
      // get location of form
      $form_location = ($this->config->get('sb_input_box_location') != '') ? $this->config->get('sb_input_box_location') : 'top';

      // empty output
      $htmlOut = '';

      // display
      if (is_array($this->shoutbox_ids) && count($this->shoutbox_ids) > 0)
      {
       
		$this->tpl->add_css(
		"
		"
		);
	   // output table header
        $htmlOut .= '<table width="100%" class="colorswitch hoverrows sb_horizontal">';


        // output
        foreach ($this->shoutbox_ids as $shoutbox_id)
        {

          $htmlOut .= '<tr>
                         <td>';
			
		  $htmlOut .= '<div class="sb_entry">';
			
          // output date as well as User and text
		  $useravatar = $this->pdh->geth('shoutbox', 'useravatar', array($shoutbox_id));
		  if ($useravatar) $htmlOut .= '<div class="user-avatar-small user-avatar-border floatLeft" title="'.$this->pdh->get('shoutbox', 'usermembername', array($shoutbox_id)).'">'.$useravatar.'</div>';
		   $htmlOut .= '<div class="sb_content_container'.(($useravatar) ? ' sb_with_avatar' : '').'"><div class="sb_date small">'.$this->pdh->geth('shoutbox', 'usermembername', array($shoutbox_id)).', '. $this->pdh->geth('shoutbox', 'date', array($shoutbox_id, true));
		   		   
		   // if admin or own entry, ouput delete link
		   if ($this->user->data['user_id'] == $this->pdh->get('shoutbox', 'userid', array($shoutbox_id)) ||
		   $this->user->check_auth('a_shoutbox_delete', false))
		   {
		   	 
		   	// Java Script for delete
		   	$htmlOut .= '<span class="small bold floatRight hand sb_delete_btn" onclick="$(\'#del_shoutbox\').ajaxSubmit(
                           {
                             target: \'#htmlShoutboxTable\',
                             url:\''.$this->server_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'&amp;sb_delete='.$shoutbox_id.'&amp;sb_orientation=horizontal\',
                             beforeSubmit: function(formData, jqForm, options) {
                               deleteShoutboxRequest( '.$shoutbox_id.', \''.$this->user->lang('delete').'\');
                             }
                           }); ">
                           <span id="shoutbox_delete_button_'.$shoutbox_id.'">
							<i class="icon-remove-sign icon-large icon-grey" title="'.$this->user->lang('delete').'"></i>
                           </span>
                         </span>';
		   }
		   
		   $htmlOut .= '</div><div class="sb_text'.(($useravatar) ? ' sb_text_margin' : '').' ">'. $this->pdh->geth('shoutbox', 'text', array($shoutbox_id)).'</div></div>';
		   $htmlOut .= '</div><div class="clear"></div>';
		  
		   $htmlOut .= '  </td>
                       </tr>';
        }

        // output table footer
        $htmlOut .= '</table>';
      }
      else
      {
        $htmlOut .= $this->user->lang('sb_no_entries');
      }

      return $htmlOut;
    }

    /**
     * jCodeOrientation
     * get the orientation for the JCode output
     *
     * @return  string
     */
    protected function jCodeOrientation()
    {
      return 'horizontal';
    }

    /**
     * getForm
     * get the Shoutbox <form>
     *
     * @return  string
     */
    private function getForm()
    {

      // get location and max text length
      $form_location = ($this->config->get('sb_input_box_location') != '') ? $this->config->get('sb_input_box_location') : 'top';

      // only display form if user has members assigned to or if user modus is selected
      $members = $this->pdh->get('member', 'connection_id', array($this->user->data['user_id']));
      if ((is_array($members) && count($members) > 0) ||
          $this->config->get('sb_use_users', 'shoutbox'))
      {
      	$out = "";
      	if ($form_location == 'bottom')
      	{
      		$out .= '<div class="contentDivider"></div>';
      	}
      	
        // html
        $out .= '<form id="reload_shoutbox" name="reload_shoutbox" action="'.$this->server_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'" method="post">
                </form>
                <form id="Shoutbox" name="Shoutbox" action="'.$this->server_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'" method="post">
                	<div>'.$this->getFormName().'
                		<span class="small bold hand floatRight" onclick="$(\'#reload_shoutbox\').ajaxSubmit(
                         {
                           target: \'#htmlShoutboxTable\',
                           url:\''.$this->server_path.'plugins/shoutbox/shoutbox.php'.$this->SID.'&amp;sb_orientation=horizontal\',
                           beforeSubmit: function(formData, jqForm, options) {
                             reloadShoutboxRequest();
                           },
                           success: function() {
                             reloadShoutboxFinished(\''.$this->user->lang('sb_reload').'\');
                           }
                         });">
                         <span id="shoutbox_reload_button"><i class="icon-refresh icon-large" title="'.$this->user->lang('sb_reload').'"></i>
                         </span>
                       </span>		
                	</div>
                	<div class="center">
                       <textarea class="input" name="sb_text" style="width: 96%;" rows="1" cols="1"></textarea>
                     </div>	
                	 <div class="center">
                       <input type="hidden" name="sb_orientation" value="horizontal"/>
                       <span id="shoutbox_button"></span>
                     </div>
                 </form>';
        if ($form_location == 'top')
        {
        	$out .= '<div class="contentDivider"></div>';
        }
        
      }
      else if ($this->config->get('sb_use_users', 'shoutbox'))
      {
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
    private function getFormName()
    {
      // for anonymous user, just return empty string
      $outHtml = '';

      // if we have users, just return the single user, otherwise use member dropdown
      if ($this->config->get('sb_use_users', 'shoutbox'))
      {
        // show name as text and user id as hidden value
        $username = $this->pdh->get('user', 'name', array($this->user->data['user_id']));
        $outHtml .= '<input type="hidden" name="sb_usermember_id" value="'.$this->user->data['user_id'].'"/>';
      }
      else
      {
        // get member array
        $members = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($this->user->data['user_id']))));
        if (is_array($members))
        {
          $membercount = count($members);

          // if more than 1 member, show dropdown box
          if ($membercount > 1)
          {
            // show dropdown box
            $outHtml .= $this->html->DropDown('sb_usermember_id', $members, $this->pdh->get('user', 'mainchar', array($this->user->id)));
          }
          // if only one member, show just member
          else if ($membercount == 1)
          {
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
    private function getArchiveLink()
    {
      $htmlOut = '<div class="center">
      			<button type="button" onclick="window.location.href=\''.$this->server_path.'plugins/shoutbox/archive.php'.$this->SID.'\'"><i class="icon-folder-open"></i>'.$this->user->lang('sb_archive').'</button>
              </div>';

      return $htmlOut;
    }

  }
}
?>
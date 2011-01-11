<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2008 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev$
 *
 * $Id$
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists('sb_style_base'))
{
  include_once($eqdkp_root_path.'plugins/shoutbox/includes/styles/sb_style_base.class.php');
}

/*+----------------------------------------------------------------------------
  | sb_vertical
  +--------------------------------------------------------------------------*/
if (!class_exists("sb_vertical"))
{
  class sb_vertical extends sb_style_base
  {
    /**
     * layoutShoutbox
     * get the complete shoutbox layout
     *
     * @return  string
     */
    protected function layoutShoutbox()
    {
      global $core, $user;

      // default is empty output
      $htmlOut = '';

      // get location of form
      $form_location = ($core->config['sb_input_box_location'] != '') ? $core->config['sb_input_box_location'] : 'top';

      // is input on top (and user can add entries) append form first
      if ($form_location == 'top' && $user->check_auth('u_shoutbox_add', false))
      {
        $htmlOut .= $this->getForm();
      }

      // content table
      $htmlOut .= '<div id="htmlShoutboxTable">';
      $htmlOut .= $this->getContent();
      $htmlOut .= '</div>';

      // archive link? (User must be logged in to see archive link)
      if ($core->config['sb_show_archive'] && $user->data['user_id'] != ANONYMOUS)
      {
        $htmlOut .= $this->getArchiveLink();
      }

      // is input below (and user can add entries) append form
      if ($form_location == 'bottom' && $user->check_auth('u_shoutbox_add', false))
      {
        $htmlOut .= $this->getForm();
      }

      return $htmlOut;
    }

    /**
     * layoutContent
     * layout the content only of the shoutbox
     *
     * @param  string  $root_path  root path
     *
     * @return  string
     */
    public function layoutContent($root_path)
    {
      global $user, $core, $SID, $pdh, $eqdkp_root_path;

      // get location of form
      $form_location = ($core->config['sb_input_box_location'] != '') ? $core->config['sb_input_box_location'] : 'top';

      // empty output
      $htmlOut = '';

      // display
      if (is_array($this->shoutbox_ids) && count($this->shoutbox_ids) > 0 && is_dir($root_path))
      {
        // output table header
        $htmlOut .= '<table width="100%" border="0" cellspacing="1" cellpadding="2">';

        // input above? If true, insert a space row
        if ($form_location == 'top' && $user->check_auth('u_shoutbox_add', false))
          $htmlOut .= '<tr><th>&nbsp;</th></tr>';

        // output
        foreach ($this->shoutbox_ids as $shoutbox_id)
        {
          // get class for row
          $class = $core->switch_row_class();

          $htmlOut .= '<tr class="'.$class.'" onmouseout="this.className=\''.$class.'\';" onmouseover="this.className=\'rowHover\';">
                         <td>';

          // if admin or own entry, ouput delete link
          if ($user->data['user_id'] == $pdh->get('shoutbox', 'userid', array($shoutbox_id)) ||
              $user->check_auth('a_shoutbox_delete', false))
          {
            $img = $root_path.'images/global/delete.png';

            // Java Script for delete
            $htmlOut .= '<span class="small bold floatRight hand" onclick="$(\'#del_shoutbox\').ajaxSubmit(
                           {
                             target: \'#htmlShoutboxTable\',
                             url:\''.$root_path.'plugins/shoutbox/shoutbox.php'.$SID.'&amp;sb_delete='.$shoutbox_id.'&amp;sb_root='.rawurlencode($root_path).'&amp;sb_orientation=vertical\',
                             beforeSubmit: function(formData, jqForm, options) {
                               deleteShoutboxRequest(\''.$root_path.'\', '.$shoutbox_id.', \''.$user->lang('delete').'\');
                             }
                           }); ">
                           <span id="shoutbox_delete_button_'.$shoutbox_id.'">
                             <img src="'.$img.'" alt="'.$user->lang('delete').'" title="'.$user->lang('delete').'"/>
                           </span>
                         </span>';
          }

          // output date as well as User and text
          $htmlOut .= $pdh->geth('shoutbox', 'date', array($shoutbox_id, $core->config['sb_show_date'])).
                      '<br/>'.
                      $pdh->geth('shoutbox', 'usermembername', array($shoutbox_id)).
                      ':<br/>'.
                      $pdh->geth('shoutbox', 'text', array($shoutbox_id, $root_path));

          $htmlOut .= '  </td>
                       </tr>';
        }

        // output table footer
        $htmlOut .= '</table>';
      }
      else
      {
        $htmlOut .= '<table width="100%" border="0" cellspacing="1" cellpadding="2">
                       <tr class="'.$core->switch_row_class().'">
                         <td><div class="center">'.$user->lang('sb_no_entries').'</div></td>
                       </tr>
                     </table>';
      }

      return $htmlOut;
    }

    /**
     * getForm
     * get the Shoutbox <form>
     *
     * @param  string  $rpath  root path
     *
     * @return  string
     */
    private function getForm($rpath='')
    {
      global $user, $core, $eqdkp_root_path, $SID, $pdh, $html;

      // root path
      $root_path = ($rpath != '') ? $rpath : $eqdkp_root_path;

      // get location
      $form_location = ($core->config['sb_input_box_location'] != '') ? $core->config['sb_input_box_location'] : 'top';

      // get class for row
      $class = $core->switch_row_class();

      // only display form if user has members assigned to or if user modus is selected
      $members = $pdh->get('member', 'connection_id', array($user->data['user_id']));
      if ((is_array($members) && count($members) > 0) ||
          $core->config['shoutbox']['sb_use_users'])
      {
        // html
        $out = '<form id="reload_shoutbox" name="reload_shoutbox" action="'.$root_path.'plugins/shoutbox/shoutbox.php" method="post">
                </form>
                <form id="Shoutbox" name="Shoutbox" action="'.$root_path.'plugins/shoutbox/shoutbox.php" method="post">
                  <table width="100%" border="0" cellspacing="1" cellpadding="2">';

        // input below? If true insert space row
        if ($form_location == 'bottom' && $user->check_auth('u_shoutbox_add', false))
        {
          $out .= '<tr><th>&nbsp;</th></tr>';
        }

        $out .= '<tr class="'.$class.'">
                   <td>
                     <div class="center">'
                     .$this->getFormName().
                    '</div>
                   </td>
                 </tr>
                 <tr class="'.$class.'">
                   <td><div class="center"><textarea class="input" name="sb_text" style="width: 90%;" rows="3" cols="1"></textarea></div></td>
                 </tr>
                 <tr class="'.$class.'">
                   <td>
                     <div class="center">
                       <input type="hidden" name="sb_root" value="'.urlencode($root_path).'"/>
                       <input type="hidden" name="sb_orientation" value="vertical"/>
                       <span id="shoutbox_button"><input type="submit" class="mainoption bi_ok" name="sb_submit" value="'.$user->lang('sb_submit_text').'"/></span>
                       <span class="small bold hand" onclick="$(\'#reload_shoutbox\').ajaxSubmit(
                         {
                           target: \'#htmlShoutboxTable\',
                           url:\''.$root_path.'plugins/shoutbox/shoutbox.php'.$SID.'&amp;sb_root='.rawurlencode($root_path).'&amp;sb_orientation=vertical\',
                           beforeSubmit: function(formData, jqForm, options) {
                             reloadShoutboxRequest(\''.$root_path.'\');
                           },
                           success: function() {
                             reloadShoutboxFinished(\''.$root_path.'\', \''.$user->lang('sb_reload').'\');
                           }
                         });">
                         <span id="shoutbox_reload_button">
                           <img src="'.$root_path.'plugins/shoutbox/images/reload.png" alt="'.$user->lang('sb_reload').'" title="'.$user->lang('sb_reload').'"/>
                         </span>
                       </span>
                     </div>
                   </td>
                 </tr>
               </table>
             </form>';
      }
      else if ($core->config['shoutbox']['sb_use_users'])
      {
        $out .= '<div class="center">'.$user->lang('sb_no_character_assigned').'</div>';
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
      global $user, $pdh, $html, $core;

      // for anonymous user, just return empty string
      $outHtml = '';

      // if we have users, just return the single user, otherwise use member dropdown
      if ($core->config['shoutbox']['sb_use_users'])
      {
        // show name as text and user id as hidden value
        $username = $pdh->get('user', 'name', array($user->data['user_id']));
        $outHtml .= '<input type="hidden" name="sb_usermember_id" value="'.$user->data['user_id'].'"/>'.$username;
      }
      else
      {
        // get member array
        $members = $pdh->get('member', 'connection_id', array($user->data['user_id']));
        if (is_array($members))
        {
          $membercount = count($members);

          // if more than 1 member, show dropdown box
          if ($membercount > 1)
          {
            // show dropdown box
            $outHtml .= $html->DropDown('sb_usermember_id', $members, '');
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
      global $user, $core, $SID, $eqdkp_root_path;

      $html = '<table width="100%" border="0" cellspacing="1" cellpadding="2">
                 <tr class="'.$core->switch_row_class().'">
                   <td class="menu">
                     <div class="center">
                       <input type="button" class="liteoption bi_archive" value="'.$user->lang('sb_archive').'" onclick="window.location.href=\''.$eqdkp_root_path.'plugins/shoutbox/archive.php'.$SID.'\'"/>
                     </div>
                   </td>
                 </tr>
               </table>';

      return $html;
    }

  }
}

?>

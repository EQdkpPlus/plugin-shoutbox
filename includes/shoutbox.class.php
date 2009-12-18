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


/*+----------------------------------------------------------------------------
  | Shoutbox
  +--------------------------------------------------------------------------*/
if (!class_exists("Shoutbox"))
{
  class Shoutbox
  {
    /**
     * RSS object
     */
    private $rss;

    /**
     * Required versions
     */
    private $reqVersions = array(
        'php'   => '5.0.0',
        'eqdkp' => '0.7.0.1'
    );

    /**
     * Required versions
     */
    private $output_limit;

    /**
     * Constructor
     */
    public function __construct()
    {
      global $eqdkp, $pcache;

      $this->rss = new UniversalFeedCreator();
      $this->rss->title          = 'Shoutbox';
      $this->rss->description    = $eqdkp->config['main_title'].' - Shoutbox';
      $this->rss->link           = $pcache->BuildLink();
      $this->rss->syndicationURL = $pcache->BuildLink().$_SERVER['PHP_SELF'];

      // read in shoutbox config
      $this->readConfig();

      // get output limit
      $this->output_limit = ($eqdkp->config['sb_output_count_limit'] > 0 ? $eqdkp->config['sb_output_count_limit'] : 10);
    }

    /**
     * checkRequirements
     * Check the shoutbox requirements
     *
     * @returns true if success, otherwise error string
     */
    public function checkRequirements()
    {
      global $user;

      // set defult to OK
      $result = true;

      // compare
      if (version_compare(phpversion(), $this->reqVersions['php'], "<"))
      {
        $result = sprintf($user->lang['sb_php_version'], $this->reqVersions['php'], phpversion());
      }
      else if (version_compare(EQDKPPLUS_VERSION, $this->reqVersions['eqdkp'], "<"))
      {
        $result = sprintf($user->lang['sb_plus_version'], $this->reqVersions['eqdkp'],
                          ((EQDKPPLUS_VERSION > 0) ? EQDKPPLUS_VERSION : '[non-PLUS]'));
      }

      return $result;
    }

    /**
     * insertShoutboxEntry
     * Insert a shoutbox entry for current member
     *
     * @param    int    $member_id   member id
     * @param    string $text        text to insert
     * @param    int    $tz          timezone offset
     */
    public function insertShoutboxEntry($member_id, $text, $tz=0)
    {
      global $user, $pdh;

      // is user allowed to add a shoutbox entry?
      if ($user->data['user_id'] != ANONYMOUS && $user->check_auth('u_shoutbox_add', false))
      {
        // insert
        $shoutbox_id = $pdh->put('shoutbox', 'add', array($member_id, $text, $tz));
        if ($shoutbox_id === false)
          return false;

        // process hook queue
        $pdh->process_hook_queue();

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
    public function deleteShoutboxEntry($shoutbox_id)
    {
      global $user, $pdh;

      // is user owner of the shoutbox entry or is admin?
      if (($user->data['user_id'] != ANONYMOUS && $user->data['user_id'] == $pdh->get('shoutbox', 'userid', array($shoutbox_id))) ||
          ($user->check_auth('a_shoutbox_delete', false)))
      {
        $result = $pdh->put('shoutbox', 'delete', array($shoutbox_id));
        if (!$result)
          return false;

        // process hook queue
        $pdh->process_hook_queue();

        return $result;
      }

      return false;
    }

    /**
     * showShoutbox
     * show the complete shoutbox
     *
     * @return  string
     */
    public function showShoutbox()
    {
      global $user, $eqdkp;

      $html = '';

      // javascript code
      $this->shoutboxJCode();

      // is input above (and user can add entries) append form
      if ($eqdkp->config['sb_input_box_below'] != 1 && $user->check_auth('u_shoutbox_add', false))
      {
        $html .= $this->getForm();
      }

      // content table
      $html .= '<div id="htmlShoutboxTable">';
      $html .= $this->getContent();
      $html .= '</div>';

      // archive link? (User must be logged in to see archive link)
      if ($eqdkp->config['sb_show_archive'] && $user->data['user_id'] != ANONYMOUS)
      {
        $html .= $this->getArchiveLink();
      }

      // is input below (and user can add entries) append form
      if ($eqdkp->config['sb_input_box_below'] == 1 && $user->check_auth('u_shoutbox_add', false))
      {
        $html .= $this->getForm();
      }

      return $html;
    }

    /**
     * getContent
     * get the content of the shoutbox
     *
     * @param  string   $rpath   root path
     * @param  boolean  $decode  UTF8 decode?
     *
     * @return  string
     */
    public function getContent($rpath='', $decode=false)
    {
      global $user, $eqdkp, $SID, $eqdkp_root_path, $pcache, $pdh;

      // root path
      $root_path = ($rpath != '') ? $rpath : $eqdkp_root_path;

      $html = '';

      // the delete form
      $html .= '<form id="del_shoutbox" name="del_shoutbox" action="'.$eqdkp_root_path.'plugins/shoutbox/shoutbox.php" method="post">
                </form>';

      // get shoutbox id's
      $shoutbox_ids = $pdh->get('shoutbox', 'id_list');
      if (is_array($shoutbox_ids) && count($shoutbox_ids) > 0 && is_dir($root_path))
      {
        // output table header
        $html .= '<table width="100%" border="0" cellspacing="1" cellpadding="2">';

        // input above? If true, insert a space row
        if ($eqdkp->config['sb_input_box_below'] != 1 && $user->check_auth('u_shoutbox_add', false))
        {
          $html .= '<tr><th>&nbsp;</th></tr>';
        }

        // output at most number of requested items
        $output_count = min($this->output_limit, count($shoutbox_ids));
        for ($i = 0; $i < $output_count; $i++)
        {
          $shoutbox_id = $shoutbox_ids[$i];

          // get class for row
          $class = $eqdkp->switch_row_class();

          $html .= '<tr class="'.$class.'" onmouseout="this.className=\''.$class.'\';" onmouseover="this.className=\'rowHover\';">
                      <td>';

          // if admin or own entry, ouput delete link
          if ($user->data['user_id'] == $pdh->get('shoutbox', 'userid', array($shoutbox_id)) ||
              $user->check_auth('a_shoutbox_delete', false))
          {
            $img = $root_path.'images/global/delete.png';
            $delete_text = ($decode ? utf8_encode($user->lang['delete']) : $user->lang['delete']);

            // Java Script for delete
            $html .= '<span class="small bold floatRight hand" onclick="$(\'#del_shoutbox\').ajaxSubmit(
                        {
                          target: \'#htmlShoutboxTable\',
                          url:\''.$root_path.'plugins/shoutbox/shoutbox.php'.$SID.'&sb_delete='.$shoutbox_id.'&sb_root='.$root_path.'\',
                          beforeSubmit: function(formData, jqForm, options) {
                            deleteShoutboxRequest(\''.$root_path.'\', '.$shoutbox_id.', \''.$delete_text.'\');
                          }
                        }); ">
                        <span id="shoutbox_delete_button_'.$shoutbox_id.'">
                          <img src="'.$img.'" alt="'.$delete_text.'" title="'.$delete_text.'"/>
                        </span>
                      </span>';
          }

          // output date as well as User and text
          $html .= $pdh->geth('shoutbox', 'date', array($shoutbox_id, $eqdkp->config['sb_show_date'])).': '.
                   $pdh->geth('shoutbox', 'membername', array($shoutbox_id, $decode)).
                   '<br/>'.
                   $pdh->geth('shoutbox', 'text', array($shoutbox_id, $rpath));

          $html .= '  </td>
                    </tr>';

          // create RSS feed item
          $rssitem = new FeedItem();
          $rssitem->title       = $pdh->get('shoutbox', 'membername', array($shoutbox_id, $decode));
          $rssitem->link        = $this->rss->link;
          $rssitem->description = $pdh->geth('shoutbox', 'text', array($shoutbox_id));
          $rssitem->date        = $pdh->get('shoutbox', 'date', array($shoutbox_id));
          $rssitem->source      = $this->rss->link;
          $rssitem->author      = $pdh->get('shoutbox', 'membername', array($shoutbox_id, $decode));
          $rssitem->guid        = $shoutbox_id;
          $this->rss->addItem($rssitem);
        }

        // output table footer
        $html .= '</table>';

        // save RSS
        $this->rss->saveFeed('RSS2.0', $pcache->FilePath('shoutbox.xml', 'shoutbox'), false);
        // add link to RSS
        $html .= '<link rel="alternate" type="application/rss+xml" title="EQDkp-Plus Shoutbox"
                   href="'.$pcache->BuildLink().$pcache->FileLink('shoutbox.xml', 'shoutbox').'" />';
      }
      else
      {
        $no_entries = ($decode ? utf8_encode($user->lang['sb_no_entries']) : $user->lang['sb_no_entries']);

        $html .= '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">
                    <tr class="'.$eqdkp->switch_row_class().'">
                      <td><div align="center">'.$no_entries.'</div></td>
                    </tr>
                  </table>';
      }

      return $html;
    }

    /**
     * shoutboxJCode
     * output the Java Code for the Shoutbox
     */
    private function shoutboxJCode()
    {
      global $user, $eqdkp_root_path, $eqdkp, $SID, $tpl;

      // set autoreload (0 = disable)
      $autoreload = ($eqdkp->config['sb_autoreload'] != '') ? intval($eqdkp->config['sb_autoreload']) : 0;
      $autoreload = ($autoreload < 600 ? $autoreload : 0);
      $autoreload = $autoreload * 1000; // to ms

      $jscode  = "// wait for the DOM to be loaded
                  $(document).ready(function() {
                    $('#Shoutbox').ajaxForm({
                      target: '#htmlShoutboxTable',
                      beforeSubmit:  function(formData, jqForm, options) {
                        showShoutboxRequest('".$eqdkp_root_path."', '".$user->lang['sb_save_wait']."');
                      },
                      success: function() {
                        showShoutboxFinished('".$eqdkp_root_path."', '".$user->lang['sb_submit_text']."', '".$user->lang['sb_reload']."');
                      }
                    });
                 ";
      if ($autoreload > 0)
      {
        $jscode .= "setInterval(function() {
                      shoutboxAutoReload('".$eqdkp_root_path."', '".$SID."', '".$user->lang['sb_reload']."');
                    }, ".$autoreload.");";
      }
      $jscode .= '});';

      $tpl->js_file($eqdkp_root_path.'plugins/shoutbox/includes/javascripts/shoutbox.js');
      $tpl->add_js($jscode);
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
      global $user, $eqdkp, $eqdkp_root_path, $SID, $pdh;

      // root path
      $root_path = ($rpath != '') ? $rpath : $eqdkp_root_path;

      // get class for row
      $class = $eqdkp->switch_row_class();

      // only display form if user has members assigned to
      $member_ids = $pdh->get('sb_member_user', 'memberid_list', array($user->data['user_id']));
      if (is_array($member_ids) && count($member_ids) > 0)
      {
        // html
        $html = '<form id="reload_shoutbox" name="reload_shoutbox" action="'.$root_path.'plugins/shoutbox/shoutbox.php" method="post">
                 </form>
                 <form id="Shoutbox" name="Shoutbox" action="'.$root_path.'plugins/shoutbox/shoutbox.php" method="post">
                   <table width="100%" border="0" cellspacing="1" cellpadding="2">';

        // input below? If true insert space row
        if ($eqdkp->config['sb_input_box_below'] == 1 && $user->check_auth('u_shoutbox_add', false))
        {
          $html .= '<tr><th>&nbsp;</th></tr>';
        }

        $html .= '<tr class="'.$class.'">
                    <td>
                      <div align="center">'
                      .$this->getFormMember().
                     '</div>
                    </td>
                  </tr>
                  <tr class="'.$class.'">
                    <td><div align="center"><textarea class="input" name="sb_text" cols="20" rows="3"></textarea></div></td>
                  </tr>
                  <tr class="'.$class.'">
                    <td>
                      <div align="center">
                        <input type="hidden" name="sb_root" value="'.$root_path.'"/>
                        <span id="shoutbox_button"><input type="submit" class="input" name="sb_submit" value="'.$user->lang['sb_submit_text'].'"/></span>
                        <span class="small bold hand" onclick="$(\'#reload_shoutbox\').ajaxSubmit(
                          {
                            target: \'#htmlShoutboxTable\',
                            url:\''.$root_path.'plugins/shoutbox/shoutbox.php'.$SID.'&sb_root='.$root_path.'\',
                            beforeSubmit: function(formData, jqForm, options) {
                              reloadShoutboxRequest(\''.$root_path.'\');
                            },
                            success: function() {
                              reloadShoutboxFinished(\''.$root_path.'\', \''.$user->lang['sb_reload'].'\');
                            }
                          });">
                          <span id="shoutbox_reload_button">
                            <img src="'.$root_path.'plugins/shoutbox/images/reload.png" alt="'.$user->lang['sb_reload'].'" title="'.$user->lang['sb_reload'].'"/>
                          </span>
                        </span>
                      </div>
                    </td>
                  </tr>
                </table>
              </form>';
      }
      else
      {
        $html .= '<div align="center">'.$user->lang['sb_no_character_assigned'].'</div>';
      }

      return $html;
    }

    /**
     * getFormMember
     * get the Shoutbox <form> Members
     *
     * @return  string
     */
    private function getFormMember()
    {
      global $user, $pdh;

      // for anonymous user, just return empty string
      $html = '';


      $member_ids = $pdh->get('sb_member_user', 'memberid_list', array($user->data['user_id']));
      if (is_array($member_ids))
      {
        $membercount = count($member_ids);

        // if more than 1 member, show dropdown box
        if ($membercount > 1)
        {
          // show dropdown box
          $html .= '<select name="sb_member_id" size="1">';
          foreach($member_ids as $member_id)
          {
            $html .= '<option value="'.$member_id.'">'.$pdh->get('member', 'name', array($member_id, false, false)).'</option>';
          }
          $html .= '</select>';
        }
        // if only one member, show just member
        else if ($membercount == 1)
        {
          // show name as text and member id as hidden value
          $html .= '<input type="hidden" name="sb_member_id" value="'.$member_ids[0].'"/>'.
                   $pdh->get('member', 'name', array($member_ids[0], false, false));
        }
      }

      return $html;
    }

    /**
     * getArchiveLink
     * get the archive link text
     *
     * @return  string
     */
    private function getArchiveLink()
    {
      global $user, $eqdkp, $SID, $eqdkp_root_path;

      $html = '<table width="100%" border="0" cellspacing="1" cellpadding="2">
                 <tr class="'.$eqdkp->switch_row_class().'">
                   <td>
                     <div align="center"><a href="'.$eqdkp_root_path.'plugins/shoutbox/archive.php'.$SID.'">'.$user->lang['sb_archive'].'</a></div>
                   </td>
                 </tr>
               </table>';

      return $html;
    }

    /**
     * readConfig
     * Read in the shoutbox configuration
     */
    private function readConfig()
    {
      global $eqdkp, $db;

      $sql = 'SELECT * FROM `__shoutbox_config`';
      $result = $db->query($sql);
      if ($result)
      {
        while(($row = $db->fetch_record($result)))
        {
          $eqdkp->config[$row['config_name']] = $row['config_value'];
        }
        $db->free_result($result);
      }
    }

  }
}

?>

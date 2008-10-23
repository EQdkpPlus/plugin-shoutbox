<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date:$
 * -----------------------------------------------------------------------
 * @author      $Author:$
 * @copyright   2008 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev:$
 *
 * $Id:$
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
    * Constructor
    */
    function Shoutbox()
    {
    }

    /**
    * getNumShoutboxEntries
    * Get the number of shoutbox entries in database
    *
    * @returns integer
    */
    function getNumShoutboxEntries()
    {
      global $db;

      $sql = 'SELECT COUNT(*) FROM `__shoutbox`';
      $count = $db->query_first($sql);

      return $count;
    }

    /**
    * getShoutboxEntries
    * Get all shoutbox entries as array
    *
    * @return array(
    *           'name',
    *           'class_id',
    *           'member_id',
    *           'date',
    *           'text',
    *           'id'
    *         )
    *
    */
    function getShoutboxEntries($start = 0, $limit = false, $decode=false)
    {
      global $conf_plus, $db;

      // init array
      $shoutbox = array();

      // (re)set limit
      if ($limit === false)
      {
        $limit = ($conf_plus['sb_output_count_limit'] > 0 ? $conf_plus['sb_output_count_limit'] : SHOUTBOX_DEFAULT_LIMIT);
      }

      // get last $(limit) entries
      $sql = 'SELECT members.member_name, members.member_class_id, members.member_id, UNIX_TIMESTAMP(shoutbox.date) AS date, shoutbox.text, shoutbox.shoutbox_id
              FROM `__shoutbox` AS shoutbox
              LEFT JOIN `__members` AS members ON members.member_id = shoutbox.member_id
              ORDER BY shoutbox.date DESC
              LIMIT '.$start.' , '.$limit;
      $result = $db->query($sql);
      if (!$result){
        message_die('Could not obtain shoutbox information', '', __FILE__, __LINE__, $sql);
      }

      while ($row = $db->fetch_record($result))
      {
        $shoutbox[] = array(
          'name'      => ($decode == true) ? utf8_encode($row['member_name']) : $row['member_name'],
          'class_id'  => $row['member_class_id'],
          'member_id' => $row['member_id'],
          'date'      => $row['date'],
          'text'      => ($decode == true) ? utf8_encode($row['text']) : $row['text'],
          'id'        => $row['shoutbox_id'],
        );
      }
      $db->sql_freeresult($result);

      return $shoutbox;
    }

    /**
    * getMembersForUser
    * Get all members of current user as array with array() = array('name', 'id', 'class_id')
    *
    * return  array(
    *           'name',
    *           'id',
    *           'class_id'
    *         )
    *
    */
    function getMembersForUser()
    {
      global $user, $db;

      $members = array();

      if ($user->data['user_id'] != ANONYMOUS)
      {
        // get number of members for user
        $sql = 'SELECT members.member_name, members.member_id, members.member_class_id
                FROM `__member_user` AS member_user
                LEFT JOIN `__members` AS members ON members.member_id = member_user.member_id
                WHERE member_user.user_id='.$user->data['user_id'];
        $result = $db->query($sql);
        if (!$result){
          message_die('Could not obtain shoutbox member information', '', __FILE__, __LINE__, $sql);
        }

        while ($row = $db->fetch_record($result))
        {
          $members[] = array(
            'name'     => $row['member_name'],
            'id'       => $row['member_id'],
            'class_id' => $row['member_class_id'],
          );
        }
        $db->sql_freeresult($result);
      }

      return $members;
    }

    /**
    * getUserIdFromMemberId
    * Get the user id from a member id
    *
    * @param    int    $member_id   member id
    *
    * @return integer
    *
    */
    function getUserIdFromMemberId($member_id)
    {
      global $db;

      $user_id = ANONYMOUS;

      $sql = 'SELECT user_id FROM `__member_user` WHERE member_id='.$member_id;
      $user_id = $db->sql_query_first($sql);

      return $user_id;
    }

    /**
    * getUserIdFromShoutboxId
    * Get the user id from a shoutbox id
    *
    * @param    int    $shoutbox_id   shoutbox id
    *
    * @return integer
    *
    */
    function getUserIdFromShoutboxId($shoutbox_id)
    {
      global $db;

      $user_id = ANONYMOUS;

      $sql = 'SELECT m.user_id
              FROM `__member_user` m
              LEFT JOIN `__shoutbox` s
              ON m.member_id = s.member_id
              WHERE s.shoutbox_id='.$shoutbox_id;
      $user_id = $db->sql_query_first($sql);

      return $user_id;
    }

    /**
    * checkUTF8
    * Check if UTF-8
    *
    * @param    string $string  text
    *
    * @return boolean
    */
    function checkUTF8($string)
    {
      if (is_array($string))
      {
        $enc = implode('', $string);
        return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
      }
      else
      {
        return (utf8_encode(utf8_decode($string)) == $string);
      }
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
    function shoutbox_wordwrap($text, $width, $break="\n", $cut=false)
    {
      // explode by spaces
      $element_array = explode(' ', $text);
      $count = count($element_array);

      // loop through all the elements
      $wraped_text = '';
      foreach($element_array as $org_text)
      {
        // explode by \n
        $inner_element_array = explode("\n", $org_text);
        foreach($inner_element_array as $inner_org_text)
        {
          // strip bbcode from text
          $striped_text = preg_replace('#\[[\w=]+\](.*?)\[/[\w]+\]#si', '\1', $inner_org_text);
          // get sriped size
          $striped_size = strlen($striped_text);

          // do not wrap image/urls/emails
          $inner_cut = $cut;
          if (preg_match('#\[img\](.*?)\[/img\]#si', $inner_org_text) ||
              preg_match('#\[url=?"?(.*?)"?\](.*?)\[/url\]#si', $inner_org_text) ||
              preg_match('#\[email\](.*?)\[/email\]#si', $inner_org_text))
          {
            $inner_cut = false;
          }

          // fits?
          if ($striped_size > $width)
          {
            $new_text = wordwrap($striped_text, $width, $break, $inner_cut);
            // replace in original text
            $new_text = str_replace($striped_text, $new_text, $inner_org_text);
          }
          else
          {
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

    /**
    * escape
    * Escape string
    *
    * @param   string  $s  string to escape
    *
    * @return  string
    */
    function escape($s)
    {
      global $text;
      $text = strip_tags($text);
      return '<pre><code>'.htmlspecialchars($s[1]).'</code></pre>';
    }

    /**
    * removeBr
    * Clean some tags to remain strict
    * not very elegant, but it works. No time to do better ;)
    *
    * @param   string  $s  string to remove br
    *
    * @return  string
    */
    function removeBr($s)
    {
      return preg_replace('/\<br[\s\/]*\>/ms', '', $s[0]);
    }

    /**
    * toHTML
    * Convert input to HTML by cleaning up and decode BBCode
    *
    * @param   string  $text  Text to convert
    *
    * @return  string
    */
    function toHTML($text)
    {
      $text = trim($text);
      $text = '<p>'.$text.'</p>';
      $text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', array($this,"escape"), $text);

      // Smileys to find...
      $in = array(
              ':)', ':-)',
              ':D', ':-D',
              ':o', ':-o',
              ':p', ':-p',
              ':P', ':-P',
              ':(', ':-(',
              ';)', ';-)'
      );
      $out = array(
              '<img alt=":)" src="{SMILEY_PATH}/happy.png" />',     '<img alt=":-)" src="{SMILEY_PATH}/happy.png" />',
              '<img alt=":D" src="{SMILEY_PATH}/smile.png" />',     '<img alt=":-D" src="{SMILEY_PATH}/smile.png" />',
              '<img alt=":o" src="{SMILEY_PATH}/surprised.png" />', '<img alt=":-o" src="{SMILEY_PATH}/surprised.png" />',
              '<img alt=":p" src="{SMILEY_PATH}/tongue.png" />',    '<img alt=":-p" src="{SMILEY_PATH}/tongue.png" />',
              '<img alt=":P" src="{SMILEY_PATH}/tongue.png" />',    '<img alt=":-P" src="{SMILEY_PATH}/tongue.png" />',
              '<img alt=":(" src="{SMILEY_PATH}/unhappy.png" />',   '<img alt=":-(" src="{SMILEY_PATH}/unhappy.png" />',
              '<img alt=";)" src="{SMILEY_PATH}/wink.png" />',      '<img alt=";-)" src="{SMILEY_PATH}/wink.png" />'
      );
      $text = str_replace($in, $out, $text);

      // BBCode to find...
      $in = array(
               '/\[b\](.*?)\[\/b\]/ms',
               '/\[i\](.*?)\[\/i\]/ms',
               '/\[u\](.*?)\[\/u\]/ms',
               '/\[img\](.*?)\[\/img\]/ms',
               '/\[email\](.*?)\[\/email\]/ms',
               '/\[url\](.*?)\[\/url\]/ms',
               '/\[url\="?(.+)"?\](.*?)\[\/url\]/ms',
               '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
               '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
               '/\[quote](.*?)\[\/quote\]/ms',
               '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
               '/\[list\](.*?)\[\/list\]/ms',
               '/\[\*\]\s?(.*?)\n/ms'
      );

      // And replace them by...
      $out = array(
               '<strong>\1</strong>',                     // [b]
               '<em>\1</em>',                             // [i]
               '<u>\1</u>',                               // [u]
               '',                                        // [img]
               '<a href="mailto:\1">\1</a>',              // [email]
               '<a href="\1">\1</a>',                     // [url]
               '<a href="\1">\2</a>',                     // [url=]
               '<span style="font-size:\1%">\2</span>',   // [size]
               '<span style="color:\1">\2</span>',        // [color]
               '\1',                                      // [quote]
               '',                                        // [list=]
               '',                                        // [list]
               '\1'                                       // [*]
      );
      $text = preg_replace($in, $out, $text);

      // paragraphs
      $text = str_replace("\r", "", $text);
      $text = nl2br($text);

      $text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', array($this,"removeBr"), $text);
      $text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);

      $text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', array($this,"removeBr"), $text);
      $text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);

      return $text;
    }

    /**
    * insertShoutboxEntry
    * Insert a shoutbox entry for current member
    *
    * @param    int    $member_id   member id
    * @param    string $text        text to insert
    * @param    string $rpath       root path
    */
    function insertShoutboxEntry($member_id, $text, $rpath='')
    {
      global $user, $db, $eqdkp_root_path;

      // get root path
      $root_path = ($rpath != '') ? $rpath : $eqdkp_root_path;

      // is user allowed to add a shoutbox entry?
      if ($user->data['user_id'] != ANONYMOUS && $user->check_auth('u_shoutbox_add', false))
      {
        // clean input
        $text_insert = $this->shoutbox_wordwrap($text, SHOUTBOX_WORDWRAP, "\n", true);
        $text_insert = $this->toHTML($text_insert);
        $text_insert = ($this->checkUTF8($text_insert) == 1) ? utf8_decode($text_insert) : $text_insert;

        // insert
        $sql = 'INSERT INTO `__shoutbox` (`member_id`, `text`) VALUES ('.$member_id.', \''.$text_insert.'\')';
        $result = $db->query($sql);
        return ($result ? true : false);
      }

      return false;
    }

    /**
    * deleteShoutboxEntry
    * delete a shoutbox entry
    *
    * @param    int    $shoutbox_id   shoutbox entry id
    */
    function deleteShoutboxEntry($shoutbox_id)
    {
      global $user, $db;

      // is user owner of the shoutbox entry or is admin?
      if (($user->data['user_id'] != ANONYMOUS && $user->data['user_id'] == $this->getUserIdFromShoutboxId($shoutbox_id)) ||
          ($user->check_auth('a_shoutbox_delete', false)))
      {
        $sql = 'DELETE FROM `__shoutbox` WHERE shoutbox_id='.$shoutbox_id;
        $result = $db->query($sql);
        return ($result ? true : false);
      }


      return false;
    }

    /**
    * showShoutbox
    * show the complete shoutbox
    *
    * @return  string
    */
    function showShoutbox()
    {
      global $user, $conf_plus;

      $html  = $this->getShoutboxJCode();
      if ($user->data['user_id'] != ANONYMOUS && $user->check_auth('u_shoutbox_add', false))
      {
        $html .= $this->getForm();
      }
      $html .= '<div id="htmlShoutboxTable">';
      $html .= $this->getContent();
      $html .= '</div>';
      if ($conf_plus['sb_show_archive'])
      {
        $html .= $this->getArchiveLink();
      }

      return $html;
    }

    /**
    * getShoutboxJCode
    * get the Java Code for the Shoutbox
    *
    * @return  string
    */
    function getShoutboxJCode()
    {
      global $user, $eqdkp_root_path;

      $jscode = "<script type='text/javascript'>
                   // wait for the DOM to be loaded
                   $(document).ready(function() {

                     $('#Shoutbox').ajaxForm({
                       target: '#htmlShoutboxTable',
                       beforeSubmit:  showShoutboxRequest,
                       success: function() {
                         // clear the input field:
                         document.Shoutbox.sb_text.value = '';
                         document.Shoutbox.sb_text.disabled=false;
                         document.getElementById('shoutbox_button').innerHTML='<input type=\"submit\" class=\"input\" name=\"sb_submit\" value=\"".$user->lang['sb_submit_text']."\"/>';
                       }
                     });
                   });

                   function showShoutboxRequest(formData, jqForm, options) {
                     document.Shoutbox.sb_text.disabled=true;
                     document.getElementById('shoutbox_button').innerHTML='<img src=\"".$eqdkp_root_path."images/global/loading.gif\" alt=\"Save\"/>".$user->lang['sb_save_wait']."';
                   }
                 </script>";

      return $jscode;
    }

    /**
    * getForm
    * get the Shoutbox <form>
    *
    * @param   string  $rpath  root path
    *
    * @return  string
    */
    function getForm($rpath='')
    {
      global $user, $eqdkp, $eqdkp_root_path;

      // root path
      $root_path = ($rpath != '') ? $rpath : $eqdkp_root_path;

      // get class
      $class = $eqdkp->switch_row_class();

      // html
      $html = '<form id="Shoutbox" name="Shoutbox" action="'.$root_path.'plugins/shoutbox/shoutbox.php" method="post">
                 <table width="100%" border="0" cellspacing="1" cellpadding="2">
                   <tr class="'.$class.'">
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
                       </div>
                     </td>
                   </tr>
                 </table>
               </form>';

      // the delete form
      $html .= '<form id="del_shoutbox" name="del_shoutbox" action="'.$root_path.'plugins/shoutbox/shoutbox.php" method="post">
                </form>';

      return $html;
    }

    /**
    * getFormMember
    * get the Shoutbox <form> Members
    *
    * @return  string
    */
    function getFormMember()
    {
      // get members
      $members = $this->getMembersForUser();
      $membercount = count($members);

      $html = '';

      if ($membercount > 1)
      {
        // show dropdown box
        $html .= '<select name="sb_member_id" size="1">';
        foreach($members as $member)
        {
          $html .= '<option value="'.$member['id'].'">'.$member['name'].'</option>';
        }
        $html .= '</select>';
      }
      else
      {
        // show name as text and member id as hidden value
        $html .= '<input type="hidden" name="sb_member_id" value="'.$members[0]['id'].'"/>'.
                 $members[0]['name'];
      }

      return $html;
    }

    /**
    * getArchiveLink
    * get the archive link text
    *
    * @return  string
    */
    function getArchiveLink()
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
    * getContent
    * get the content of the shoutbox
    *
    * @param   string    $rpath   root path
    * @param   boolean   $decode  UTF8 decode?
    *
    * @return  string
    */
    function getContent($rpath='', $decode=false)
    {
      global $user, $eqdkp, $SID, $eqdkp_root_path;

      // root path
      $root_path = ($rpath != '') ? $rpath : $eqdkp_root_path;

      $html = '';

      // get shoutbox entries
      $shoutbox_entries = $this->getShoutboxEntries(0, false, $decode);
      $count = count($shoutbox_entries);
      if ($count > 0)
      {
        // output table header
        $html .= '<table width="100%" border="0" cellspacing="1" cellpadding="2">
                    <tr><th>&nbsp;</th></tr>';
        foreach ($shoutbox_entries as $entry)
        {
          // get class for row
          $class = $eqdkp->switch_row_class();


          $html .= '<tr class="'.$class.'" onmouseout="this.className=\''.$class.'\';" onmouseover="this.className=\'rowHover\';">
                      <td>';

          // if admin or own entry, ouput delete link
          if ($user->data['user_id'] == $this->getUserIdFromMemberId($entry['member_id']) ||
              $user->check_auth('a_shoutbox_delete', false))
          {
            $img = $root_path.'images/global/delete.png';

            $html .= '<span class="small bold floatRight hand" onclick="$(\'#del_shoutbox\').ajaxSubmit(
                        {
                          target: \'#htmlShoutboxTable\',
                          url:\''.$root_path.'plugins/shoutbox/shoutbox.php'.$SID.'&shoutbox_delete='.$entry['id'].'&sb_root='.$root_path.'\',
                        }); ">
                        <img src="'.$img.'" alt="'.$user->lang['delete'].'"/>
                      </span>';
          }

          // output Date, User and text
          $html .= date('H:i', $entry['date']).': '.$this->getColoredClassName($entry['name']).
                   '<br/>'.
                   $this->getCleanOutput($entry['text'], $root_path);

          $html .= '  </td>
                    </tr>';
        }
        // output table footer
        $html .= '</table>';
      }
      else
      {
        $html .= '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">
                    <tr class="'.$eqdkp->switch_row_class().'"><td><div align="center">'.$user->lang['sb_no_entries'].'</div></td></tr>
                  </table>';
      }

      return $html;
    }

    /**
    * getColoredClassName
    * get the class name colored
    *
    * @param   string    $name   Member name
    *
    * @return  string
    */
    function getColoredClassName($name)
    {
      global $eqdkp;

      // decode if UTF-8
      $name_for_class = ($this->checkUTF8($name) == 1) ? utf8_decode($name) : $name;

      // get class by name
      $class = get_classNamebyMemberName($name_for_class);

      if($eqdkp->config['default_game'] == 'WoW')
      {
        return '<span class="'.get_classColorChecked($class).'">'.$name.'</span>';
      }
      else
      {
        return '<span>'.$name.'</span>';
      }
    }

    /**
    * getCleanOutput
    * get a clean output
    *
    * @param   string    $text   Text to replace with
    * @param   string    $rpath  root path
    *
    * @return  string
    */
    function getCleanOutput($text, $rpath='')
    {
       global $eqdkp_root_path;

      // root path
      $root_path = ($rpath != '') ? $rpath : $eqdkp_root_path;

      // search array
      $search = array(
                 '{SMILEY_PATH}',
      );
      // replace array
      $replace = array(
                 $root_path.'pluskernel/include/jquery/img/editor/icons',
      );

      return str_replace($search, $replace, $text);
    }

  }
}

?>

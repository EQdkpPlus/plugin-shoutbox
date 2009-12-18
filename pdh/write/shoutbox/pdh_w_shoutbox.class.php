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
  die('Do not access this file directly.');
}

/*+----------------------------------------------------------------------------
  | pdh_w_shoutbox
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_w_shoutbox'))
{
  class pdh_w_shoutbox extends pdh_w_generic
  {
    /*
     * Number of chars to wrap after
     */
    private $wordwrap;

    /**
     * Constructor
     */
    public function __construct()
    {
      parent::pdh_w_generic();

      // set default wordwrap
      $this->wordwrap = 20;
    }

    /**
     * add
     * Add a new shoutbox entry
     *
     * @param  int     $member_id  Member ID
     * @param  string  $text       Text to insert
     * @param  int     $timezone   Timezone
     *
     * @returns mixed, on success shoutbox id, else false
     */
    public function add($member_id, $text, $timezone=0)
    {
      global $db, $pdh;

      // get timezone as integer
      $timezone = ($timezone != '' && is_numeric($timezone)) ? intval($timezone) : 0;

      // cleanup text
      $text = $this->cleanupText($text);

      // get current timestamp
      $cur_time = time() + ($timezone * 3600);
      $cur_timestamp = mktime(gmdate('H', $cur_time), gmdate('i', $cur_time), gmdate('s', $cur_time),
                              gmdate('n', $cur_time), gmdate('j', $cur_time), gmdate('Y', $cur_time));

      // add to database
      $sql_data = array(
        'member_id'     => $member_id,
        'shoutbox_date' => $cur_timestamp,
        'shoutbox_text' => $db->sql_escape($text)
      );
      $result = $db->query('INSERT INTO `__shoutbox` :params', $sql_data);
      if (!$result)
        return false;

      // do hooks
      $pdh->enqueue_hook('shoutbox_update');

      return $db->insert_id();
    }

    /**
     * delete
     * Delete a shoutbox entry from db
     *
     * @param  int     $shoutbox_id  Shoutbox ID
     *
     * @returns boolean
     */
    public function delete($shoutbox_id)
    {
      global $db, $pdh;

      // delete from db
      $sql = 'DELETE FROM `__shoutbox` WHERE shoutbox_id='.$shoutbox_id;
      $result = $db->query($sql);
      if (!$result)
        return false;

      // do hooks
      $pdh->enqueue_hook('shoutbox_update');

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
    private function cleanupText($text)
    {
      global $bbcode;

      // No html or javascript in shoutbox entries
      $cleanup_text = strip_tags($text);

      // wrap words (do own handling cause of bbcodes)
      $cleanup_text = $this->shoutbox_wordwrap($cleanup_text, $this->wordwrap, "\n", true);

      // convert UTF8 htmlentities
      $cleanup_text = $this->utf8_htmlentities($cleanup_text);

      // wrap around with <p>
      $cleanup_text = '<p>'.$cleanup_text.'</p>';

      // bbcodes, set smiley path to special identifier cause shoutbox has to replace when showing
      $bbcode->SetSmiliePath('{SMILEY_PATH}');
      $cleanup_text = $bbcode->toHTML($cleanup_text);
      $cleanup_text = $bbcode->MyEmoticons($cleanup_text);

      return $cleanup_text;
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
    private function shoutbox_wordwrap($text, $width, $break="\n", $cut=false)
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
          // get striped size
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
     * utf8_htmlentities
     * get html entities of UTF-8 string
     *
     * @param  string  $content  text to replace entities
     *
     * @return  string
     */
    private function utf8_htmlentities($content)
    {
      // convert to array, and convert each array element to entity if neccessary
      $contents = $this->unicode_string_to_array($content);
      $count = count($contents);

      $swap = '';
      for ($i = 0; $i < $count; $i++)
      {
        $contents[$i] = $this->unicode_entity_replace($contents[$i]);
        $swap .= $contents[$i];
      }

      return mb_convert_encoding($swap, 'UTF-8');
    }

    /**
     * unicode_string_to_array
     * convert unicode string to array of unicode chars
     *
     * @param  string  $string  unicode string to make array of
     *
     * @return  array
     */
    private function unicode_string_to_array($string)
    {
      $strlen = mb_strlen($string);
      while ($strlen)
      {
        $array[] = mb_substr($string, 0, 1, 'UTF-8');
        $string = mb_substr($string, 1, $strlen, 'UTF-8');
        $strlen = mb_strlen($string);
      }

      return $array;
    }

    /**
     * unicode_entity_replace
     * replace unicode char by html entity
     *
     * @param  char  $c  unicode character
     *
     * @return  array
     */
    private function unicode_entity_replace($c)
    {
      // get ornial of character, if less than 127, just return, else check for UTF8 and decode
      $h = ord($c{0});
      if ($h <= 0x7F)
      {
        return $c;
      }
      else if ($h < 0xC2)
      {
        return $c;
      }

      if ($h <= 0xDF)
      {
        $h = ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
        $h = '&#'.$h.';';
        return $h;
      }
      else if ($h <= 0xEF)
      {
        $h = ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6 | (ord($c{2}) & 0x3F);
        $h = '&#'.$h.';';
        return $h;
      }
      else if ($h <= 0xF4)
      {
        $h = ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12 | (ord($c{2}) & 0x3F) << 6 | (ord($c{3}) & 0x3F);
        $h = '&#'.$h.';';
        return $h;
      }
    }

  } //end class
} //end if class not exists

?>

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
  | pdh_r_shoutbox
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_shoutbox'))
{
  class pdh_r_shoutbox extends pdh_r_generic
  {
    /**
     * Data array loaded by initialize
     */
    private $data;

    /**
     * Path for smileys
     */
    private $smiley_path;

    /**
     * Hook array
     */
    public $hooks = array(
      'member_update',
      'shoutbox_update'
    );

    /**
     * Presets array
     */
    public $presets = array(
      'sbdate' => array('date',       array('%shoutbox_id%', true),  array()), // true = Show Date
      'sbname' => array('membername', array('%shoutbox_id%'),        array()),
      'sbtext' => array('text',       array('%shoutbox_id%'),        array())
    );

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->smiley_path = 'libraries/jquery/images/editor/icons';
    }

    /**
     * reset
     * Reset shoutbox read module by clearing cached data
     */
    public function reset()
    {
      global $pdc;

      $pdc->del('pdh_shoutbox_table');
      unset($this->data);
    }

    /**
     * init
     * Initialize the shoutbox read module by loading all information from db
     *
     * @returns boolean
     */
    public function init()
    {
      global $pdc, $db, $core;

      // try to get from cache first
      $this->data = $pdc->get('pdh_shoutbox_table');
      if($this->data !== NULL)
      {
        return true;
      }

      // empty array as default
      $this->data = array();

      // read all shoutbox entries from db
      $sql = 'SELECT
                shoutbox_id,
                member_id,
                shoutbox_date,
                shoutbox_text
              FROM `__shoutbox`
              ORDER BY shoutbox_date DESC;';
      $result = $db->query($sql);
      if ($result)
      {
        // get DST correction value
        $correction = date('I') * 3600;

        // add row by row to local copy
        while (($row = $db->fetch_record($result)))
        {
          $this->data[$row['shoutbox_id']] = array(
            'member_id' => $row['member_id'],
            'date'      => $row['shoutbox_date'],
            'text'      => $row['shoutbox_text']
          );
        }
        $db->free_result($result);
      }

      // add data to cache
      $pdc->put('pdh_shoutbox_table', $this->data, null);

      return true;
    }

    /**
     * get_id_list
     * Return the list of shoutbox ids
     *
     * @returns array(int)
     */
    public function get_id_list()
    {
      // empty id list as default
      $shoutbox_ids = array();

      // add each key of data as shoutbox id to id list
      if (is_array($this->data))
      {
        $keys = array_keys($this->data);
        foreach ($keys as $shoutbox_id)
        {
          $shoutbox_ids[] = $shoutbox_id;
        }
      }

      return $shoutbox_ids;
    }

    /**
     * get_memberid
     * Return the member id corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  shoutbox id
     *
     * @returns integer
     */
    public function get_memberid($shoutbox_id)
    {
      if (is_array($this->data[$shoutbox_id]) && isset($this->data[$shoutbox_id]['member_id']))
      {
        return $this->data[$shoutbox_id]['member_id'];
      }

      return -1;
    }

    /**
     * get_membername
     * Return the member name corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  Shoutbox ID
     *
     * @returns string
     */
    public function get_membername($shoutbox_id)
    {
      global $pdh;

      return $pdh->get('member', 'name', array($this->get_memberid($shoutbox_id), false, false));
    }

    /**
     * get_html_membername
     * Return the member name corresponding to the shoutbox id as html
     *
     * @param  int  $shoutbox_id  Shoutbox ID
     *
     * @returns string
     */
    public function get_html_membername($shoutbox_id)
    {
      global $pdh;

      return $pdh->geth('member', 'name', array($this->get_memberid($shoutbox_id), false, false));
    }

    /**
     * get_text
     * Return the text corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  shoutbox id
     *
     * @returns string
     */
    public function get_text($shoutbox_id)
    {
      if (is_array($this->data[$shoutbox_id]) && isset($this->data[$shoutbox_id]['text']))
      {
        return stripslashes($this->data[$shoutbox_id]['text']);
      }

      return '';
    }

    /**
     * get_html_text
     * Return the text corresponding to the shoutbox id as html
     *
     * @param  int      $shoutbox_id  shoutbox id
     * @param  string   $rpath        root path
     *
     * @returns string
     */
    public function get_html_text($shoutbox_id, $rpath='')
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
        $root_path.$this->smiley_path,
      );

      // cleanup
      $text = str_replace($search, $replace, $this->get_text($shoutbox_id));

      return $text;
    }

    /**
     * get_date
     * Return the timestamp corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  shoutbox id
     *
     * @returns integer
     */
    public function get_date($shoutbox_id)
    {
      if (is_array($this->data[$shoutbox_id]) && isset($this->data[$shoutbox_id]['date']))
      {
        return $this->data[$shoutbox_id]['date'];
      }

      return 0;
    }

    /**
     * get_date
     * Return the timestamp corresponding to the shoutbox id as html
     *
     * @param  int      $shoutbox_id   Shoutbox ID
     * @param  boolean  $show_date     Show date also or just time
     *
     * @returns string
     */
    public function get_html_date($shoutbox_id, $show_date=false)
    {
      global $user, $time;

      if ($show_date)
        $date = $time->date($user->lang['sb_date_format'], $this->get_date($shoutbox_id));
      else
        $date = $time->date($user->lang['sb_time_format'], $this->get_date($shoutbox_id));

      return $date;
    }

    /**
     * get_count
     * Return the number of shoutbox entries
     *
     * @returns integer
     */
    public function get_count()
    {
      if ($this->data && is_array($this->data))
      {
        return count($this->data);
      }

      return 0;
    }

    /**
     * get_userid
     * Return the user id corresponding to the shoutbox id
     *
     * @param  int  $shoutbox_id  Shoutbox ID
     *
     * @returns integer
     */
    public function get_userid($shoutbox_id)
    {
      global $pdh;

      return $pdh->get('sb_member_user', 'userid', array($this->get_memberid($shoutbox_id)));
    }

  } //end class
} //end if class not exists

?>

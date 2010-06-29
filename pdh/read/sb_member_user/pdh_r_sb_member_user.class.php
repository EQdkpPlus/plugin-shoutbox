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
  | pdh_r_sb_member_user
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_r_sb_member_user'))
{
  class pdh_r_sb_member_user extends pdh_r_generic
  {
    /**
     * Members array loaded by initialize
     */
    private $members;

    /**
     * Users array loaded by initialize
     */
    private $users;

    /**
     * Hook array
     */
    public $hooks = array(
      'member_update',
      'user_update'
    );


    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * reset
     * Reset member <-> user read module by clearing cached data
     */
    public function reset()
    {
      global $pdc;

      $pdc->del('pdh_sb_member_user_table.members');
      $pdc->del('pdh_sb_member_user_table.users');
      unset($this->members);
      unset($this->users);
    }

    /**
     * init
     * Initialize the member <-> user read module by loading all information from db
     *
     * @returns boolean
     */
    public function init()
    {
      global $pdc, $db;

      // try to get from cache first
      $this->members = $pdc->get('pdh_sb_member_user_table.members');
      $this->users   = $pdc->get('pdh_sb_member_user_table.users');
      if($this->members !== NULL && $this->users !== NULL)
      {
        return true;
      }

      // empty array as default
      $this->members = array();
      $this->users   = array();

      // read all member_user 's
      $sql = 'SELECT
                member_id,
                user_id
              FROM `__member_user`
              ORDER BY user_id, member_id;';
      $result = $db->query($sql);
      if ($result)
      {
        // add row by row to local copy
        while (($row = $db->fetch_record($result)))
        {
          // each member has one user
          $this->members[$row['member_id']] = $row['user_id'];
          // each user could have multiple members
          $this->users[$row['user_id']][] = $row['member_id'];
        }
        $db->free_result($result);
      }

      // add data to cache
      $pdc->put('pdh_sb_member_user_table.members', $this->members, null);
      $pdc->put('pdh_sb_member_user_table.users',   $this->users,   null);

      return true;
    }

    /**
     * get_memberid_list
     * Return the member ids belonging to this user
     *
     * @param  int  $user_id  User id
     *
     * @returns array
     */
    public function get_memberid_list($user_id)
    {
      $members = array();

      if (is_array($this->users[$user_id]))
      {
        $members = $this->users[$user_id];
      }

      return $members;
    }

    /**
     * get_userid
     * Return the user id corresponding to the member id
     *
     * @param  int  $member_id  Member id
     *
     * @returns integer
     */
    public function get_userid($member_id)
    {
      if (is_array($this->members) && isset($this->members[$member_id]))
      {
        return $this->members[$member_id];
      }

      return ANONYMOUS;
    }

  } //end class
} //end if class not exists

?>

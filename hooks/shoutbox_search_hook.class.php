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
 * $Id: shoutbox_list.php 11795 2012-05-30 21:41:49Z wallenium $
 */

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | shoutbox_search_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('shoutbox_search_hook'))
{
  class shoutbox_search_hook extends gen_class
  {
    /* List of dependencies */
    public static $shortcuts = array('user');

	/**
    * hook_search
    * Do the hook 'search'
    *
    * @return array
    */
	public function search()
	{
		// build search array
		$search = array(
		  'shoutbox' => array(
			'category'    => $this->user->lang('shoutbox'),
			'module'      => 'shoutbox',
			'method'      => 'search',
			'permissions' => array('u_'),
		  ),
		);

		return $search;
	}
  }
}
  
?>
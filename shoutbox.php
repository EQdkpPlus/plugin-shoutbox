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

define('EQDKP_INC', true);
$eqdkp_root_path = './../../';
include_once('includes/common.php');


// Be sure plugin is installed
if ($pm->check(PLUGIN_INSTALLED, 'shoutbox'))
{
  // get post/get values
  $sb_text      = $in->get('sb_text');
  $sb_member_id = $in->get('sb_member_id', ANONYMOUS);
  $sb_delete    = $in->get('shoutbox_delete', 0);
  $sb_root      = $in->get('sb_root');

  // -- Insert? ---------------------------------------------
  if ($sb_text && $sb_member_id != ANONYMOUS)
  {
    $shoutbox->insertShoutboxEntry($sb_member_id, $sb_text);
  }
  // -- Delete? ---------------------------------------------
  else if ($sb_delete)
  {
    $shoutbox->deleteShoutboxEntry($sb_delete);
  }

  // -- Output ----------------------------------------------
  echo $shoutbox->getContent($sb_root, true);
}
else
{
  $error = '<table width="100%" border="0" cellspacing="1" cellpadding="2" class="forumline">
              <tr class="'.$eqdkp->switch_row_class().'">
                <td><div align="center">'.$user->lang['sb_plugin_not_installed'].'</div></td>
              </tr>
            </table>';
  echo $error;
}

?>

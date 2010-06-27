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
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check(PLUGIN_INSTALLED, 'shoutbox') )
{
  message_die('Shoutbox plugin not installed.');
}


// ----------------------------------------------------------------------------
$tpl->assign_vars(array(
    'L_VERSION'      => $pm->get_data('shoutbox', 'version'),
    'L_BUILD'        => $pm->plugins['shoutbox']->build,
    'L_STATUS'       => $pm->plugins['shoutbox']->vstatus,
    'L_COPYRIGHT'    => $user->lang['sb_copyright'],
    'L_YEARR'        => date('Y'),
));


// ----------------------------------------------------------------------------
$core->set_vars(array(
  'page_title'    => 'About Shoutbox',
  'template_file' => 'about.html',
  'template_path' => $pm->get_data('shoutbox', 'template_path'),
  'header_format' => 'none',
  'display'       => true)
);

?>

<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2008-11-09 18:05:54 +0100 (So, 09 Nov 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: osr-corgan $
 * @copyright   2008 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 3069 $
 *
 * $Id: archive.php 3069 2008-11-09 17:05:54Z osr-corgan $
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../';
include_once('includes/common.php');


// -- Plugin installed? -------------------------------------------------------
if (!$pm->check(PLUGIN_INSTALLED, 'shoutbox'))
{
  message_die($user->lang['sb_plugin_not_installed']);
}


// -- Get content -------------------------------------------------------------
$content = $shoutbox->showShoutbox();


// -- Template ----------------------------------------------------------------
$tpl->assign_vars(array (
  // Form
  'ROW_CLASS'       => $core->switch_row_class(),
  'CONTENT'         => $content
));


// -- EQDKP -------------------------------------------------------------------
$core->set_vars(array (
  'page_title'    => $user->lang['shoutbox'],
  'template_path' => $pm->get_data('shoutbox', 'template_path'),
  'template_file' => 'show.html',
  'header_format' => 'simple',
  'display'       => true
  )
);

?>

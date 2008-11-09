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
    header('HTTP/1.0 404 Not Found'); exit;
}

// -- Portal Module -----------------------------------------------------------
$portal_module['shoutbox'] = array(                        // the same name as the folder!
      'name'          => 'Shoutbox Module',                // The name to show
      'path'          => 'shoutbox',                       // Folder name again
      'version'       => '0.0.5',                          // Version
      'author'        => 'Aderyn',                         // Author
      'contact'       => 'Aderyn@gmx.net',                 // email/internet adress
      'description'   => 'Display a shoutbox',             // Detailed Description
      'positions'     => array('left1', 'left2', 'right'), // Which blocks should be usable? left1 (over menu), left2 (under menu), right, middle
      'signedin'      => '0',                              // 0 = all users, 1 = signed in only
      'install'       => array(
                           'autoenable'        => '0',
                           'defaultposition'   => 'left2',
                           'defaultnumber'     => '1',
                         ),
);


// -- Settings ----------------------------------------------------------------
/* Define the Settings if needed
   name:       The name of the Database field & Input name
   language:   The name of the language string in the language file
   property:   What type of field? (text,checkbox,dropdown)
   size:       Size of the field if required (optional)
   options:    If dropdown: array('value'=>'Name')
*/
$portal_settings['shoutbox'] = array(
  'pk_shoutbox_output_count_limit'  => array(
        'name'      => 'sb_output_count_limit',
        'language'  => 'sb_output_count_limit',
        'property'  => 'text',
        'size'      => '2',
      ),
  'pk_shoutbox_show_archive'        => array(
        'name'      => 'sb_show_archive',
        'language'  => 'sb_show_archive',
        'property'  => 'checkbox',
      ),
   'pk_shoutbox_input_box_location' => array(
        'name'      => 'sb_input_box_below',
        'language'  => 'sb_input_box_below',
        'property'  => 'checkbox',
      ),
    'pk_shoutbox_invisible_to_guests' => array(
        'name'      => 'sb_invisible_to_guests',
        'language'  => 'sb_invisible_to_guests',
        'property'  => 'checkbox',
      ),
);


// -- shoutbox_module ---------------------------------------------------------
if (!function_exists(shoutbox_module))
{
  function shoutbox_module()
  {
    global $pm, $eqdkp_root_path;

    if ($pm->check(PLUGIN_INSTALLED, 'shoutbox'))
    {
      $shoutbox_file = $eqdkp_root_path.'plugins/shoutbox/includes/shoutbox.class.php';
      if (file_exists($shoutbox_file))
      {
        if (!defined('SHOUTBOX_DEFAULT_LIMIT')) define('SHOUTBOX_DEFAULT_LIMIT', 10);
        if (!defined('SHOUTBOX_WORDWRAP'))      define('SHOUTBOX_WORDWRAP',      20);
        
        include($shoutbox_file);
        $shoutbox = new Shoutbox();

        // return the output for module manager
        return $shoutbox->showShoutbox();
      }
    }

    return;
  }
}
?>

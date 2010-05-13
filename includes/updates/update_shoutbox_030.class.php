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


include_once($eqdkp_root_path.'maintenance/includes/sql_update_task.class.php');

if (!class_exists('update_shoutbox_030'))
{
  class update_shoutbox_030 extends sql_update_task
  {
    public $author      = 'Aderyn';
    public $version     = '0.3.0';    // new version
	public $name		= 'Shoutbox 0.3.0 Update';
	public $type 		= 'plugin_update';
    public $plugin_path = 'shoutbox'; // important!
  
    /**
     * Constructor
     */
    public function __construct()
    {
    	parent::__construct();
  
      // init language
    	$this->langs = array(
    		'english' => array(
    			'update_shoutbox_030' => 'Shoutbox 0.3.0 Update Package',
          // SQL
  				 1 => 'Delete guest setting',
  				 2 => 'Delete location setting',
  		  ),
  		  'german' => array(
  		    'update_shoutbox_030' => 'Shoutbox 0.3.0 Update Paket',
          // SQL
  				 1 => 'Entferne Gast Einstellung',
  				 2 => 'Entferne Positions Einstellung',
  		  ),
  	  );
  
      // init SQL querys
      $this->sqls = array(
    		 1 => 'DELETE FROM `__config` WHERE `config_name`=\'sb_invisible_to_guests\';',
   			 2 => 'DELETE FROM `__config` WHERE `config_name`=\'sb_input_box_below\';',
      );
    }
  }
}

?>

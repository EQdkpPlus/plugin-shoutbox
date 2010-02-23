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


/*+----------------------------------------------------------------------------
  | sb_style_base
  +--------------------------------------------------------------------------*/
if (!class_exists("sb_style_base"))
{
  abstract class sb_style_base
  {
    /**
     * Output Shoutbox ids to display
     */
    protected $shoutbox_ids = array();

    /**
     * Constructor
     *
     * @param  integer  $output_limit  Number of shoutbox id's to display
     */
    public function __construct($shoutbox_ids=array())
    {
      if (is_array($shoutbox_ids))
      {
        $this->shoutbox_ids = $shoutbox_ids;
      }
    }

    /**
     * showShoutbox
     * show the complete shoutbox
     *
     * @return  string
     */
    public function showShoutbox()
    {
      // output javascript code
      $this->shoutboxJCode();

      // output layout
      return $this->layoutShoutbox();
    }

    /**
     * getContent
     * get the content only of the shoutbox
     *
     * @param  string   $rpath        root path
     * @param  boolean  $utf8_encode  Encode UTF8?
     *
     * @return  string
     */
    public function getContent($rpath='', $utf8_encode=false)
    {
      global $eqdkp_root_path;

      // root path
      $root_path = ($rpath != '') ? $rpath : $eqdkp_root_path;

      // the delete form
      $htmlOut = '<form id="del_shoutbox" name="del_shoutbox" action="'.$eqdkp_root_path.'plugins/shoutbox/shoutbox.php" method="post">
                  </form>';

      // layout content
      $htmlOut .= $this->layoutContent($root_path, $utf8_encode);

      return $htmlOut;
    }

    /**
     * layoutShoutbox
     * get the complete shoutbox layout
     *
     * @return  string
     */
    protected abstract function layoutShoutbox();

    /**
     * layoutContent
     * layout the content only of the shoutbox
     *
     * @param  string   $root_path    root path
     * @param  boolean  $utf8_encode  Encode UTF8?
     *
     * @return  string
     */
    protected abstract function layoutContent($root_path, $utf8_encode);

    /**
     * shoutboxJCode
     * output the Java Code for the Shoutbox
     */
    private function shoutboxJCode()
    {
      global $user, $eqdkp_root_path, $eqdkp, $SID, $tpl;

      // set autoreload (0 = disable)
      $autoreload = ($eqdkp->config['sb_autoreload'] != '') ? intval($eqdkp->config['sb_autoreload']) : 0;
      $autoreload = ($autoreload < 600 ? $autoreload : 0);
      $autoreload = $autoreload * 1000; // to ms

      $jscode  = "// wait for the DOM to be loaded
                  $(document).ready(function() {
                    $('#Shoutbox').ajaxForm({
                      target: '#htmlShoutboxTable',
                      beforeSubmit:  function(formData, jqForm, options) {
                        showShoutboxRequest('".$eqdkp_root_path."', '".$user->lang['sb_save_wait']."');
                      },
                      success: function() {
                        showShoutboxFinished('".$eqdkp_root_path."', '".$user->lang['sb_submit_text']."', '".$user->lang['sb_reload']."');
                      }
                    });
                 ";
      if ($autoreload > 0)
      {
        $jscode .= "setInterval(function() {
                      shoutboxAutoReload('".$eqdkp_root_path."', '".$SID."', '".$user->lang['sb_reload']."');
                    }, ".$autoreload.");";
      }
      $jscode .= '});';

      $tpl->js_file($eqdkp_root_path.'plugins/shoutbox/includes/javascripts/shoutbox.js');
      $tpl->add_js($jscode);
    }
  }
}

?>

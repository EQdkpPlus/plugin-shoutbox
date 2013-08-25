<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-11-12 11:19:13 +0100 (Sa, 12. Nov 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 11432 $
 *
 * $Id: sb_style_base.class.php 11432 2011-11-12 10:19:13Z Aderyn $
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
  abstract class sb_style_base extends gen_class
  {
    /* List of dependencies */
    public static $shortcuts = array('user', 'config', 'tpl');

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
     * @param  string  $rpath  root path
     *
     * @return  string
     */
    public function getContent()
    {
      // the delete form
      $htmlOut = '<form id="del_shoutbox" name="del_shoutbox" action="'.$this->server_path.'plugins/shoutbox/shoutbox.php" method="post">
                  </form>';

      // layout content
      $htmlOut .= $this->layoutContent();

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
     * @return  string
     */
    protected abstract function layoutContent();

    /**
     * jCodeOrientation
     * get the orientation for the JCode output
     *
     * @return  string
     */
    protected abstract function jCodeOrientation();

    /**
     * shoutboxJCode
     * output the Java Code for the Shoutbox
     */
    private function shoutboxJCode()
    {
      // set autoreload (0 = disable)
      $autoreload = ($this->config->get('sb_autoreload') != '') ? intval($this->config->get('sb_autoreload')) : 0;
      $autoreload = ($autoreload < 600 ? $autoreload : 0);
      $autoreload = $autoreload * 1000; // to ms

      // set maxlength
      $max_text_length = ($this->config->get('sb_max_text_length') && is_numeric($this->config->get('sb_max_text_length'))) ? intval($this->config->get('sb_max_text_length')) : 160;

      $jscode  = "$('#Shoutbox').ajaxForm({
                    target: '#htmlShoutboxTable',
                    beforeSubmit:  function(formData, jqForm, options) {
                      showShoutboxRequest('".$this->user->lang('sb_save_wait')."');
                    },
                    success: function() {
                      showShoutboxFinished('".$this->user->lang('sb_submit_text')."', '".$this->user->lang('sb_reload')."');
                    }
                  });

                  $(document).on('keyup blur', 'textarea[name=sb_text]', function(e){
                    var maxlength = ".$max_text_length.";
                    var value = $(this).val();

                    // Trim
                    if (value.length > maxlength)
                    {
                      $(this).val(value.slice(0, maxlength));
                    }
                    		                  		
                    while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css(\"borderTopWidth\")) + parseFloat($(this).css(\"borderBottomWidth\"))) {
        				$(this).height($(this).height()+5);
    				};
                    		
                    if (e.which == 13) {
					    $('form#Shoutbox').submit();
					    return false;
					}
                  });
                 ";
      if ($autoreload > 0)
      {
        $jscode .= "setInterval(function() {
                      shoutboxAutoReload('".$this->user->lang('sb_reload')."', '".$this->jCodeOrientation()."');
                    }, ".$autoreload.");";
      }

      $this->tpl->add_js($jscode, 'docready');
      $this->tpl->js_file($this->root_path.'plugins/shoutbox/includes/javascripts/shoutbox.js');
    }
  }
}

?>
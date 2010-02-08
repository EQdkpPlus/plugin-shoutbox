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

/**
  * showShoutboxRequest
  * show saving icon
  *
  * @param  root  string  root directory of eqdkp
  * @param  text  string  text to display for "Saving..."
  */
function showShoutboxRequest(root, text) {
  // disable input field, hide reload icon and display "Saving..." text
  $('textarea[name=sb_text]').attr('disabled', 'disabled');
  $('#shoutbox_reload_button').html('');
  $('#shoutbox_button').html('<img src="'+root+'images/global/loading.gif" alt="Save"/>'+text);
}

/**
  * showShoutboxFinished
  * finished saving
  *
  * @param  root        string  root directory of eqdkp
  * @param  textSubmit  string  text to display on "Send" button
  * @param  textReload  string  text to display als alt of reload image
  */
function showShoutboxFinished(root, textSubmit, textReload) {
  // clear input field, enable input, show reload and set "Saving..." text back to send button
  $('textarea[name=sb_text]').val('');
  $('textarea[name=sb_text]').attr('disabled', '');
  $('#shoutbox_button').html('<input type="submit" class="liteoption bi_ok" name="sb_submit" value="'+textSubmit+'"/>');
  $('#shoutbox_reload_button').html('<img src="'+root+'plugins/shoutbox/images/reload.png" alt="'+textReload+'" title="'+textReload+'"/>');
}

/**
  * reloadShoutboxRequest2
  * show reloading icon
  *
  * @param  root        string  root directory of eqdkp
  */
function reloadShoutboxRequest(root) {
  // disable submit button and set loading image
  $('input[name=sb_submit]').attr('disabled', 'disabled');
  $('#shoutbox_reload_button').html('<img src="'+root+'images/global/loading.gif" alt="Load"/>');
}

/**
  * reloadShoutboxFinished
  * finished reload
  *
  * @param  root        string  root directory of eqdkp
  * @param  textReload  string  text to display als alt of reload image
  */
function reloadShoutboxFinished(root, textReload) {
  // enable submit button and reset reload image
  $('#shoutbox_reload_button').html('<img src="'+root+'plugins/shoutbox/images/reload.png" alt="'+textReload+'" title="'+textReload+'"/>');
  $('input[name=sb_submit]').attr('disabled', '');
}

/**
  * deleteShoutboxRequest
  * Delete a shoutbox entry
  *
  * @param  root        string  root directory of eqdkp
  * @param  id          int     id of delete button
  * @param  textDelete  string  text to display als alt of delete image
  */
function deleteShoutboxRequest(root, id, textDelete) {
  $('#shoutbox_delete_button_'+id).html('<img src="'+root+'images/global/loading.gif" alt="'+textDelete+'"/>');
}

/**
  * shoutboxAutoReload
  * auto reload the shoutbox
  *
  * @param  root        string  root directory of eqdkp
  * @param  sid         string  SID
  * @param  textReload  string  text to display als alt of reload image
  */
function shoutboxAutoReload(root, sid, textReload)
{
  // get the content of the shoutbox
  $('#reload_shoutbox').ajaxSubmit(
  {
    target: '#htmlShoutboxTable',
    url: root+'plugins/shoutbox/shoutbox.php'+sid+'&sb_root='+root,
    beforeSubmit: function(formData, jqForm, options) {
      reloadShoutboxRequest(root);
    },
    success: function() {
      reloadShoutboxFinished(root, textReload);
    }
  });
}

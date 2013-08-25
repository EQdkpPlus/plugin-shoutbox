/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2011-08-09 10:00:07 +0200 (Di, 09. Aug 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: Aderyn $
 * @copyright   2008-2011 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 10949 $
 *
 * $Id: shoutbox.js 10949 2011-08-09 08:00:07Z Aderyn $
 */

/**
  * showShoutboxRequest
  * show saving icon
  *
  * @param  root  string  root directory of eqdkp
  * @param  text  string  text to display for "Saving..."
  */
function showShoutboxRequest(text) {
  // disable input field, hide reload icon and display "Saving..." text
  $('textarea[name=sb_text]').attr('disabled', 'disabled');
  $('#shoutbox_reload_button').html('');
  $('#shoutbox_button').html('<i class="icon-spinner icon-spin"></i>'+text);
}

/**
  * showShoutboxFinished
  * finished saving
  *
  * @param  root        string  root directory of eqdkp
  * @param  textSubmit  string  text to display on "Send" button
  * @param  textReload  string  text to display als alt of reload image
  */
function showShoutboxFinished(textSubmit, textReload, showSubmitButton) {
  // clear input field, enable input, show reload and set "Saving..." text back to send button
  $('textarea[name=sb_text]').val('').css("height", "");
  $('textarea[name=sb_text]').removeAttr('disabled');
  if (showSubmitButton) {
	  $('#shoutbox_button').html('<input type="submit" class="liteoption bi_ok" name="sb_submit" value="'+textSubmit+'"/>');
  } else {
	  $('#shoutbox_button').html('');
  }
  $('#shoutbox_reload_button').html('<i class="icon-refresh icon-large" title="'+textReload+'"></i>');
}

/**
  * reloadShoutboxRequest2
  * show reloading icon
  *
  */
function reloadShoutboxRequest() {
  // disable submit button and set loading image
  $('input[name=sb_submit]').attr('disabled', 'disabled');
  $('#shoutbox_reload_button').html('<i class="icon-spinner icon-spin icon-large"></i>');
}

/**
  * reloadShoutboxFinished
  * finished reload
  *
  * @param  textReload  string  text to display als alt of reload image
  */
function reloadShoutboxFinished(textReload) {
  // enable submit button and reset reload image
  $('#shoutbox_reload_button').html('<i class="icon-refresh icon-large"></i>');
  $('input[name=sb_submit]').removeAttr('disabled');
}

/**
  * deleteShoutboxRequest
  * Delete a shoutbox entry
  *
  * @param  id          int     id of delete button
  * @param  textDelete  string  text to display als alt of delete image
  */
function deleteShoutboxRequest(id, textDelete) {
  $('#shoutbox_delete_button_'+id).html('<i class="icon-spinner icon-spin"></i>');
}

/**
  * shoutboxAutoReload
  * auto reload the shoutbox
  *
  * @param  textReload   string  text to display als alt of reload image
  * @param  orientation  string  orientation of shoutbox
  */
function shoutboxAutoReload(textReload, orientation)
{
  // get the content of the shoutbox
  $('#reload_shoutbox').ajaxSubmit(
  {
    target: '#htmlShoutboxTable',
    url: mmocms_root_path+'plugins/shoutbox/shoutbox.php'+mmocms_sid+'&sb_orientation='+orientation,
    beforeSubmit: function(formData, jqForm, options) {
      reloadShoutboxRequest();
    },
    success: function() {
      reloadShoutboxFinished(textReload);
    }
  });
}

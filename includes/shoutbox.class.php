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
  | Shoutbox
  +--------------------------------------------------------------------------*/
if (!class_exists("Shoutbox"))
{
  class Shoutbox
  {
    /**
     * RSS Feed object
     */
    private $rssFeed;

    /**
     * Required versions
     */
    private $reqVersions = array(
        'php'   => '5.0.0',
        'eqdkp' => '0.7.0.1'
    );

    /**
     * Output limit (number of entries to display)
     */
    private $output_limit;

    /**
     * Constructor
     */
    public function __construct()
    {
      global $core, $pcache, $user;

      $this->rssFeed = new Feed();
      $this->rssFeed->title          = $user->lang['shoutbox'];
      $this->rssFeed->description    = $core->config['main_title'].' - '.$user->lang['shoutbox'];
      $this->rssFeed->link           = $core->BuildLink();
      $this->rssFeed->feedfile       = $core->BuildLink().$pcache->FileLink('shoutbox.xml', 'shoutbox');
      $this->rssFeed->published      = $time->time;
      $this->rssFeed->language       = 'de-DE';

      // get output limit
      $this->output_limit = ($core->config['sb_output_count_limit'] > 0 ? $core->config['sb_output_count_limit'] : 10);
    }

    /**
     * checkRequirements
     * Check the shoutbox requirements
     *
     * @returns true if success, otherwise error string
     */
    public function checkRequirements()
    {
      global $user, $core;

      // set defult to OK
      $result = true;

      // compare
      if (version_compare(phpversion(), $this->reqVersions['php'], "<"))
      {
        $result = sprintf($user->lang['sb_php_version'], $this->reqVersions['php'], phpversion());
      }
      else if (version_compare($core->config['plus_version'], $this->reqVersions['eqdkp'], "<"))
      {
        $result = sprintf($user->lang['sb_plus_version'], $this->reqVersions['eqdkp'],
                          (($core->config['plus_version'] > 0) ? $core->config['plus_version'] : '[non-PLUS]'));
      }

      return $result;
    }

    /**
     * insertShoutboxEntry
     * Insert a shoutbox entry for current user or member
     *
     * @param    int     $usermember_id   user or member id
     * @param    string  $text            text to insert
     *
     * @returns  true if success, otherwise false
     */
    public function insertShoutboxEntry($usermember_id, $text)
    {
      global $user, $pdh;

      // is user allowed to add a shoutbox entry?
      if ($user->data['user_id'] != ANONYMOUS && $user->check_auth('u_shoutbox_add', false))
      {
        // insert
        $shoutbox_id = $pdh->put('shoutbox', 'add', array($usermember_id, $text));
        if ($shoutbox_id === false)
          return false;

        // process hook queue
        $pdh->process_hook_queue();

        // recreate RSS
        $this->createRSS();

        return true;
      }

      return false;
    }

    /**
     * deleteShoutboxEntry
     * delete a shoutbox entry
     *
     * @param  int  $shoutbox_id  shoutbox entry id
     */
    public function deleteShoutboxEntry($shoutbox_id)
    {
      global $user, $pdh;

      // is user owner of the shoutbox entry or is admin?
      if (($user->data['user_id'] != ANONYMOUS && $user->data['user_id'] == $pdh->get('shoutbox', 'userid', array($shoutbox_id))) ||
          ($user->check_auth('a_shoutbox_delete', false)))
      {
        $result = $pdh->put('shoutbox', 'delete', array($shoutbox_id));
        if (!$result)
          return false;

        // process hook queue
        $pdh->process_hook_queue();

        // recreate RSS
        $this->createRSS();

        return $result;
      }

      return false;
    }

    /**
     * deleteAllEntries
     * delete all shoutbox entries
     */
    public function deleteAllEntries()
    {
      global $user, $pdh;

      // is user allowed to delete?
      if ($user->data['user_id'] != ANONYMOUS && $user->check_auth('a_shoutbox_delete', false))
      {
        // get all shoutbox ids
        $shoutbox_ids = $pdh->get('shoutbox', 'id_list');
        if (is_array($shoutbox_ids))
        {
          foreach ($shoutbox_ids as $shoutbox_id)
            $pdh->put('shoutbox', 'delete', array($shoutbox_id));

          // process hook queue
          $pdh->process_hook_queue();

          // recreate RSS
          $this->createRSS();
        }
      }
    }

    /**
     * showShoutbox
     * show the complete shoutbox
     *
     * @param  string  $orientation  orientation vertical/horizontal
     *
     * @returns  string
     */
    public function showShoutbox($orientation='vertical')
    {
      global $eqdkp_root_path, $pcache, $tpl, $core, $user;

      $htmlOut = '';

      // get ids
      $shoutbox_ids = $this->getShoutboxOutEntries();

      // get the layout
      $layout_file = $eqdkp_root_path.'plugins/shoutbox/includes/styles/sb_'.$orientation.'.class.php';
      if (file_exists($layout_file))
      {
        include_once($layout_file);
        $class_name = 'sb_'.$orientation;
        $shoutbox_style = new $class_name($shoutbox_ids);
      }

      // show shoutbox
      if ($shoutbox_style)
        $htmlOut .= $shoutbox_style->showShoutbox();

      // create RSS feed if they do not exist
      $rss_file = $core->BuildLink().$pcache->FileLink('shoutbox.xml', 'shoutbox');
      if (!is_file($rss_file))
        $this->createRSS();

      // add link to RSS
      $tpl->add_rssfeed($core->config['guildtag'].' - '.$user->lang['shoutbox'], $rss_file);

      return $htmlOut;
    }

    /**
     * getContent
     * get the content of the shoutbox
     *
     * @param  string   $orientation  orientation vertical/horizontal
     * @param  string   $rpath        root path
     *
     * @returns  string
     */
    public function getContent($orientation, $rpath='')
    {
      global $eqdkp_root_path, $pcache, $pdh;

      // get shoutbox ids to display
      $shoutbox_ids = $this->getShoutboxOutEntries();

      // empty output
      $htmlOut = '';

      // get the layout
      $layout_file = $eqdkp_root_path.'plugins/shoutbox/includes/styles/sb_'.$orientation.'.class.php';
      if (file_exists($layout_file))
      {
        include_once($layout_file);
        $class_name = 'sb_'.$orientation;
        $shoutbox_style = new $class_name($shoutbox_ids);
      }

      // get content
      if ($shoutbox_style)
        $htmlOut .= $shoutbox_style->getContent($rpath);

      return $htmlOut;
    }

    /**
     * convertFromMemberToUser
     * convert all entries from member entries to user entries
     *
     * @returns  true if success, otherwise false
     */
    public function convertFromMemberToUser()
    {
      global $pdh;

      // get all shoutbox ids
      $shoutbox_ids = $pdh->get('shoutbox', 'id_list');
      if (is_array($shoutbox_ids))
      {
        // for each entry, get the current member id, look up the corresponding user id and
        // update with user id
        foreach ($shoutbox_ids as $shoutbox_id)
        {
          // get member id
          $member_id = $pdh->get('shoutbox', 'usermemberid', array($shoutbox_id));
          // lookup the user id for this member
          $user_id = $pdh->get('member_connection', 'userid', array($member_id));
          // update with new user id
          $pdh->put('shoutbox', 'set_user', array($shoutbox_id, $user_id));
        }

        // process hook queue
        $pdh->process_hook_queue();

        // recreate RSS
        $this->createRSS();
      }

      return true;
    }

    /**
     * getShoutboxOutEntries
     * get the id list to display
     *
     * @returns  array(ids)
     */
    private function getShoutboxOutEntries()
    {
      global $pdh;

      $shoutbox_out = array();

       // get all shoutbox id's
      $shoutbox_ids = $pdh->get('shoutbox', 'id_list');
      if (is_array($shoutbox_ids))
      {
        $shoutbox_count = count($shoutbox_ids);
        $output_count = min($this->output_limit, $shoutbox_count);

        // copy the last n elements to the output entry
        for ($i = 0; $i < $output_count; $i++)
          $shoutbox_out[] = $shoutbox_ids[$i];
      }

      return $shoutbox_out;
    }

    /**
     * createRSS
     * create RSS feed
     */
    private function createRSS()
    {
      global $pcache, $pdh;

      // get shoutbox ids
      $shoutbox_ids = $this->getShoutboxOutEntries();
      if (is_array($shoutbox_ids))
      {
        // create RSS feed item
        foreach ($shoutbox_ids as $shoutbox_id)
        {
          $rssitem = new feeditems();
          $rssitem->title       = $pdh->get('shoutbox', 'usermembername', array($shoutbox_id));
          $rssitem->description = $pdh->geth('shoutbox', 'text', array($shoutbox_id));
          $rssitem->link        = $this->rssFeed->link;
          $rssitem->published   = $pdh->get('shoutbox', 'date', array($shoutbox_id));
          $rssitem->author      = $pdh->get('shoutbox', 'usermembername', array($shoutbox_id));
          $rssitem->source      = $this->rssFeed->link;
          $this->rssFeed->addItem($rssitem);
        }
      }

      // save RSS
      $this->rssFeed->save($pcache->FilePath('shoutbox.xml', 'shoutbox'), false);
    }

  }
}

?>

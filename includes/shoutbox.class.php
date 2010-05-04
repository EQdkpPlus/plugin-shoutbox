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
     * RSS object
     */
    private $rss;

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
      global $eqdkp, $pcache, $user;

      $this->rss = new UniversalFeedCreator();
      $this->rss->title          = $user->lang['shoutbox'];
      $this->rss->description    = $eqdkp->config['main_title'].' - '.$user->lang['shoutbox'];
      $this->rss->link           = $pcache->BuildLink();
      $this->rss->syndicationURL = $pcache->BuildLink().$_SERVER['PHP_SELF'];

      // read in shoutbox config
      $this->readConfig();

      // get output limit
      $this->output_limit = ($eqdkp->config['sb_output_count_limit'] > 0 ? $eqdkp->config['sb_output_count_limit'] : 10);
    }

    /**
     * checkRequirements
     * Check the shoutbox requirements
     *
     * @returns true if success, otherwise error string
     */
    public function checkRequirements()
    {
      global $user;

      // set defult to OK
      $result = true;

      // compare
      if (version_compare(phpversion(), $this->reqVersions['php'], "<"))
      {
        $result = sprintf($user->lang['sb_php_version'], $this->reqVersions['php'], phpversion());
      }
      else if (version_compare(EQDKPPLUS_VERSION, $this->reqVersions['eqdkp'], "<"))
      {
        $result = sprintf($user->lang['sb_plus_version'], $this->reqVersions['eqdkp'],
                          ((EQDKPPLUS_VERSION > 0) ? EQDKPPLUS_VERSION : '[non-PLUS]'));
      }

      return $result;
    }

    /**
     * insertShoutboxEntry
     * Insert a shoutbox entry for current member
     *
     * @param    int    $member_id   member id
     * @param    string $text        text to insert
     * @param    int    $tz          timezone offset
     */
    public function insertShoutboxEntry($member_id, $text, $tz=0)
    {
      global $user, $pdh;

      // is user allowed to add a shoutbox entry?
      if ($user->data['user_id'] != ANONYMOUS && $user->check_auth('u_shoutbox_add', false))
      {
        // insert
        $shoutbox_id = $pdh->put('shoutbox', 'add', array($member_id, $text, $tz));
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
     * showShoutbox
     * show the complete shoutbox
     *
     * @param  string  $orientation  orientation vertical/horizontal
     *
     * @return  string
     */
    public function showShoutbox($orientation='vertical')
    {
      global $eqdkp_root_path, $pcache, $tpl, $eqdkp, $user;

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
      $rss_file = $pcache->BuildLink().$pcache->FileLink('shoutbox.xml', 'shoutbox');
      if (!is_file($rss_file))
        $this->createRSS();

      // add link to RSS
      $tpl->add_rssfeed($eqdkp->config['guildtag'].' - '.$user->lang['shoutbox'], $rss_file);

      return $htmlOut;
    }

    /**
     * getContent
     * get the content of the shoutbox
     *
     * @param  string   $orientation  orientation vertical/horizontal
     * @param  string   $rpath        root path
     *
     * @return  string
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
     * getShoutboxOutEntries
     * get the id list to display
     *
     * @return  array(ids)
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
          $rssitem = new FeedItem();
          $rssitem->title       = utf8_decode($pdh->get('shoutbox', 'membername', array($shoutbox_id)));
          $rssitem->link        = $this->rss->link;
          $rssitem->description = utf8_decode($pdh->geth('shoutbox', 'text', array($shoutbox_id)));
          $rssitem->date        = $pdh->get('shoutbox', 'date', array($shoutbox_id));
          $rssitem->source      = $this->rss->link;
          $rssitem->author      = utf8_decode($pdh->get('shoutbox', 'membername', array($shoutbox_id)));
          $rssitem->guid        = $shoutbox_id;
          $this->rss->addItem($rssitem);
        }
      }

      // save RSS
      $this->rss->saveFeed('RSS2.0', $pcache->FilePath('shoutbox.xml', 'shoutbox'), false);
    }

    /**
     * readConfig
     * Read in the shoutbox configuration
     */
    private function readConfig()
    {
      global $eqdkp, $db;

      $sql = 'SELECT * FROM `__shoutbox_config`';
      $result = $db->query($sql);
      if ($result)
      {
        $sb_conf = array();
        while(($row = $db->fetch_record($result)))
        {
          $sb_conf[$row['config_name']] = $row['config_value'];
        }
        $db->free_result($result);

        // merge to EQDKP config
        $eqdkp->config = array_merge($eqdkp->config, $sb_conf);
      }
    }

  }
}

?>

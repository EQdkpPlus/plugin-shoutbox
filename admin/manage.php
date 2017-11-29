<?php
/*	Project:	EQdkp-Plus
 *	Package:	Shoutbox Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'shoutbox');

$eqdkp_root_path = './../../../';
include_once('./../includes/common.php');

/*+----------------------------------------------------------------------------
  | ShoutboxManage
  +--------------------------------------------------------------------------*/
class ShoutboxManage extends page_generic {

	/**
	* Constructor
	*/
	public function __construct(){
		// Plugin installed?
		if (!$this->pm->check('shoutbox', PLUGIN_INSTALLED))
			message_die($this->user->lang('sb_plugin_not_installed'));

		$handler = array(
			'sb_delete'	=> array('process' => 'delete', 'csrf' => true, 'check' => 'a_shoutbox_delete'),
		);
		parent::__construct('a_shoutbox_', $handler);

		$this->process();
	}

	/**
	* delete
	* Delete entries
	*/
	public function delete(){
		$messages = array();

		$delete_ids = $this->in->getArray('selected_ids', 'int');
		if (is_array($delete_ids) && count($delete_ids) > 0){
			foreach ($delete_ids as $delete_id)
				register('ShoutboxClass')->deleteShoutboxEntry($delete_id);

			$messages[] = $this->user->lang('sb_delete_success');
		}
		$this->display($messages);
	}

	/**
	* display
	* Display the page
	*
	* @param  array   $messages  Array of Messages to output
	*/
	public function display($messages=array()){
		// -- Messages ------------------------------------------------------------
		if ($messages){
			foreach($messages as $name)
				$this->core->message($name, $this->user->lang('shoutbox'), 'green');
		}

		// -- get shoutbox entries ------------------------------------------------
		$shoutbox_ids = $this->pdh->get('shoutbox', 'id_list', array());
		$shoutbox_out = array();


		// -- build 2D array with [year][month] -----------------------------------
		$date_array = array();
		foreach ($shoutbox_ids as $shoutbox_id){
			$shoutbox_date			= $this->pdh->get('shoutbox', 'date', array($shoutbox_id));
			$shoutbox_date_year		= $this->time->date('Y', $shoutbox_date);
			$shoutbox_date_month	= $this->time->date('m', $shoutbox_date);
			$date_array[$shoutbox_date_year][$shoutbox_date_month][] = $shoutbox_id;
		}


		// -- output date select on left side -------------------------------------
		foreach ($date_array as $year => $months){
			$this->tpl->assign_block_vars('year_row', array(
				'YEAR' => $year
			));

			foreach ($months as $month => $ids){
				$this->tpl->assign_block_vars('year_row.month_row', array(
					'MONTH'		=> $this->time->date('F', $this->time->mktime(0, 0, 0, $month, 1, $year)),
					'COUNT'		=> count($ids),
					'LINK_VIEW'	=> $this->root_path.'plugins/shoutbox/admin/manage.php'.$this->SID.'&amp;year='.$year.'&amp;month='.$month,
				));
			}
		}


		// -- year/month select? --------------------------------------------------
		$page_title = '';
		if ($this->in->get('year') && $this->in->get('month')){
			// add all shoutbox entries within date/month to the output array
			$shoutbox_out = $date_array[$this->in->get('year')][$this->in->get('month')];
			$url_suffix   = '&amp;year='.$this->in->get('year').'&amp;month='.$this->in->get('month');
			$page_title   = $this->time->date('F', $this->time->mktime(0, 0, 0, $this->in->get('month'), 1, $this->in->get('year'))).' '.$this->in->get('year');
		}
		// -- search? -------------------------------------------------------------
		else if ($this->in->get('search')){
			// loop through all the shoutbox entries and try to find in either username or in text
			foreach ($shoutbox_ids as $shoutbox_id){
				$text	= $this->pdh->get('shoutbox', 'text',           array($shoutbox_id));
				$name	= $this->pdh->get('shoutbox', 'usermembername', array($shoutbox_id));
				$search	= $this->in->get('search');
				if (strpos($text, $search) !== false || strpos($name, $search) !== false)
					$shoutbox_out[] = $shoutbox_id;
				$url_suffix	= '&amp;search='.sanitize($this->in->get('search'));
				$page_title= $this->user->lang('search').': '.sanitize($this->in->get('search'));
			}
		}
		// -- last month ----------------------------------------------------------
		else if (count($shoutbox_ids) > 0){
			// show the last month only
			$shoutbox_date			= $this->pdh->get('shoutbox', 'date', array($shoutbox_ids[0]));
			$shoutbox_date_year		= $this->time->date('Y', $shoutbox_date);
			$shoutbox_date_month	= $this->time->date('m', $shoutbox_date);
			$shoutbox_out			= $date_array[$shoutbox_date_year][$shoutbox_date_month];
			$url_suffix				= '';
			$page_title				= $this->time->date('F', $this->time->mktime(0, 0, 0, $shoutbox_date_month, 1, $shoutbox_date_year)).' '.$shoutbox_date_year;
		}

		// -- pagination ----------------------------------------------------------
		// get total and start
		$start = $this->in->get('start', 0);
		$total_entries = count($shoutbox_out);
		$limit = 50;
		$end = min($start + $limit, $total_entries);
		// pagination
		$pagination = generate_pagination('manage.php'.$this->SID.$url_suffix, $total_entries, $limit, $start);

		// -- display entries -----------------------------------------------------
		require_once($this->root_path.'plugins/shoutbox/includes/systems/shoutbox.esys.php');
		$hptt_sort       = $this->in->get('sort');
		$hptt_url_suffix = $url_suffix.($start > 0 ? '&amp;start='.$start : '');
		$hptt = $this->get_hptt($systems_shoutbox['pages']['manage'], $shoutbox_ids, $shoutbox_out, array());

		// -- Template ------------------------------------------------------------
		$this->jquery->selectall_checkbox('sb_delete_all', 'selected_ids[]');
		$this->tpl->assign_vars(array (
			// Form
			'SB_TABLE'			=> $hptt->get_html_table($hptt_sort, $hptt_url_suffix, $start, $end),
			'SB_PAGE_TITLE'		=> ($page_title != '') ? '&raquo; '.$page_title : '',

			// pagination
			'START'				=> $start,
			'PAGINATION'		=> $pagination,
		));

		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array (
			'page_title'	=> $this->user->lang('shoutbox').' '.$this->user->lang('sb_manage_archive').' '.$page_title,
			'template_path'	=> $this->pm->get_data('shoutbox', 'template_path'),
			'template_file'	=> 'admin/manage.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('shoutbox').': '.$this->user->lang('sb_manage_archive').' '.$page_title, 'url'=>' '],
				],
			'display'		=> true
		));
	}
}
registry::register('ShoutboxManage');
?>

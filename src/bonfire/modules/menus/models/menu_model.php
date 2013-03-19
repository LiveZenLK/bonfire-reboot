<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menu_model extends BF_Model {

	protected $_table		= 'menus';
	protected $primary_key	= 'menu_id';

	protected $set_created	= FALSE;
	protected $set_modified	= FALSE;

	protected $validate 	= array(
		array(
			'field'		=> 'name',
			'label'		=> 'Name',
			'rules'		=> 'required|trim|strip_tags|[max_length[255]|xss_clean'
		),
		array(
			'field'		=> 'system_name',
			'label'		=> 'System Name',
			'rules'		=> 'trim|strip_tags|max_length[255]|xss_clean'
		)
	);

	protected $before_insert = array('add_system_name');
	protected $before_update = array('add_system_name');

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Scope Methods
	//--------------------------------------------------------------------

	/**
	 * Tells the next find/etc call to grab the menu links belonging to
	 * this menu.
	 */
	public function with_links()
	{
		array_unshift($this->after_find, 'find_links_for_menu');

		return $this;
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Trigger Methods
	//--------------------------------------------------------------------

	/**
	 * Creates a url_title() version of the name if the
	 * system_name doesn't already exist, and adds it to the data array.
	 *
	 * Intended for use within the $before_insert and $before_update triggers.
	 *
	 * @param array $data The array of data to be inserted
	 *
	 * @return array
	 */
	public function add_system_name($data)
	{
		if (!isset($data['system_name']) && isset($data['name']))
		{
			if (!function_exists('url_title'))
			{
				$this->load->helper('url');
			}

			$data['system_name'] = url_title($data['name'], '_', true);
		}

		return $data;
	}

	//--------------------------------------------------------------------

	/**
	 * Grabs the menu links for a single menu. Intended for use by
	 * during the 'after_find' trigger.
	 *
	 * @param  object $row The object result for a menu_model->find() call.
	 */
	public function find_links_for_menu($row)
	{
		$this->load->model('menus/menu_link_model');

		if (isset($row->{$this->primary_key}))
		{
			$this->load->model('menus/menu_link_model');
			$row->links = $this->menu_link_model->for_menu($row->{$this->primary_key})
												 ->order_by('weight', 'asc')
												 ->find_all();
		}

		return $row;
	}

	//--------------------------------------------------------------------

}

//--------------------------------------------------------------------

/**
 * A helper function that displays a menu as an ordered list.
 * @param  [type] $menu_name [description]
 * @param  string $id        [description]
 * @param  string $class     [description]
 * @return [type]            [description]
 */
function show_menu($menu_name, $class='', $id='')
{
	$ci =& get_instance();
	$ci->load->model('menus/menu_model');

	$menu = $ci->menu_model->with_links()->find_by('system_name', $menu_name);

	// Menu not found?
	if (is_array($menu) && !count($menu))
	{
		$ci->lang->load('menus');
		return sprintf(lang('menu_not_found'), $menu_name);
	}

	// Menu didn't have any links?
	if (!count($menu->links))
	{
		$ci->lang->load('menus');
		return sprintf(lang('menu_no_links'), $menu_name);
	}

	$output = "<ol id='$id' class='$class'>\n";

	foreach ($menu->links as $link)
	{
		$active = strpos(site_url($link->url), current_url()) !== false ? ' class="active"' : '';
		$output .= "<li{$active}><a href='{$link->url}''>{$link->title}</a></li>";
	}

	$output .= "</ol>\n";

	return $output;
}

//--------------------------------------------------------------------

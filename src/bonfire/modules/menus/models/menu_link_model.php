<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menu_link_model extends BF_Model {

	protected $_table		= 'menu_links';

	protected $set_created	= FALSE;
	protected $set_modified	= FALSE;

	protected $validate 	= array(
		array(
			'field'		=> 'title',
			'label'		=> 'Title',
			'rules'		=> 'required|trim|strip_tags|[max_length[255]|xss_clean'
		),
		array(
			'field'		=> 'url',
			'label'		=> 'URL',
			'rules'		=> 'required|trim|max_length[255]|xss_clean'
		),
		array(
			'field'		=> 'menu_id',
			'label'		=> 'Menu ID',
			'rules'		=> 'required|trim|is_natural_no_zero|less_than[1000]'
		),
		array(
			'field'		=> 'weight',
			'label'		=> 'Weight',
			'rules'		=> 'trim|is_natural_no_zero|less_than[1000]'
		),
		array(
			'field'		=> 'parent_id',
			'label'		=> 'Parent',
			'rules'		=> 'trim|is_natural|max_length[9]'
		)
	);

	//--------------------------------------------------------------------

	/**
	 * Sets the scope of the call to be specific to a single menu name.
	 * Can pass either an INT that will be treated as the id, or a string
	 * which will match against the system name.
	 *
	 * @param  INT/sring $model Either the primary key or the system_name of the menu.
	 */
	public function for_menu($menu)
	{
		// Is it an ID?
		if (is_numeric($menu))
		{
			$this->db->where('menu_id', $menu);
		}
		else
		{
			$this->load->model('menus/menu_model');
			$menu = $this->menu_model->find_by('system_name', $menu);

			if ($menu && isset($menu->menu_id))
			{
				$this->db->where('menu_id', $menu);
			}
		}

		return $this;
	}

	//--------------------------------------------------------------------

}
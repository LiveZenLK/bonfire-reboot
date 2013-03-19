<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_menus_module extends Migration {

	public function up()
	{
		// Menus database table
		$fields = array(
			'menu_id'	=> array(
				'type'			=> 'INT',
				'constraint'	=> 3,
				'null'			=> false,
				'auto_increment' => true
			),
			'name' => array(
				'type'			=> 'VARCHAR',
				'constraint'	=> 255,
				'null'			=> false
			),
			'system_name' 	=> array(
				'type'			=> 'varchar',
				'constraint'	=> 255,
				'null'			=> false
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('menu_id', true);
		$this->dbforge->create_table('menus', true);

		// Menu links
		$fields = array(
			'id'			=> array(
				'type'			=> 'INT',
				'constraint'	=> 9,
				'null'			=> false,
				'auto_increment'	=> true
			),
			'title'			=> array(
				'type'			=> 'varchar',
				'constraint'	=> 255,
				'null'			=> false
			),
			'url'			=> array(
				'type'			=> 'varchar',
				'constraint'	=> 255,
				'null'			=> false
			),
			'menu_id'		=> array(
				'type'			=> 'int',
				'constraint'	=> 3,
				'null'			=> false,
				'default'		=> 0
			),
			'weight'		=> array(
				'type'			=> 'int',
				'constraint'	=> 3,
				'null'			=> false,
				'default'		=> 50
			),
			'parent_id'		=> array(
				'type'			=> 'int',
				'constraint'	=> 9,
				'null'			=> false,
				'default'		=> 0
			),
			'has_children'	=> array(
				'type'			=> 'tinyint',
				'constraint'	=> 1,
				'null'			=> false,
				'default'		=> 0
			)
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);
		$this->dbforge->add_key('menu_id');
		$this->dbforge->create_table('menu_links', true);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->dbforge->drop_table('menus');
		$this->dbforge->drop_table('menu_links');
	}

	//--------------------------------------------------------------------

}
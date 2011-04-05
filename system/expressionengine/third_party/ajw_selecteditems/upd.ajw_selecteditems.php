<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajw_selecteditems_upd { 

	var $version        = '2.0'; 

	function Ajw_selecteditems_upd( $switch = TRUE ) 
	{ 
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	} 

	function install() 
	{
		$this->EE->load->dbforge();

		$data = array(
			'module_name' => 'Ajw_selecteditems' ,
			'module_version' => $this->version,
			'has_cp_backend' => 'y'
			);

		$fields = array(
			'ajw_selecteditems_id' => array(
				'type' => 'int',
				'constraint' => '6',
				'unsigned' => TRUE,
				'auto_increment'=> TRUE
			),
			'ajw_selecteditems_name' => array(
				'type' => 'varchar',
				'constraint' => '255',
				'null' => FALSE
			), 
			'ajw_selecteditems_description' => array(
				'type' => 'text',
				'null' => FALSE
			), 
			'ajw_selecteditems_cache' => array(
				'type' => 'text'
			), 
			'ajw_selecteditems_orderby' => array(
				'type' => 'varchar',
				'constraint' => '64',
			), 
			'ajw_selecteditems_channels' => array(
				'type' => 'varchar',
				'constraint' => '64',
			), 
			'ajw_selecteditems_site_id' => array(
				'type' => 'int',
				'constraint' => '4',
				'unsigned' => TRUE,
				'null' => FALSE,
				'default' => '1'
			)
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('ajw_selecteditems_id', TRUE);
		$this->EE->dbforge->create_table('ajw_selecteditems');

		$this->EE->db->insert('modules', $data); 

		return TRUE;
	}

	function uninstall() 
	{ 
		$this->EE->load->dbforge();

		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => 'Ajw_selecteditems'));

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		$this->EE->db->where('module_name', 'Ajw_selecteditems');
		$this->EE->db->delete('modules');

		$this->EE->db->where('class', 'Ajw_selecteditems');
		$this->EE->db->delete('actions');

		$this->EE->db->where('class', 'Ajw_selecteditems_mcp');
		$this->EE->db->delete('actions');

		$this->EE->dbforge->drop_table('ajw_selecteditems');

		return TRUE;
	}

	function update($current = '')
	{
		return FALSE;
	}

}

/* End of file upd.ajw_selecteditems.php */
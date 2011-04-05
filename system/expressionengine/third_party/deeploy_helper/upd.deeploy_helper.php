<?php	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
==========================================================
	This software package is intended for use with 
	ExpressionEngine.	ExpressionEngine is Copyright © 
	2002-2009 EllisLab, Inc. 
	http://ellislab.com/
==========================================================
	THIS IS COPYRIGHTED SOFTWARE, All RIGHTS RESERVED.
	Written by: Travis Smith and Justin Crawdford
	Copyright (c) 2009 Hop Studios
	http://www.hopstudios.com/software/
--------------------------------------------------------
	Please do not distribute this software without written
	consent from the author.
==========================================================
	Files:
	- mcp.deeploy_helper.php
	- lang.deeploy_helper.php
----------------------------------------------------------
	Purpose: 
	- Helps change site preferences all in one handy panel
----------------------------------------------------------
	Notes: 
	- None
==========================================================
*/

require_once PATH_THIRD."deeploy_helper/config.php";

class Deeploy_helper_upd { 

var $version = VERSION;

	function Deeploy_helper_upd()
	{
		$this->EE =& get_instance();
	}


	// ---------------------------------------- 
	//	Module installer 
	// ---------------------------------------- 
	function install() 
	{ 
		/* orig
		$sql[] = "INSERT INTO exp_modules (module_id, module_name, module_version, has_cp_backend) 
							VALUES ('', 'Deeploy_helper', '$this->version', 'y')"; 
		*/

		$data = array(
			'module_name'	=> 'Deeploy_helper',
			'module_version'	=> $this->version,
			'has_cp_backend'	=> 'y',
			'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('exp_modules', $data);

		return true; 
	} 
	// END 
		 
		 
	// ---------------------------------------- 
	//	Module de-installer 
	// ---------------------------------------- 
	function uninstall() 
	{ 
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => 'Deeploy_helper'));

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		$this->EE->db->where('module_name', 'Deeploy_helper');
		$this->EE->db->delete('modules');

		$this->EE->db->where('class', 'Deeploy_helper');
		$this->EE->db->delete('actions');
	

		return true; 
	} 
	// END 

	// ---------------------------------------- 
	//	Module updater 
	// ---------------------------------------- 
	function update($current = '')
	{
		if ($current == $this->version)
		{
			return FALSE;
		}
			
		if ($current < 1.0) 
		{
			// Do your update code here
		} 
		
		return TRUE; 
	}


}

/* END Class */

/* End of file upd.deeploy_helper.php */

<?php if ( ! defined('EXT')) exit('No direct script access allowed');
 
 /**
 * Solspace - Freeform
 *
 * @package		Solspace:Freeform
 * @author		Solspace DevTeam
 * @copyright	Copyright (c) 2008-2010, Solspace, Inc.
 * @link		http://solspace.com/docs/addon/c/Freeform/
 * @version		3.0.5
 * @filesource 	./system/modules/freeform/
 * 
 */
 
 /**
 * Freeform - Actions
 *
 * Handles All Form Submissions and Action Requests Used on both User and CP areas of EE
 *
 * @package 	Solspace:Freeform
 * @author		Solspace DevTeam
 * @filesource 	./system/modules/freeform/act.freeform.php
 */

if (APP_VER < 2.0)
{
	require_once PATH.'bridge/lib/addon_builder/extension_builder.php';
}
else
{
	require_once PATH_THIRD . 'bridge/lib/addon_builder/extension_builder.php';
}

class Freeform_actions extends Addon_builder_bridge {
    
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 
	 * @access	public
	 * @return	null
	 */
    
	function Freeform_actions()
    {	
    	parent::Addon_builder_bridge('freeform');
    	
    	/** -------------------------------------
		/**  Module Installed and What Version?
		/** -------------------------------------*/
			
		if ($this->database_version() == FALSE OR $this->version_compare($this->database_version(), '<', FREEFORM_VERSION))
		{
			return;
		}
	}
	/* END */


}
/* END Freeform_actions Class */


?>

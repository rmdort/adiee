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
 * Freeform - Data Models
 *
 * @package 	Solspace:Freeform
 * @author		Solspace DevTeam
 * @filesource 	./system/modules/freeform/data.freeform.php
 */
 

if (APP_VER < 2.0)
{
	require_once PATH.'bridge/lib/addon_builder/data.addon_builder.php';
}
else
{
	require_once PATH_THIRD . 'bridge/lib/addon_builder/data.addon_builder.php';
}

class Freeform_data extends Addon_builder_data_bridge {

	var $cached = array();

	public $preference_defaults 	= array(
		'max_user_recipients'	=> '10',
		'spam_count'			=> '30',
		'spam_interval'			=> '60'		
	);

	// --------------------------------------------------------------------
	
	/**
	 * set_prefs
	 *
	 * @access	public
	 * @param	array  associative array of prefs and values
	 * @return	array
	 */
	 
	function set_prefs($params = array())
	{
		//no shenanigans
		if(empty($params))
		{
			return;
		}
		
		//kill all entries
		ee()->db->query("TRUNCATE exp_freeform_preferences");
       
       	//params is a key, value pair, but we need to send each as inserts
       	//to the DB to thier respective fields
		foreach($params as $key => $value)
        {
			ee()->db->query(
				ee()->db->insert_string(
					'exp_freeform_preferences', 
					array(
						'preference_name' 	=> $key,
						'preference_value'	=> $value
					)
				
				)
			);	
        }	
	}
    // END set_prefs
    


	// --------------------------------------------------------------------
	
	/**
	 * get_prefs
	 *
	 * @access	public
	 * @return	array associative array of prefs
	 */
    
	function get_prefs($params = array()) /* dummy for _imploder */
    {	
 		//  --------------------------------------------
        //   Prep Cache, Return if Set
        //  --------------------------------------------
 		
 		$cache_name = __FUNCTION__;
 		$cache_hash = $this->_imploder(func_get_args());
 		
 		if (isset($this->cached[$cache_name][$cache_hash]))
 		{
 			return $this->cached[$cache_name][$cache_hash];
 		}
 		
 		$this->cached[$cache_name][$cache_hash] = array();
 		
 		//  --------------------------------------------
        //   Perform the Actual Work
        //  --------------------------------------------
		
		$sql = 'SELECT preference_name, preference_value FROM exp_freeform_preferences';
		
		$query = ee()->db->query($sql);
		
		//if no results, send defaults
		if($query->num_rows() == 0)
		{
			return $this->preference_defaults;	
		}
		
		foreach($query->result_array() as $row)
		{
			$this->cached[$cache_name][$cache_hash][$row['preference_name']] = $row['preference_value'];
		}
		
		foreach($this->preference_defaults as $default_pref => $pref_data)
		{
			if ( ! isset($this->cached[$cache_name][$cache_hash][$default_pref]) )
			{
				$this->cached[$cache_name][$cache_hash][$default_pref] = $pref_data;
			}
		}
        
 		//  --------------------------------------------
        //   Return Data
        //  --------------------------------------------
 		
 		return $this->cached[$cache_name][$cache_hash];	
    }
    // END get_prefs
}
// END CLASS Freeform_data

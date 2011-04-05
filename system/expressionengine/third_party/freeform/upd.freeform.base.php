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
 * Freeform - Updater
 *
 * In charge of the install, uninstall, and updating of the module
 *
 * @package 	Solspace:Freeform
 * @author		Solspace DevTeam
 * @filesource 	./system/modules/freeform/upd.freeform.php
 */

if ( ! defined('APP_VER')) define('APP_VER', '2.0'); // EE 2.0's Wizard doesn't like CONSTANTs

if (APP_VER < 2.0)
{
	require_once PATH.'bridge/lib/addon_builder/module_builder.php';
}
else
{
	require_once PATH_THIRD . 'bridge/lib/addon_builder/module_builder.php';
}

class Freeform_updater_base extends Module_builder_bridge
{
    
    var $module_actions			= array();
    var $hooks					= array();
    var $freeform_notification 	= "
Someone has posted to Freeform. Here are the details:  
			 		
Entry Date: {entry_date}
{all_custom_fields}";

	// --------------------------------------------------------------------

	/**
	 * Contructor
	 *
	 * @access	public
	 * @return	null
	 */
    
	function Freeform_updater_base( )
    {
		if ( isset($GLOBALS['CI']) && get_class($GLOBALS['CI']) == 'Wizard')
    	{
    		return;
    	}

    	parent::Module_builder_bridge('freeform');
    	
		/** --------------------------------------------
        /**  Module Actions
        /** --------------------------------------------*/
        
        $this->module_actions = array(
			'insert_new_entry', 
			'retrieve_entries', 
			'delete_freeform_notification'
		);
    }
    /* END*/
	
	// --------------------------------------------------------------------

	/**
	 * Module Installer
	 *
	 * @access	public
	 * @return	bool
	 */

    function install()
    {
		$sql = array();
	
        // Already installed, let's not install again.
        if ($this->database_version() !== FALSE)
        {
        	return FALSE;
        }
        
        /** --------------------------------------------
        /**  Our Default Install
        /** --------------------------------------------*/
        
        if ($this->default_module_install() == FALSE)
        {
        	return FALSE;
        }
		
		/** --------------------------------------------
        /**  Module Install
        /** --------------------------------------------*/
        
        $sql[] = ee()->db->insert_string(
			'exp_modules', 
			array(	
				'module_name'		=> $this->class_name,
				'module_version'	=> FREEFORM_VERSION,
				'has_cp_backend'	=> 'y'
			)														
		);
		
				
		//---------------------------------------------------
		// exp_freeform_fields inserts
		//---------------------------------------------------
	
		$items = array(
		//--------------------------------------------------------	  
		// field_order, name, 			label, 			editable 	                                                                                                                           
		//--------------------------------------------------------
			array('1' 	, 'name'  		, 'Name'  	   	, 'n'), 
			array('2' 	, 'email'  		, 'Email'  	   	, 'n'), 
			array('3' 	, 'website'   	, 'Website'    	, 'n'), 
			array('4' 	, 'street1'   	, 'Street 1'   	, 'n'), 
			array('5' 	, 'street2'   	, 'Street 2'   	, 'n'), 
			array('6' 	, 'street3'   	, 'Street 3'   	, 'n'), 
			array('7' 	, 'city'  		, 'City'  	   	, 'n'), 
			array('8' 	, 'state'  		, 'State'  	   	, 'n'), 
			array('9' 	, 'country'   	, 'Country'    	, 'n'), 
			array('10'	, 'postalcode'	, 'Postal Code'	, 'n'), 
			array('11'	, 'phone1'  	, 'Phone 1'    	, 'n'), 
			array('12'	, 'phone2'  	, 'Phone 2'    	, 'n'), 
			array('13'	, 'fax'  	 	, 'Fax'  	   	, 'n')
		); 

		//add everything from items and merge in the blanks
		foreach($items as $item)
		{				
			$sql[] = ee()->db->insert_string(
				'exp_freeform_fields', 
				array(
					'field_order' 	=> $item[0], 
					'name' 			=> $item[1], 
					'label'	 		=> $item[2], 
					'editable' 		=> $item[3],
					'field_id'  	=> '', 
					'form_name' 	=> '', 
					'name_old'  	=> '', 
					'weblog_id' 	=> '',
					'author_id' 	=> '',
					'entry_date'	=> '',
					'edit_date' 	=> '',
					'status'    	=> ''  
				)
			);
		}
		
		
		//---------------------------------------------------
		// exp_freeform_templates inserts
		//---------------------------------------------------
				
		$sql[]	= ee()->db->insert_string(
			'exp_freeform_templates', 
			array(
				'template_id' 		=> '', 
				'template_name' 	=> 'default_template', 
				'template_label' 	=> 'Default Template', 
				'data_from_name' 	=> '', 
				'data_from_email' 	=> '', 
				'data_title' 		=> 'Someone has posted to Freeform', 
				'template_data' 	=> addslashes( trim( $this->freeform_notification ) )
			)
		); 
				
		//----------------------------------------------------------------
        foreach ($sql as $query)
        {
            ee()->db->query($query);
        }
        
        return TRUE;
    }
	/* END install() */
    

	// --------------------------------------------------------------------

	/**
	 * Module Uninstaller
	 *
	 * @access	public
	 * @return	bool
	 */

    function uninstall()
    {
        // Cannot uninstall what does not exist, right?
        if ($this->database_version() === FALSE)
        {
        	return FALSE;
        }
        
		/** --------------------------------------------
        /**  Default Module Uninstall
        /** --------------------------------------------*/
        
        if ($this->default_module_uninstall() == FALSE)
        {
        	return FALSE;
        }
        
        return TRUE;
    }
    /* END */


	// --------------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @access	public
	 * @return	bool
	 */
    
    function update()
    {
    	/** --------------------------------------------
        /**  ExpressionEngine 2.x attempts to do automatic updates.  
        /**		- Mitchell questioned clients/customers and discovered that the majority preferred to update
        /**		themselves, especially on higher traffic sites. So, we forbid EE 2.x from doing updates
        /**		unless it comes through our update form.
        /** --------------------------------------------*/
        
    	if ( ! isset($_POST['run_update']) OR $_POST['run_update'] != 'y')
    	{
    		return FALSE;
    	}
    	
    	// --------------------------------------------
        //  Default Module Update
        // --------------------------------------------
    
    	$this->default_module_update();
    	
    	$this->actions();

		//runs sql file and adds in missing tables
		$this->install_module_sql();

		$sql = array();	
		
		if ( ee()->db->table_exists('exp_freeform_entries') )
    	{
    		if ($this->version_compare($this->database_version(), '<', '2.7.1'))
    		{
    			$sql[] = "ALTER TABLE `exp_freeform_entries` 	ADD INDEX (`group_id`)";
    			$sql[] = "ALTER TABLE `exp_freeform_entries` 	ADD INDEX (`weblog_id`)";
    			$sql[] = "ALTER TABLE `exp_freeform_templates` 	ADD INDEX (`enable_template`)";
    		}
    	}

		//----------------------------------------------------------------
		// exp_freeform_entries: update
		//----------------------------------------------------------------

		$query		= ee()->db->query("DESCRIBE exp_freeform_entries");
	
		$ip_address		= FALSE;
		$template		= FALSE;
	
		foreach( $query->result_array() as $row )
		{		
			//	Change ip_address to varchar
			if ( $row['Field'] == 'ip_address' )
			{
				if ( $row['Type'] != 'varchar(16)' )
				{
					ee()->db->query( 
						"ALTER TABLE 	exp_freeform_entries 
						 MODIFY 		ip_address varchar(16) NOT NULL default '0'" 
					);
				}

				$ip_address	= TRUE;
			}
		
			//	Check for template column
			if ( $row['Field'] == 'template' )
			{
				$template	= TRUE;
			}
		}
	
		//	Add in missing columns
		if ( ! $ip_address )
		{
			ee()->db->query( 
				"ALTER TABLE 	exp_freeform_entries 
				 ADD 			ip_address varchar(16) NOT NULL default '0' 
				 AFTER 			author_id" 
			);
		}
	
		if ( ! $template )
		{
			ee()->db->query( 
				"ALTER TABLE 	exp_freeform_entries 
				 ADD 			template varchar(150) NOT NULL  
				 AFTER 			form_name" 
			);		
		}
		
		//----------------------------------------------------------------
		// exp_freeform_fields: update
		//----------------------------------------------------------------
	
		$query			= ee()->db->query("DESCRIBE exp_freeform_fields");
	
		$field_order	= FALSE;
		$field_type		= FALSE;
		$field_length	= FALSE;
	
		foreach( $query->result_array() as $row )
		{
			switch( $row['Field'] )
			{
				//	Check for field order	
				case 'field_order':
					$field_order	= TRUE;
					break;
		
				//	Check for field type
				case 'field_type':
					$field_type		= TRUE;
					break;
		
				//	Check for field length
				case 'field_length':
					$field_length	= TRUE;
					break;
			}
		}
	
		//	Add in missing columns
		if ( ! $field_length )
		{
			ee()->db->query( "ALTER TABLE 	exp_freeform_fields 
							  ADD 			field_length int(3) NOT NULL default '150' 
							  AFTER 		field_id" );
		}
	
		if ( ! $field_type )
		{
			ee()->db->query( "ALTER TABLE 	exp_freeform_fields 
							  ADD 			field_type varchar(50) NOT NULL default 'text' 
							  AFTER 		field_id" );
		}
	
		if ( ! $field_order )
		{
			ee()->db->query( "ALTER TABLE 	exp_freeform_fields 
							  ADD 			field_order int(10) unsigned NOT NULL default '0' 
							  AFTER 		field_id" );
		}
    

		//----------------------------------------------------------------
		// exp_freeform_templates: update
		//----------------------------------------------------------------
	
		$query				= ee()->db->query("DESCRIBE exp_freeform_templates");
	
		$template_label		= FALSE;
		$data_from_name		= FALSE;
		$data_from_email	= FALSE;
		$wordwrap			= FALSE;
		$html				= FALSE;
	
		foreach( $query->result_array() as $row )
		{
			//	Validate template_name
			if ( $row['Field'] == 'template_name' AND $row['Type'] != 'varchar(150)' )
			{
				ee()->db->query( "ALTER TABLE 	exp_freeform_templates 
									   MODIFY 		template_name varchar(150) NOT NULL default '0'" );
			}
		
			switch( $row['Field'] )
			{
				//	Check for from name field
				case 'data_from_name':
					$data_from_name		= TRUE;
					break;

				//	Check for from email field
				case 'data_from_email':
					$data_from_email 	= TRUE;
					break;

				//	Check for template label
				case 'template_label':
					$template_label		= TRUE;
					break;

				//	Check for wordwrap
				case 'wordwrap':
					$wordwrap			= TRUE;
					break;

				// check for html
				case 'html':
					$html				= TRUE;
					break;
			}
		}
	
	
		//----------------------------------------------------------------
		// exp_freeform_templates: add in missing columns from
		//----------------------------------------------------------------
		// these must be done BEFORE the sql loop so that all of the inserts work
	
		if ( ! $template_label )
		{
			ee()->db->query( "ALTER TABLE 	exp_freeform_templates 
								   ADD 			template_label varchar(150) NOT NULL 
								   AFTER 		template_name" );

			ee()->db->query( 
				ee()->db->update_string( 
					'exp_freeform_templates', 
					array('template_label' 	=> 'Default Template'), 
					array('template_name' 	=> 'default') 
				) 
			);
		}
	
		//	Add in missing columns
		if ( ! $html )
		{
			ee()->db->query( "ALTER TABLE 	exp_freeform_templates 
					   			   ADD 			html char(1) NOT NULL default 'n' 
					   			   AFTER 		wordwrap");
		}
	
		if ( ! $data_from_email )
		{
			ee()->db->query( "ALTER TABLE 	exp_freeform_templates 
								   ADD 			data_from_email varchar(200) NOT NULL 
								   AFTER 		template_label" );
		}
	
		if ( ! $data_from_name )
		{
			ee()->db->query( "ALTER TABLE 	exp_freeform_templates 
								   ADD 			data_from_name varchar(150) NOT NULL 
								   AFTER 		template_label" );
		}
	
		if ( ! $wordwrap )
		{
			ee()->db->query( "ALTER TABLE 	exp_freeform_templates 
								   ADD 			wordwrap char(1) NOT NULL default 'y' 
								   AFTER 		enable_template" );
		}
	
		//	Update default just in case
		ee()->db->query( 
			ee()->db->update_string( 
				'exp_freeform_templates', 
				array(
					'template_name' 	=> 'default_template', 
					'template_label' 	=> 'Default Template',
					'data_title'		=> 'Someone has posted to Freeform',
					'template_data'		=> addslashes( $this->freeform_notification )
				), 
				array('template_id' => '1') 
			) 
		);
			

		//----------------------------------------------------------------
		// run all stored queries
		//----------------------------------------------------------------
		if (! empty( $sql )) 
		{
			foreach ($sql as $query)
			{
		    	ee()->db->query($query);
			}
		}

        /** --------------------------------------------
        /**  Version Number Update - LAST!
        /** --------------------------------------------*/
    	
    	ee()->db->query(
			ee()->db->update_string(	
				'exp_modules',
				array('module_version'	=> FREEFORM_VERSION), 
				array('module_name'		=> $this->class_name)
			)
		);
    																	
    	return TRUE;
    }
    /* END update() */
    


}
/* END Class */
?>

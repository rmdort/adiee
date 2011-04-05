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
 * Freeform - Control Panel
 *
 * The Control Panel master class that handles all of the CP Requests and Displaying
 *
 * @package 	Solspace:Freeform
 * @author		Solspace DevTeam
 * @filesource 	./system/modules/freeform/mcp.freeform.php
 */

if (APP_VER < 2.0)
{
	require_once PATH.'bridge/lib/addon_builder/module_builder.php';
}
else
{
	require_once PATH_THIRD . 'bridge/lib/addon_builder/module_builder.php';
}

class Freeform_cp_base extends Module_builder_bridge {

    //---------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	(bool)		Enable calling of methods based on URI string
	 * @return	(string)
	 */
    
	function Freeform_cp_base( $switch = TRUE )
    {    	
        parent::Module_builder_bridge('freeform');
        
        if ((bool) $switch === FALSE) return; // Install or Uninstall Request
        
		//load helpers for everything
		ee()->load->helper(array('text', 'form'));

		//---------------------------------------------
        //  Module Menu Items
        //---------------------------------------------
        
        $menu	= array(										
			'module_entries'		=> array(	
				'link'  => $this->base . '&method=manage_entries',
				'title' => ee()->lang->line('entries')
			),
			'module_fields'			=> array(	
				'link'  => $this->base . '&method=manage_fields',
				'title' => ee()->lang->line('fields')
			),
			'module_templates'		=> array(	
				'link'  => $this->base . '&method=manage_templates',
				'title' => ee()->lang->line('templates')
			),
			'module_preferences'	=> array(	
				'link'  => $this->base . '&method=module_preferences',
				'title' => ee()->lang->line('preferences')
			),
			'module_documentation'	=> array(	
				'link'  => FREEFORM_DOCS_URL,
				'title' => ee()->lang->line('documentation').
						   '&nbsp;(<span style="font-size:11px">v.' . FREEFORM_VERSION . '</span>)'
			)
		);
        
        $this->cached_vars['module_menu'] 			= $menu;

		$this->cached_vars['js_magic_checkboxes']	= $this->js_magic_checkboxes();

		//--------------------------------------
		//  Module Installed and What Version?
		//--------------------------------------
			
		if ($this->database_version() == FALSE)
		{
			return;
		}
		elseif($this->version_compare($this->database_version(), '<', FREEFORM_VERSION))
		{
			if (APP_VER < 2.0)
			{
				if ($this->freeform_module_update() === FALSE)
				{
					return;
				}
			}
			else
			{
				// For EE 2.x, we need to redirect the request to Update Routine
				$_GET['method'] = 'freeform_module_update';
			}
		}
		else
		{
			//no updates needed? get prefs from new table
			$this->prefs = $this->data->get_prefs();
		}
        
        //--------------------------------------
		//  Request and View Builder
		//--------------------------------------
        
        if (APP_VER < 2.0 && $switch !== FALSE)
        {	
        	if (ee()->input->get('method') === FALSE)
        	{
        		$this->manage_entries();
        	}
        	elseif( ! method_exists($this, ee()->input->get('method')))
        	{
        		$this->add_crumb(ee()->lang->line('invalid_request'));
        		$this->cached_vars['error_message'] = ee()->lang->line('invalid_request');
        		
        		return $this->ee_cp_view('error_page.html');
        	}
        	else
        	{
        		$this->{ee()->input->get('method')}();
        	}
        }

    }
    // END Freeform_cp_base
	
	
	//---------------------------------------------------------------------

	/**
	 * Module's Main Homepage
	 * @access	public
	 * @param	(string) status message from action
	 * @return	(null)
	 */
    
	public function index($message='')
    {
		return $this->manage_entries($message);
	}
	// END index()


	//---------------------------------------------------------------------
	// begin cp pages
	//---------------------------------------------------------------------

	//---------------------------------------------------------------------

	/**
	 * manage_entries
	 * @access	public
	 * @param	(string) status message from action
	 * @return	(null)
	 */

	function manage_entries($message='')
    {
		//--------------------------------------
		//  double check message
		//--------------------------------------
        
		if ($message == '' && isset($_GET['msg']))
        {
        	$message = ee()->lang->line($_GET['msg']);
        }
        
        $this->cached_vars['message'] = $message;
        
		//--------------------------------------
		//  start vars
		//--------------------------------------
		
		$row_limit		= 50;
	    $paginate		= '';
	    $row_count		= 0;
		$entries		= array();
		$fields 		= array();
		$filled 		= array();

		//--------------------------------------
		//  Title and Left Crumb
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_entries'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight'] = 'module_entries';
		
		//--------------------------------------
		//  right crumb link
		//--------------------------------------
		
	    $form_name			= ( ee()->input->get_post('form_name') ) ? 
								AMP . 'form_name=' . ee()->input->get_post('form_name')	: '';
		
		$this->cached_vars['get_form_name']			= ee()->input->get_post('form_name');

	    $status				= ( ee()->input->get_post('status') ) ? 
								AMP . 'status=' . ee()->input->get_post('status') : '';

		$this->cached_vars['get_status']			= ee()->input->get_post('status');

	    $show_empties		= ( ee()->input->get_post('show_empties') ) ? 
								AMP . 'show_empties=' . ee()->input->get_post('show_empties') : '';
		
		$do_show_empties	= ee()->input->get_post('show_empties') == 'yes';
		
		$this->cached_vars['get_show_empties']		= ee()->input->get_post('show_empties');
		
		
		// build crumb
		$this->_build_right_link(
			array(
				'export_entries', 
				'export_entries_csv'
			),
			array(
				$this->base . 
			 	AMP . 'method=export_entries'  . 
			 	AMP . 'type=txt' . $form_name . 
				$status . $show_empties,
				
				$this->base . 
			 	AMP . 'method=export_entries_csv'  . 
			 	$form_name . $status . $show_empties
			)
		);
	
		//--------------------------------------
		//  form data
		//--------------------------------------
		
		$forms		= ee()->db->query(
			"SELECT DISTINCT 	form_name 
			 FROM 				exp_freeform_entries 
			 ORDER BY 			form_name ASC"
		);
		
		$this->cached_vars['forms']					= $forms->result();
		$this->cached_vars['manage_form_url']		= $this->base . 
													  AMP . 'method=manage_entries';
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		$language_variables = array(
			'manage_entries',		
			'filter_by_collection',
			'filter_by_status',
			'form_name',
			'all_entries',
			'status',
			'open',
			'closed',
			'show_empty_fields',
			'search',
			'yes',
			'no',
			'no_entries',
			'count',
			'delete',
			'edit',
			'attachments',
			'date',
			'template'
		);
		
		foreach($language_variables as $var)
		{
			$this->cached_vars['lang_' . $var] = ee()->lang->line($var);
		}

		$this->cached_vars['lang_form_name'] = str_replace(' ', '&nbsp;', ee()->lang->line('form_name'));

		//--------------------------------------
		//  entries data
		//--------------------------------------		
		
		//work with the mysql line a little backwards due to options.
		$sql		= "FROM exp_freeform_entries WHERE entry_id != '' ";

		if ( ee()->input->get_post('form_name') != '' )
		{
			$sql	.= "AND form_name = '" . ee()->db->escape_str(ee()->input->get_post('form_name')) . "' ";
		}

		if ( ee()->input->get_post('status') != '' )
		{
			$sql	.= "AND status = '" . ee()->db->escape_str(ee()->input->get_post('status')) . "' ";
		}

		//all entries (query still used later in code)
		$cquery		= ee()->db->query("SELECT COUNT(*) AS count " . $sql);
		
		
		//if there are no entries we can stop here
		if ( $cquery->row('count') == 0 )
		{
			$this->cached_vars['fields']		= $fields;
			$this->cached_vars['filled']		= $filled;
			$this->cached_vars['entries']		= $entries;
			$this->cached_vars['paginate']		= $paginate;
			$this->cached_vars['current_page']	= $this->view('entries.html', NULL, TRUE);
			return $this->ee_cp_view('index.html');
		}	

		//--------------------------------------
		//  if there ARE entries...
		//--------------------------------------
		
		//reuse sql trail before
		$sql  = "SELECT * ".$sql;
		$sql .= "ORDER BY entry_date DESC";

		//---------------------------------------------
	    //  Pagination
	    //---------------------------------------------

		if ( $cquery->row('count') > $row_limit )
		{
			//url to build pagination links from
			$pageination_base_url	= $this->base . AMP . 'method=manage_entries';

			if ( ee()->input->get_post('form_name') )
			{
				$pageination_base_url	.= AMP . 'form_name=' . ee()->input->get_post('form_name');
			}
			
			$row_count				= ( ! ee()->input->get_post('row')) ? 
										0 : ee()->input->get_post('row');

			ee()->load->library('pagination');

			$config['base_url'] 			= $pageination_base_url;
			$config['total_rows'] 			= $cquery->row('count');
			$config['per_page'] 			= $row_limit;
			$config['page_query_string'] 	= TRUE;
			$config['query_string_segment'] = 'row';

			ee()->pagination->initialize($config);

			$paginate 		= ee()->pagination->create_links();

			$sql			.= " LIMIT $row_count, $row_limit";
		}

		//run query
		$query = ee()->db->query($sql);

		//-----------------------------------------
		//	Attachments
		//-----------------------------------------

		$attachments_q	= ee()->db->query( 
			"SELECT entry_id 
			 FROM 	exp_freeform_attachments" 
		);

		$attachments	= array();

		foreach ( $attachments_q->result_array() as $row )
		{
			$attachments[]	= $row['entry_id'];
		}

		//-----------------------------------------
		//	Determine empties
		//-----------------------------------------

		if ( ee()->input->get_post('show_empties') != 'yes' )
		{
			foreach ( $query->result_array() as $row )
			{
				foreach ( $row as $key => $val )
				{
					if ( $val != '' )
					{
						$filled[$key]	= $val;
					}
				}
			}
		}
				
		$qfields = ee()->db->query(
			"SELECT 	name, label 
			 FROM 		exp_freeform_fields
			 ORDER BY 	field_order ASC"
		);
		
		//load fields for output
		if ( $qfields->num_rows() > 0 )
		{
			foreach ( $qfields->result_array() as $field )
			{
				if ( $do_show_empties OR isset( $filled[ $field['name'] ] ) )
				{
					$fields[] = $field['label'];
				}
			}
		}
		
		//--------------------------------------
		//  prep entries for output
		//--------------------------------------
		
		foreach ( $query->result_array() as $row )
		{
			$item = array();
		
			$item['count'] 			= ++$row_count;
			$item['id']				= $row['entry_id']; //for delete entry
			$item['edit_url']		= $this->base . AMP . 'method=edit_entry_form' . AMP .
										  'entry_id=' . $row['entry_id'] . $show_empties;
			
			if ( in_array( $item['id'], $attachments ) )
			{
				$item['attachment_url']	= $this->base . AMP . 'method=attachments' . AMP .
										  	  		'entry_id=' . $row['entry_id'] . $show_empties;
			}			
			$item['status']			= ucfirst( $row['status'] );
			$item['date']			= ee()->localize->set_human_time($row['entry_date']);
			$item['form_name']		= $row['form_name'];
			$item['template']		= $row['template'];
			
			$item['fields']			= array();	
			
			//load fields for output
			if ( $qfields->num_rows() > 0 )
			{
				foreach ( $qfields->result_array() as $field )
				{
					if ( $do_show_empties OR isset( $filled[ $field['name'] ] ) )
					{
						$item['fields'][] = $row[$field['name']];
					}
				}
			}
			
			//add the stack
			$entries[] = $item;
		}
		
		$this->cached_vars['delete_form_url']	= $this->base . AMP. 'method=delete_entry_confirm';
		
		//--------------------------------------
		//  final output data
		//--------------------------------------		
		
		$this->cached_vars['fields']			= $fields;
		$this->cached_vars['filled']			= $filled;
		$this->cached_vars['entries']			= $entries;
		$this->cached_vars['paginate']			= $paginate;
		
		//--------------------------------------
		//  menus and page content
		//--------------------------------------
		
		$this->cached_vars['current_page']		= $this->view('entries.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load Homepage
        //---------------------------------------------
        		
		return $this->ee_cp_view('index.html');
	}
	// END manage_entries
	
	
	//---------------------------------------------------------------------

	/**
	 * manage_fields
	 * @access	public
	 * @param	(string) status message from action
	 * @return	(null)
	 */
	
	function manage_fields($message='')
    {
        if ($message == '' && isset($_GET['msg']))
        {
        	$message = ee()->lang->line($_GET['msg']);
        }
        
        $this->cached_vars['message'] 				= $message;
        
		//--------------------------------------
		//  Title and Left Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_fields'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight'] = 'module_fields';
		
		//--------------------------------------
		//  right crumb
		//--------------------------------------
		
		$this->_build_right_link(
			array('create_new_field', 'edit_field_order'), 
			array($this->base . AMP . 'method=edit_field_form', $this->base . AMP . 'method=field_order_form')
		);
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		$this->cached_vars['lang_manage_fields']	= ee()->lang->line('manage_fields');
		$this->cached_vars['lang_delete']			= ee()->lang->line('delete');
		$this->cached_vars['lang_name']				= ee()->lang->line('name');	
		$this->cached_vars['lang_label']			= ee()->lang->line('label');
		$this->cached_vars['lang_locked']			= ee()->lang->line('locked');
		$this->cached_vars['lang_edit_field_order']	= ee()->lang->line('edit_field_order');
		$this->cached_vars['lang_no_fields']		= ee()->lang->line('no_fields');
		
		//--------------------------------------
		//  start vars
		//--------------------------------------
		
		$row_limit			= 50;
	    $paginate			= '';
	   	$row_count			= 0;
	   	$exclude_fields		= array();
		$fields				= array();
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		//what URL to post to
		$this->cached_vars['fields_form_url'] 		= $this->base . AMP . 'method=delete_field_confirm';
				
		//this will be used more than once
		$sql	= "SELECT 	* 
				   FROM 	exp_freeform_fields 
				   ORDER BY field_order ASC";

		//initial usage
		$query	= ee()->db->query($sql);
		
		//if there is nothing to show, end early
		if ( $query->num_rows() == 0 )
		{
			$this->cached_vars['fields']		= $fields;
			$this->cached_vars['paginate']		= $paginate;
			$this->cached_vars['current_page']	= $this->view('fields.html', NULL, TRUE);
			return $this->ee_cp_view('index.html');
		}
		
		// do we need pagination?
		if ( $query->num_rows() > $row_limit )
		{
			$row_count		= ( ! ee()->input->get_post('row') ) ? 
								0 : ee()->input->get_post('row');
			
			ee()->load->library('pagination');

			$config['base_url'] 			= $this->base . AMP . 'method=manage_fields';
			$config['total_rows'] 			= $query->num_rows();
			$config['per_page'] 			= $row_limit;
			$config['page_query_string'] 	= TRUE;
			$config['query_string_segment'] = 'row';

			ee()->pagination->initialize($config);

			$paginate 		= ee()->pagination->create_links();

			$sql			.= " LIMIT $row_count, $row_limit";

			$query			= ee()->db->query($sql);  
		}
						
		//prep fields				
		foreach($query->result_array() as $row)
		{			
			$item 				= array();
			
			$item['count']		= ++$row_count;
			$item['locked']		= in_array( $row['name'], $exclude_fields );
			$item['name']		= $row['name'];
			$item['label']		= $row['label'];
			$item['id']			= $row['field_id'];
			$item['edit_url']	= $this->base . AMP . 'method=edit_field_form' . 
												AMP . 'field_id=' . $row['field_id'];
			
			$fields[]			= $item;
		}
		
		$this->cached_vars['fields']				= $fields;
		$this->cached_vars['paginate']				= $paginate;
		
		//--------------------------------------
		//  menus and page content
		//--------------------------------------
		
		$this->cached_vars['current_page']	= $this->view('fields.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load Homepage
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
	}
	// END manage_fields
	
	
	//---------------------------------------------------------------------

	/**
	 * manage_templates
	 * @access	public
	 * @param	(string) status message from action
	 * @return	(null)
	 */
	
	function manage_templates($message='')
    {
        if ($message == '' && isset($_GET['msg']))
        {
        	$message = ee()->lang->line($_GET['msg']);
        }
        
        $this->cached_vars['message'] = $message;
        
		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_templates'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_templates';
		
		//--------------------------------------
		//  right crumb
		//--------------------------------------
		
		$this->_build_right_link('create_new_template', $this->base . AMP . 'method=edit_template_form');
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		$this->cached_vars['lang_manage_templates']	= ee()->lang->line('manage_templates');
		$this->cached_vars['lang_delete']			= ee()->lang->line('delete');
		$this->cached_vars['lang_name']				= ee()->lang->line('name');	
		$this->cached_vars['lang_label']			= ee()->lang->line('label');
		$this->cached_vars['lang_locked']			= ee()->lang->line('locked');
		$this->cached_vars['lang_no_templates']		= ee()->lang->line('no_templates');
				
		//--------------------------------------
		//  start vars
		//--------------------------------------
		
		$row_limit			= 50;
	    $paginate			= '';
		$row_count			= 0;
	   	$exclude_templates	= array('default_template');
	   	$templates			= array();

		//--------------------------------------
		//  form/link urls
		//--------------------------------------
		
		$this->cached_vars['form_url'] = $this->base . AMP . 'method=delete_template_confirm';
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		$sql	= "SELECT 		* 
			   	   FROM 		exp_freeform_templates 
			       ORDER BY 	template_name ASC";

		$query	= ee()->db->query($sql);

		//no wesults? Pompom, lets get out of hewe!
		if ( $query->num_rows() == 0 )
		{
			$this->cached_vars['templates']			= $templates;
			$this->cached_vars['paginate']			= $paginate;
			$this->cached_vars['current_page']		= $this->view('templates.html', NULL, TRUE);
			return $this->ee_cp_view('index.html');
			return;
		}
		
		// do we need pagination?
		if ( $query->num_rows() > $row_limit )
		{
			$row_count		= ( ! ee()->input->get_post('row') ) ? 
								0 : ee()->input->get_post('row');
			
			ee()->load->library('pagination');

			$config['base_url'] 			= $this->base . AMP . 'method=manage_templates';
			$config['total_rows'] 			= $query->num_rows();
			$config['per_page'] 			= $row_limit;
			$config['page_query_string'] 	= TRUE;
			$config['query_string_segment'] = 'row';

			ee()->pagination->initialize($config);

			$paginate 		= ee()->pagination->create_links();

			$sql			.= " LIMIT $row_count, $row_limit";

			$query			= ee()->db->query($sql);  
		}
		
		//load templates for views
		//pregens data for looping
		foreach($query->result_array() as $row)
		{
			$item 				= array();
			
			$item['count']		= ++$row_count;
			$item['locked']		= in_array( $row['template_name'], $exclude_templates );
			$item['name']		= $row['template_name'];
			$item['label']		= $row['template_label'];
			$item['id']			= $row['template_id'];
			$item['edit_url']	= $this->base . AMP . 'method=edit_template_form' . 
												AMP . 'template_id=' . $row['template_id'];
		
			$templates[]		= $item;
		}
		
		//---------------------------------------------
        //  Load page
        //---------------------------------------------		

		$this->cached_vars['templates']			= $templates;
		$this->cached_vars['paginate']			= $paginate;
		$this->cached_vars['current_page']		= $this->view('templates.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load wrapper
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
	}
	// END manage_templates
		

	//---------------------------------------------------------------------

	/**
	 * module_prefences
	 * @access	public
	 * @param	string
	 * @return	null
	 */
    
	public function module_preferences($message='')
    {
        if ($message == '' && isset($_GET['msg']))
        {
        	$message = ee()->lang->line($_GET['msg']);
        }
        
        $this->cached_vars['message'] = $message;
        
		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('preferences'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_preferences';
		
		//--------------------------------------
		//  subpage and highlight
		//--------------------------------------
		
		$pref_data	= array();

		//dynamically get prefs and lang so we can just add them to defaults
		foreach( $this->prefs as $key => $value )
		{
			$pref = array();
			
			$pref['name']		= $key;
			$pref['lang_desc']	= ee()->lang->line($key . '_desc');
			$pref['value']		= $value;
			
			$pref_data[]		= $pref;
		}
		
		$this->cached_vars['pref_data']				= $pref_data;
		
		$this->cached_vars['lang_save_preferences']	= ee()->lang->line('save_preferences');
		$this->cached_vars['lang_preference']		= ee()->lang->line('preference');
		$this->cached_vars['lang_preferences']		= ee()->lang->line('preferences');
		$this->cached_vars['lang_value']			= ee()->lang->line('value');
		
		$this->cached_vars['form_url']				= $this->base . AMP . 'method=save_preferences';
		
		$this->cached_vars['current_page']			= $this->view('preferences.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load Homepage
        //---------------------------------------------
        				
		return $this->ee_cp_view('index.html');
	}
	// END index()

	
	//---------------------------------------------------------------------

	/**
	 * attachments
	 * @access	public
	 * @param	(string) status message from action
	 * @return	(null)
	 */
	
	function attachments($message='')
    {
        if ($message == '' && isset($_GET['msg']))
        {
        	$message = ee()->lang->line($_GET['msg']);
        }
        
        $this->cached_vars['message'] = $message;        
		//--------------------------------------
		//  Title and Left Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_entries'), $this->base . AMP . 'method=manage_entries');
		$this->add_crumb(ee()->lang->line('attachments'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_entries';
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		$this->cached_vars['lang_attachments']		= ee()->lang->line('attachments');
		$this->cached_vars['lang_delete']			= ee()->lang->line('delete');
		$this->cached_vars['lang_name']				= ee()->lang->line('name');	
		$this->cached_vars['lang_label']			= ee()->lang->line('label');
		$this->cached_vars['lang_locked']			= ee()->lang->line('locked');
		$this->cached_vars['lang_filename']			= ee()->lang->line('filename');
		$this->cached_vars['lang_filesize']			= str_replace(' ', '&nbsp;', ee()->lang->line('filesize'));
		$this->cached_vars['lang_date']				= ee()->lang->line('date');
		$this->cached_vars['lang_emailed']			= ee()->lang->line('emailed');
		
		//--------------------------------------
		//  start vars
		//--------------------------------------
		
		$attachments	= array();
		$pref_id		= '';
		$row_count		= 0;
		$i				= 0;
			
		//--------------------------------------
		//  form/link urls
		//--------------------------------------
		
		$this->cached_vars['form_url'] 				= $this->base . AMP . 'method=delete_attachments_confirm';
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		//get attachments, or exit on error
		if ( ee()->input->get_post('entry_id') )
		{
			$sql	= "SELECT * 
					   FROM exp_freeform_attachments 
					   WHERE entry_id = '" . ee()->db->escape_str(
												ee()->input->get_post('entry_id') 
											 ) ."'";

			$query	= ee()->db->query($sql);
		}
		else
		{
			return $this->show_error('entry_not_found');
		}
		
		//get preferences
		$prefs		= ee()->db->query( 
			"SELECT 	p.* 
			 FROM 		exp_upload_prefs p 
			 LEFT JOIN 	exp_freeform_attachments a 
			 ON 		p.id = a.pref_id 
			 WHERE 		a.entry_id = '" . ee()->db->escape_str(
											ee()->input->get_post('entry_id')
										  ) . "'" 
		);
		
		if ( $prefs->num_rows() > 0 )
		{
			$result 		= $prefs->result_array(); 
			$this->upload 	= $result[0];
			//$this->upload	= (array) $prefs->row();
			$pref_id		= $prefs->row('id');
		}
		
		$this->cached_vars['pref_id']		= $pref_id;
				
		//if upload isn't empty
		$this->cached_vars['do_link']		= ( count( $this->upload ) > 0 );
		
		foreach($query->result_array() as $row)
		{
			$item 				= array();
			
			$item['id']			= $row['attachment_id'];
			
			$item['count']		= ++$row_count;
			$item['filename']	= $row['filename'].$row['extension'];
			
			if ($this->cached_vars['do_link'])
			{
				$item['download_url'] = $this->upload['url'].$item['filename'];
			}
			
			$item['filesize']	= ceil($row['filesize'])."KB";
			$item['date']		= ee()->localize->set_human_time( $row['entry_date'] );
			$item['emailed']	= ee()->lang->line( $row['emailed'] );
			
			$attachments[]		= $item;
		}
		
		$this->cached_vars['attachments']	= $attachments;
		
		//--------------------------------------
		//  menus and page content
		//--------------------------------------
		
		$this->cached_vars['current_page']	= $this->view('attachments.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load Homepage
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
	}
	// END attachments


	//---------------------------------------------------------------------

	/**
	 * delete_entry_confirm 
	 * @access	public
	 * @return	(null)
	 */

    function delete_entry_confirm()
    {

		//protection
        if ( ! ee()->input->post('toggle'))
        { 
            return $this->manage_entries();
        }

		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_entries'), $this->base . AMP . 'method=manage_entries');
		$this->add_crumb(ee()->lang->line('entry_delete_confirm'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_entries';
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		//this is set after data has been parsed
		//$this->cached_vars['lang_confirm_delete']	
		
		$this->cached_vars['lang_confirm_title']	= ee()->lang->line('entry_delete_confirm');
		$this->cached_vars['lang_no_undo']			= ee()->lang->line('action_can_not_be_undone');
		$this->cached_vars['lang_delete']			= ee()->lang->line('delete');
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		$hidden_inputs	= array();
		$i				= 0;
		
		//add in all deletes for forwarding to the real function
		foreach ( ee()->input->post('toggle') as $val )
        {        
            $hidden_inputs[] = array('name' => 'delete[]', 'value' => ee()->security->xss_clean($val));
            $i++;        
        }

		$this->cached_vars['hidden_inputs']			= $hidden_inputs;
		
		//can set now that we have a number
		//need to replace some values to make sense
		$this->cached_vars['lang_confirm_delete']	= str_replace(  
			array( '%i%', '%entry%' ), 
			array( $i   , ($i == 1) ? ee()->lang->line('entry') : ee()->lang->line('entries')), 
			ee()->lang->line('entry_delete_question')
		);
						
		//where to forward the data
		$this->cached_vars['form_url']				= $this->base . AMP . 'method=delete_entry'; 
		
		//---------------------------------------------
        //  Load page
        //---------------------------------------------		
		
		$this->cached_vars['current_page']			= $this->view('delete_confirm.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load wrapper
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
    }
    /* END delete_entry_confirm */


	//---------------------------------------------------------------------

	/**
	 * delete_attachments_confirm 
	 * @access	public
	 * @return	(null)
	 */

    function delete_attachments_confirm()
    {
    		//protection
        if ( ! ee()->input->post('toggle'))
        { 
            return $this->manage_entries();
        }

		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_entries'), $this->base . AMP . 'method=manage_entries');
		$this->add_crumb(ee()->lang->line('attachment_delete_confirm'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_entries';
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		//this is set after data has been parsed
		//$this->cached_vars['lang_confirm_delete']	
		
		$this->cached_vars['lang_confirm_title']	= ee()->lang->line('attachment_delete_confirm');
		$this->cached_vars['lang_no_undo']			= ee()->lang->line('action_can_not_be_undone');
		$this->cached_vars['lang_delete']			= ee()->lang->line('delete');
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		$hidden_inputs	= array();
		$i				= 0;
		
		//add in all deletes for forwarding to the real function
		foreach ( ee()->input->post('toggle') as $val )
        {        
            $hidden_inputs[] = array('name' => 'delete[]', 'value' => ee()->security->xss_clean($val));
            $i++;        
        }

		$this->cached_vars['hidden_inputs']			= $hidden_inputs;
		
		//can set now that we have a number
		//need to replace some values to make sense
		$this->cached_vars['lang_confirm_delete']	= str_replace(  
			array( '%i%', '%attachments%' ), 
			array( $i   , ($i == 1) ? ee()->lang->line('attachment') : ee()->lang->line('attachments')), 
			ee()->lang->line('attachment_delete_question')
		);
						
		//where to forward the data
		$this->cached_vars['form_url']				= $this->base . AMP . 'method=delete_attachments'; 
		
		//---------------------------------------------
        //  Load page
        //---------------------------------------------		
		
		$this->cached_vars['current_page']			= $this->view('delete_confirm.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load wrapper
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
    }
    // END delete_attachments_confirm()


	//---------------------------------------------------------------------

	/**
	 * delete_field_confirm 
	 * @access	public
	 * @return	(null)
	 */

    function delete_field_confirm()
    {
		//protection
        if ( ! ee()->input->post('toggle'))
        { 
            return $this->manage_entries();
        }

		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_fields'), $this->base . AMP . 'method=manage_fields');
		$this->add_crumb(ee()->lang->line('field_delete_confirm'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_fields';
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		//this is set after data has been parsed
		//$this->cached_vars['lang_confirm_delete']	
		
		$this->cached_vars['lang_confirm_title']	= ee()->lang->line('field_delete_confirm');
		$this->cached_vars['lang_no_undo']			= ee()->lang->line('action_can_not_be_undone');
		$this->cached_vars['lang_delete']			= ee()->lang->line('delete');
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		$hidden_inputs	= array();
		$i				= 0;
		
		//add in all deletes for forwarding to the real function
		foreach ( ee()->input->post('toggle') as $val )
        {        
            $hidden_inputs[] = array('name' => 'delete[]', 'value' => ee()->security->xss_clean($val));
            $i++;        
        }
		
		$this->cached_vars['hidden_inputs']			= $hidden_inputs;
		
		//can set now that we have a number
		//need to replace some values to make sense
		$this->cached_vars['lang_confirm_delete']	= str_replace(  
			array( '%i%', '%fields%' ), 
			array( $i   , ($i == 1) ? ee()->lang->line('field') : ee()->lang->line('fields')), 
			ee()->lang->line('field_delete_question')
		);
						
		//where to forward the data
		$this->cached_vars['form_url']				= $this->base . AMP . 'method=delete_field'; 
		
		//---------------------------------------------
        //  Load page
        //---------------------------------------------		
		
		$this->cached_vars['current_page']			= $this->view('delete_confirm.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load wrapper
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
    }
    /* END delete_field_confirm */

	//---------------------------------------------------------------------

	/**
	 * delete_template_confirm 
	 * @access	public
	 * @return	(null)
	 */  

    function delete_template_confirm()
    {
		//protection
        if ( ! ee()->input->post('toggle'))
        { 
            return $this->manage_entries();
        }

		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_templates'), $this->base . AMP . 'method=manage_templates');
		$this->add_crumb(ee()->lang->line('template_delete_confirm'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_templates';
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		//this is set after data has been parsed
		//$this->cached_vars['lang_confirm_delete']	
		
		$this->cached_vars['lang_confirm_title']	= ee()->lang->line('template_delete_confirm');
		$this->cached_vars['lang_no_undo']			= ee()->lang->line('action_can_not_be_undone');
		$this->cached_vars['lang_delete']			= ee()->lang->line('delete');
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		$hidden_inputs	= array();
		$i				= 0;
		
		//add in all deletes for forwarding to the real function
		foreach ( ee()->input->post('toggle') as $val )
        {        
            $hidden_inputs[] = array('name' => 'delete[]', 'value' => ee()->security->xss_clean($val));
            $i++;        
        }
		
		$this->cached_vars['hidden_inputs']			= $hidden_inputs;
		
		//can set now that we have a number
		//need to replace some values to make sense
		$this->cached_vars['lang_confirm_delete']	= str_replace(  
			array( '%i%', '%templates%' ), 
			array( $i   , ($i == 1) ? ee()->lang->line('template') : ee()->lang->line('templates')), 
			ee()->lang->line('template_delete_question')
		);
						
		//where to forward the data
		$this->cached_vars['form_url']				= $this->base . AMP . 'method=delete_template'; 
		
		//---------------------------------------------
        //  Load page
        //---------------------------------------------		
		
		$this->cached_vars['current_page']			= $this->view('delete_confirm.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load wrapper
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
    }
    // END delete_field_confirm


	//---------------------------------------------------------------------

	/**
	 * edit_entry_form 
	 * @access	public
	 * @return	(null)
	 */

	function edit_entry_form()
    {
		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_entries'), $this->base . AMP . 'method=manage_entries');
		$this->add_crumb(ee()->lang->line('edit_entry'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_entries';
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		$this->cached_vars['lang_edit_entry']		= ee()->lang->line('edit_entry');
		$this->cached_vars['lang_update']			= ee()->lang->line('update');
		$this->cached_vars['lang_name']				= ee()->lang->line('name');	
		$this->cached_vars['lang_label']			= ee()->lang->line('label');
		$this->cached_vars['lang_field']			= ee()->lang->line('field');	
		$this->cached_vars['lang_value']			= ee()->lang->line('value');
		$this->cached_vars['lang_locked']			= ee()->lang->line('locked');
		$this->cached_vars['lang_screen_name']		= ee()->lang->line('screen_name');
		$this->cached_vars['lang_group_title']		= ee()->lang->line('group_title');
		$this->cached_vars['lang_ip_address']		= ee()->lang->line('ip_address');
		$this->cached_vars['lang_entry_date']		= ee()->lang->line('entry_date');
		$this->cached_vars['lang_entry_id']			= ee()->lang->line('entry_id');
		$this->cached_vars['lang_edit_date']		= ee()->lang->line('edit_date');
		$this->cached_vars['lang_status']			= ee()->lang->line('status');
		$this->cached_vars['lang_open']				= ee()->lang->line('open');
		$this->cached_vars['lang_closed']			= ee()->lang->line('closed');
				
		//--------------------------------------
		//  start vars
		//--------------------------------------
		
		$fields			= array();
		$screen_name	= ee()->lang->line('guest');
		$entry_id		= '';
		$filled			= array();
		
		//--------------------------------------
		//  form/link urls
		//--------------------------------------
		
		$this->cached_vars['form_url'] = $this->base . AMP . 'method=edit_entry';
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		//main query
		if ( ee()->input->get_post('entry_id') )
		{
			$sql	= "SELECT 	* 
					   FROM 	exp_freeform_entries 
					   WHERE 	entry_id = '" . 
						ee()->db->escape_str(ee()->input->get_post('entry_id')) . "' 
					   LIMIT 	1";

			$query	= ee()->db->query($sql);
		}
		//if there is no entry_id, exit on error
		else
		{
			return $this->show_error('entry_not_found');
		}
		
		//-----------------------------------------
		//	Group title
		//-----------------------------------------
		
		$group				= ee()->db->query(
			"SELECT group_title 
			 FROM 	exp_member_groups 
			 WHERE 	group_id = '" . ee()->db->escape_str($query->row('group_id')) . "'"
		);

		$this->cached_vars['group_title']		= $group->row('group_title');
		
		//-----------------------------------------
		//	Author name
		//-----------------------------------------
		
		//get author or sent name, guest is default
		if ( $query->row('author_id') != '0' )
		{					
			$author			= ee()->db->query(
				"SELECT screen_name 
				 FROM 	exp_members 
				 WHERE 	member_id = '" . ee()->db->escape_str($query->row('author_id')) . "'"
			);

			$screen_name	= $author->row('screen_name');
		}
		
		$this->cached_vars['screen_name']		= $screen_name;		

		//-----------------------------------------
		//	Fields
		//-----------------------------------------
		
		$fields_q			= ee()->db->query(
			"SELECT 	* 
			 FROM 		exp_freeform_fields 
			 ORDER BY 	field_order"
		);

		//fill fields with items and pre-parse for views
		if ( $fields_q->num_rows() > 0 )
		{
			foreach ( $fields_q->result_array() as $row )
			{
				$item				= array();
				$item['label']		= $row['label'];
				$item['type']		= $row['field_type'];
				$item['length']		= $row['field_length'];
				$item['data']		= $query->row( $row['name'] );
				
				//prep/clean data for textarea output
				//if it is not a normal value input
				if ( $item['type'] != 'text')
				{
					 $item['data'] = form_prep(
					 	//removed because it was causing errors with foreign langs. Weird.
						//ascii_to_entities( $item['data'] )
						$item['data']
					 );
				}
				
				$fields[ $row['name'] ]				= $item;
			}
		}
		
		//db data
		$this->cached_vars['entry_id']			= $query->row('entry_id');
		$this->cached_vars['ip_address']		= $query->row('ip_address');
		$this->cached_vars['entry_date']		= ee()->localize->set_human_time( $query->row('entry_date') );
		$this->cached_vars['edit_date']			= ee()->localize->set_human_time( $query->row('edit_date') );
	
		//status of entry
		$this->cached_vars['status_open_selected']		= ( $query->row('status') == 'open' ) ? 
																'selected="selected"': '';
		$this->cached_vars['status_closed_selected']	= ( $query->row('status') == 'closed' ) ? 
																'selected="selected"': '';

		//-----------------------------------------
		//	Start fields
		//-----------------------------------------
	
		$this->cached_vars['show_empties']	= (ee()->input->get_post('show_empties') == 'yes');

		//set items in filled from actual entries in freeform
		if ( ! $this->cached_vars['show_empties'] )
		{
			$sql	= "SELECT 	* 
					   FROM 	exp_freeform_entries 
					   WHERE 	status = 'open'";

			if (  $query->row('form_name') AND $query->row('form_name') != '' )
			{
				$sql	.= " AND form_name = '" . $query->row('form_name') . "'";
			}

			$sub	= ee()->db->query( $sql );

			foreach ( $sub->result_array() as $row )
			{
				foreach ( $row as $key => $value )
				{
					if ( $value != '' )
					{
						$filled[$key]	= $value;
					}
				}
			}
		}

		$this->cached_vars['filled'] 			= $filled;

		$this->cached_vars['fields'] 			= $fields;

		//---------------------------------------------
        //  Load page
        //---------------------------------------------		

		$this->cached_vars['current_page']		= $this->view('edit_entry.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load wrapper
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
	}
	// END edit_entry
	
	
	//---------------------------------------------------------------------

	/**
	 * edit_field_form 
	 * @access	public
	 * @return	(null)
	 */

	function edit_field_form()
    {
    	//have to get first thing to allow for depending lang 
		$edit_field_mode	= ( ee()->input->get_post('field_id') ) ? 'edit_field': 'create_field';
		
		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_fields'), $this->base . AMP . 'method=manage_fields');
		$this->add_crumb(ee()->lang->line($edit_field_mode));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_fields';
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		$this->cached_vars['lang_edit_field']		= ee()->lang->line($edit_field_mode);
		$this->cached_vars['lang_update']			= ee()->lang->line( 
			( $edit_field_mode == 'edit_field' ) ? 'update' : 'create'
		);
		$this->cached_vars['lang_field']			= ee()->lang->line('field');	
		$this->cached_vars['lang_value']			= ee()->lang->line('value');
		$this->cached_vars['lang_field_name']		= ee()->lang->line('field_name');	
		$this->cached_vars['lang_field_name_info']	= ee()->lang->line('field_name_info');		
		$this->cached_vars['lang_field_label']		= ee()->lang->line('field_label');
		$this->cached_vars['lang_field_label_info']	= ee()->lang->line('field_label_info');
		$this->cached_vars['lang_field_type']		= ee()->lang->line('field_type');	
		$this->cached_vars['lang_field_length']		= ee()->lang->line('field_length');
		$this->cached_vars['lang_field_order']		= ee()->lang->line('field_order');
		$this->cached_vars['lang_text']				= ee()->lang->line('text');
		$this->cached_vars['lang_textarea']			= ee()->lang->line('textarea');
				
		//--------------------------------------
		//  start vars
		//--------------------------------------
		
		$field_id			= '';
        $name				= '';
        $label				= '';
        $field_order		= '';
        $field_type			= '';
        $field_length		= '';
		$selected_text		= '';
		$selected_textarea	= '';
				
		//--------------------------------------
		//  form/link urls
		//--------------------------------------
		
		$this->cached_vars['form_url'] = $this->base . AMP . 'method=edit_field';
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		//if there is a field id, get associated data
		if ( ee()->input->get_post('field_id') )
		{
			$sql	= "SELECT 	* 
					   FROM 	exp_freeform_fields 
					   WHERE 	field_id = '" . ee()->db->escape_str(
													ee()->input->get_post('field_id')
												) . "'";

			$query			= ee()->db->query($sql);

			$field_id		= $query->row('field_id');
			$name			= $query->row('name');
			$label			= $query->row('label');
			$field_order	= $query->row('field_order');
			$field_type		= $query->row('field_type');
			$field_length	= $query->row('field_length');
		}
		//else get the most recent and copy
		else
		{
			$query	= ee()->db->query( 
				"SELECT 	field_order, field_type 
				 FROM 		exp_freeform_fields 
				 ORDER BY 	field_order DESC 
				 LIMIT 		1" 
			);

			if ( $query->num_rows() > 0 )
			{
				$field_order	= $query->row('field_order') + 1;
			}
			else
			{
				$field_order	= '1';
			}

			$field_type		= $query->row('field_type');
			$field_length	= '150';
		}

		//check field type for default selected options
		if ( $field_type == 'text' )
		{
			$selected_text		= 'selected="selected"';
		}
		else
		{
			$selected_textarea	= 'selected="selected"';
		}

		// data->view
		$this->cached_vars['field_id']			= $field_id;
		$this->cached_vars['name']				= $name;
		$this->cached_vars['label']				= $label;
		$this->cached_vars['field_order']		= $field_order;
		$this->cached_vars['field_type']		= $field_type;
		$this->cached_vars['field_length']		= $field_length;
		$this->cached_vars['selected_text']		= $selected_text;
		$this->cached_vars['selected_textarea']	= $selected_textarea;

		//---------------------------------------------
        //  Load page
        //---------------------------------------------		

		$this->cached_vars['current_page']		= $this->view('edit_field.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load wrapper
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
	}
	// END edit_field_form()
	
	
	//---------------------------------------------------------------------

	/**
	 * edit_template_form 
	 * @access	public
	 * @return	(null)
	 */

	function edit_template_form()
    {		
		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_templates'), $this->base . AMP . 'method=manage_templates');
		$this->add_crumb(ee()->lang->line('templates'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_templates';
		
		//--------------------------------------
		//  lang
		//--------------------------------------

		$this->cached_vars['lang_edit_template']		= ee()->lang->line('edit_template');
		$this->cached_vars['lang_field']				= ee()->lang->line('field');	
		$this->cached_vars['lang_value']				= ee()->lang->line('value');
		$this->cached_vars['lang_templates']			= ee()->lang->line('templates');
		$this->cached_vars['lang_template_desc']		= ee()->lang->line('template_desc');
		$this->cached_vars['lang_template_name']		= ee()->lang->line('template_name');
		$this->cached_vars['lang_template_name_info']	= ee()->lang->line('template_name_info');
		$this->cached_vars['lang_template_label']		= ee()->lang->line('template_label');
		$this->cached_vars['lang_template_label_info']	= ee()->lang->line('template_label_info');
		$this->cached_vars['lang_update']				= ee()->lang->line('update');
		$this->cached_vars['lang_attachments_pair']		= ee()->lang->line('attachments_pair');
		$this->cached_vars['lang_available_variables']	= ee()->lang->line('available_variables');
		$this->cached_vars['lang_email_from_name']		= ee()->lang->line('email_from_name');
		$this->cached_vars['lang_email_from_email']		= ee()->lang->line('email_from_email');
		$this->cached_vars['lang_email_subject']		= ee()->lang->line('email_subject');
		$this->cached_vars['lang_email_message']		= ee()->lang->line('email_message');
		$this->cached_vars['lang_wordwrap']				= ee()->lang->line('wordwrap');	
		$this->cached_vars['lang_html']					= ee()->lang->line('html');
		$this->cached_vars['lang_yes']					= ee()->lang->line('yes');
		$this->cached_vars['lang_no']					= ee()->lang->line('no');				
				
		//--------------------------------------
		//  start vars
		//--------------------------------------
		
		$edit				= FALSE;
        $template_id		= '';
		$data_from_name		= '';
		$data_from_email	= '';
		$data_title			= '';
        $template_data		= $this->freeform_notification();
        $template_name		= '';
        $template_label		= '';
        $wordwrap			= '';
		$vstr 				= '';
		$highlight_vars		= array(
			'entry_date', 
			'all_custom_fields', 
			'your_custom_field', 
			'attachment_count', 
			'freeform_entry_id'
		);
		$checked			= 'checked="checked"';
		$wordwrap_checked_y	= '';
		$wordwrap_checked_n	= $checked;
		$html_checked_y		= '';
		$html_checked_n		= $checked;

		//--------------------------------------
		//  form/link urls
		//--------------------------------------
		
		$this->cached_vars['form_url'] 			= $this->base . AMP . 'method=edit_template';
		
		//--------------------------------------
		//  data
		//-------------------------------------- 
		
		$this->cached_vars['highlight_vars']	= $highlight_vars;
		
		//-----------------------------------------
		//	New or Edit?
		//-----------------------------------------

		if ( ee()->input->get_post('template_id') )
		{
			$edit	= TRUE;

			$query = ee()->db->query(
				"SELECT 	template_id, 		wordwrap, 		html, 
							template_name, 		template_label, data_from_name, 
							data_from_email, 	data_title, 	template_data, 
							enable_template 
				 FROM 		exp_freeform_templates 
				 WHERE 		template_id = '" . ee()->db->escape_str(
													ee()->input->get_post('template_id')
											   ) . "' 
				 LIMIT 		1"
			);

			if ($query->num_rows() == 0)
			{
				return;
			}

			$template_id		= $query->row('template_id');
			$template_name		= $query->row('template_name');
			$template_label		= $query->row('template_label');
			$data_from_name		= $query->row('data_from_name');
			$data_from_email	= $query->row('data_from_email');
			$data_title			= $query->row('data_title');
			$template_data		= $query->row('template_data');
			if ( $query->row('wordwrap') AND $query->row('wordwrap') == 'y' )
			{
				$wordwrap_checked_y	= $checked;
				$wordwrap_checked_n	= '';
			}
			if ( $query->row('html') AND $query->row('html') == 'y' )
			{
				$html_checked_y		= $checked;
				$html_checked_n		= '';
			}
		}
		//end if

		//final data
		$this->cached_vars['edit']					= $edit;			
 		$this->cached_vars['template_id']	        = $template_id;	
		$this->cached_vars['data_from_name']	    = $data_from_name;	
		$this->cached_vars['data_from_email']       = $data_from_email;
		$this->cached_vars['data_title']		    = $data_title;		
 		$this->cached_vars['template_data']	        = form_prep($template_data);
 		$this->cached_vars['template_name']	        = $template_name;	
 		$this->cached_vars['template_label']	    = $template_label;	
 		$this->cached_vars['wordwrap']		        = $wordwrap;		
		$this->cached_vars['vstr'] 			        = $vstr; 			
		$this->cached_vars['highlight_vars']        = $highlight_vars;
		$this->cached_vars['wordwrap_checked_y']    = $wordwrap_checked_y;
		$this->cached_vars['wordwrap_checked_n']    = $wordwrap_checked_n;
		$this->cached_vars['html_checked_y']	    = $html_checked_y;	
		$this->cached_vars['html_checked_n']	    = $html_checked_n;
		$this->cached_vars['form_submit']			= ee()->lang->line($edit ? 'update' : 'submit');
				
		//---------------------------------------------
        //  Load page
        //---------------------------------------------		

		$this->cached_vars['current_page']			= $this->view('edit_template.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load wrapper
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
	}
	// END edit_template_form()
	
	
	//---------------------------------------------------------------------

	/**
	 * field_order_form 
	 * @access	public
	 * @param	(string)	message from actions
	 * @return	(null)
	 */

	function field_order_form($message='')
    {
        if ($message == '' && isset($_GET['msg']))
        {
        	$message = ee()->lang->line($_GET['msg']);
        }
        
        $this->cached_vars['message'] = $message;
		
		//--------------------------------------
		//  Title and Crumbs
		//--------------------------------------
		
		$this->add_crumb(ee()->lang->line('manage_fields'), $this->base . AMP . 'method=manage_fields');
		$this->add_crumb(ee()->lang->line('field_order'));
		$this->build_crumbs();
		$this->cached_vars['module_menu_highlight']	= 'module_fields';
		
		//--------------------------------------
		//  lang
		//-------------------------------------- 

		$this->cached_vars['lang_edit_field_order']	= ee()->lang->line('edit_field_order');
		$this->cached_vars['lang_no_fields']		= ee()->lang->line('no_fields');
		$this->cached_vars['lang_submit']			= ee()->lang->line('submit');
		$this->cached_vars['lang_name']				= ee()->lang->line('name');
		$this->cached_vars['lang_order']			= ee()->lang->line('order');
				
		//--------------------------------------
		//  start vars
		//--------------------------------------
		
		$row_count	= 0;
		$fields		= array();
				
		//--------------------------------------
		//  form/link urls
		//--------------------------------------
		
		$this->cached_vars['form_url'] = $this->base . AMP . 'method=field_order';
		
		//--------------------------------------
		//  data
		//--------------------------------------
		
		$query	= ee()->db->query(
			"SELECT 	* 
			 FROM 		exp_freeform_fields 
			 ORDER BY 	field_order ASC"
		);
		
		foreach($query->result_array() as $row)
		{
			$item			= array();
			
			$item['count']	= ++$row_count;
			$item['name']	= $row['name'];
			$item['id']		= $row['field_id'];
			$item['order']	= $row['field_order'];
			
			$fields[]		= $item;
		}

		$this->cached_vars['fields']			= $fields;

		//---------------------------------------------
        //  Load page
        //---------------------------------------------		

		$this->cached_vars['current_page']		= $this->view('field_order.html', NULL, TRUE);
		
		//---------------------------------------------
        //  Load wrapper
        //---------------------------------------------

		return $this->ee_cp_view('index.html');
	}
	// END field_order_form()


	//---------------------------------------------------------------------
	// end cp pages
	//---------------------------------------------------------------------


	//---------------------------------------------------------------------

	/**
	 * Module Installation
	 *
	 * Due to the nature of the 1.x branch of ExpressionEngine, this function is always required.
	 * However, because of the large size of the module the actual code for installing, uninstalling,
	 * and upgrading is located in a separate file to make coding easier
	 *
	 * @access	public
	 * @return	bool
	 */

    function freeform_module_install()
    {
       require_once $this->addon_path . 'upd.freeform.php';
    	
    	$U = new Freeform_updater_base();
    	return $U->install();
    }
	/* END freeform_module_install() */    
    
	//---------------------------------------------------------------------

	/**
	 * Module Uninstallation
	 *
	 * Due to the nature of the 1.x branch of ExpressionEngine, this function is always required.
	 * However, because of the large size of the module the actual code for installing, uninstalling,
	 * and upgrading is located in a separate file to make coding easier
	 *
	 * @access	public
	 * @return	bool
	 */

    function freeform_module_deinstall()
    {
       require_once $this->addon_path . 'upd.freeform.php';
    	
    	$U = new Freeform_updater_base();
    	return $U->uninstall();
    }
    /* END freeform_module_deinstall() */


	//---------------------------------------------------------------------

	/**
	 * Module Upgrading
	 *
	 * This function is not required by the 1.x branch of ExpressionEngine by default.  However,
	 * as the install and deinstall ones are, we are just going to keep the habit and include it
	 * anyhow.
	 *		- Originally, the $current variable was going to be passed via parameter, but as there might
	 *		  be a further use for such a variable throughout the module at a later date we made it
	 *		  a class variable.
	 *		
	 *
	 * @access	public
	 * @return	bool
	 */
    
    function freeform_module_update()
    {
    	if ( ! isset($_POST['run_update']) OR $_POST['run_update'] != 'y')
    	{
    		$this->add_crumb(ee()->lang->line('update_freeform_module'));
    		$this->build_crumbs();
			$this->cached_vars['form_url'] = $this->base . '&msg=update_successful';
			return $this->ee_cp_view('update_module.html');
		}
		
    	require_once $this->addon_path.'upd.freeform.php';
    	
    	$U = new Freeform_updater_base();

    	if ($U->update() !== TRUE)
    	{
    		return $this->index(ee()->lang->line('update_failure'));
    	}
    	else
    	{
    		return $this->index(ee()->lang->line('update_successful'));
    	}
    }
    // END freeform_module_update() 


	//---------------------------------------------------------------------

	/**
	 * delete_entry 
	 * @access	public
	 * @return	(string) html of manage_entries with action message
	 */

    function delete_entry()
    {
        if ( ! ee()->input->post('delete'))
        {
            return $this->manage_entries();
        }

        $ids	= array();

        foreach (ee()->input->post('delete') as $val)
        {
			$ids[] = ee()->db->escape_str($val);       
        }

		//-----------------------------------------
		//	Get files
		//-----------------------------------------

		$filesq	= ee()->db->query( 
			"SELECT * 
			 FROM 	exp_freeform_attachments 
			 WHERE 	entry_id 
			 IN 	('" . implode( "','", $ids ) . "')" 
		);

		$files	= array();

		if ( $filesq->num_rows() > 0 )
		{
			foreach ( $filesq->result_array() as $row )
			{
				$files[ $row['entry_id'] ][]	= $row;
			}
		}

		//-----------------------------------------
		//	Delete loop
		//-----------------------------------------

        foreach ( $ids as $id )
        {
			ee()->db->query(
				"DELETE FROM 	exp_freeform_entries 
				 WHERE 			entry_id = '" . $id . "'"
			);
			
			ee()->db->query(
				"DELETE FROM 	exp_freeform_attachments 
				 WHERE 			entry_id = '" . $id . "'"
			);

			//-----------------------------------------
			//	Delete files
			//-----------------------------------------

			if ( isset( $files[ $id ] ) )
			{
				foreach ( $files[ $id ] as $row )
				{
					@unlink( $row['server_path'].$row['filename'].$row['extension'] );
				}
			}
        }

        $message = (count($ids) == 1) ? 
        				str_replace( '%i%', count($ids), ee()->lang->line('entry_deleted') ) : 
        				str_replace( '%i%', count($ids), ee()->lang->line('entries_deleted') );

        return $this->manage_entries($message);
    }
    //	End delete entry

	
	//---------------------------------------------------------------------

	/**
	 * edit_entry
	 * @access	public
	 * @return	(string) manage_entries html with action message
	 */

    function edit_entry()
    {
		//-----------------------------------------
		//	Filter
		//-----------------------------------------

		$_POST['edit_date']	= ee()->localize->now;

		unset( $_POST['submit'] );

		//-----------------------------------------
		//	Update
		//-----------------------------------------

		ee()->db->query( 
			ee()->db->update_string(
				'exp_freeform_entries', 
				ee()->security->xss_clean($_POST), 
				'entry_id=' . ee()->input->get_post('entry_id')
			) 
		);

		$message	= ee()->lang->line('entry_updated');

        return $this->manage_entries($message);
    }
    //	End edit entry 
	
	
	//---------------------------------------------------------------------

	/**
	 * delete_attachments 
	 * @access	public
	 * @return	(string) manage_entries html with action message
	 */

    function delete_attachments()
    {
        if ( ! ee()->input->post('delete'))
        {
            return $this->manage_entries();
        }

        $ids	= array();

        foreach ( ee()->input->post('delete') as $val)
        {        
			$ids[] = "attachment_id = '" . ee()->db->escape_str($val) . "'";      
        }

        $IDS	= implode(" OR ", $ids);

        $query	= ee()->db->query(
			"SELECT attachment_id, server_path, filename, extension 
			 FROM 	exp_freeform_attachments 
			 WHERE " . $IDS
		);

        foreach ( $query->result_array() as $row )
        {
			ee()->db->query(
				"DELETE FROM 	exp_freeform_attachments 
				 WHERE 			attachment_id = '" . ee()->db->escape_str($row['attachment_id']) . "'"
			);

			@unlink( 
				$this->_sanitze_filename(
					$row['server_path'].$row['filename'].$row['extension'] 
				)
			);
        }

        $message = ($query->num_rows() == 1) ? 
						str_replace( 
							'%i%', 
							$query->num_rows(), 
							ee()->lang->line('attachment_deleted') 
						) : 
						str_replace( 
							'%i%', 
							$query->num_rows(), 
							ee()->lang->line('attachments_deleted') 
						);

        return $this->manage_entries($message);
    }
    //	End delete attachments
	
	//---------------------------------------------------------------------

	/**
	 * export entries csv
	 * @access	public
	 * @param	(string) action message
	 * @return	(string) output from export entries
	 */
	
	public function export_entries_csv($message = '')
	{
		return $this->export_entries($message, 'csv');		
	}
	//end export_entries_csv

	//---------------------------------------------------------------------

	/**
	 * csv prep, removes newlines and adds commas where needed
	 * @access	private
	 * @param	(string) text
	 * @param	(bool) 	 is this a csv?
	 * @param	(bool) 	 add a comma?
	 * @return	(string) parsed output
	 */

	private function _csv_prep($item, $is_csv, $comma = TRUE)
	{
		return (($is_csv) ? 
				preg_replace('/\n|\r/s'," ", $item) . (($comma) ? ', ' : '') : 
				$item . "\t"
		);
	}
	
	//---------------------------------------------------------------------

	/**
	 * export entries 
	 * @access	public
	 * @param	(string) action message
	 * @param	(string) output type (text or csv for now)
	 * @return	(null)
	 */

    public function export_entries($message = '', $output_type = 'text')
    {
    	$row_count	= 0;

        //-----------------------------------------
        //	Build the output header
        //-----------------------------------------

        ob_start();

		// Assign the name of the file

		$now		= ee()->localize->set_localized_time();

		$name		= ee()->input->get_post('form_name') ? 
						ee()->input->get_post('form_name'): 'Freeform_Export';

		$filename	= $this->_sanitze_filename(str_replace(" ","_",$name) . '_' . 
					  date('Y', $now) . date('m', $now) . date('d', $now) . "_" . 
					  date('G', $now)."-".date('i', $now));
		
		switch ( ee()->input->get_post('type') )
		{
			case 'zip' :

						if ( ! @function_exists('gzcompress')) 
						{
			        		return $this->show_error('unsupported_compression');
						}

						$ext  = (($output_type == 'text') ? 'txt' : 'csv') . '.zip';
						$mime = 'application/x-zip';

				break;
			case 'gzip' :

						if ( ! @function_exists('gzencode')) 
						{
			        		return $this->show_error('unsupported_compression');
						}

						$ext  = (($output_type == 'text') ? 'txt' : 'csv') . '.gz';
						$mime = 'application/x-gzip';
				break;
			default     :

						$ext = (($output_type == 'text') ? 'txt' : 'csv');

						if ($output_type == 'csv')
						{
							$mime = 'text/csv';
						}
						else if (isset($_SERVER['HTTP_USER_AGENT']) AND (
								strstr($_SERVER['HTTP_USER_AGENT'], "MSIE") OR 
								strstr($_SERVER['HTTP_USER_AGENT'], "OPERA")
							)) 
						{
							$mime = 'application/octetstream';
						}
						else
						{
							$mime = 'application/octet-stream';
						}

				break;
		}

		if (isset($_SERVER['HTTP_USER_AGENT']) AND strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			header('Content-Type: '.$mime);
			header('Content-Disposition: inline; filename="'.$filename.'.'.$ext.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} 
		else 
		{
			header('Content-Type: '.$mime);
			header('Content-Disposition: attachment; filename="'.$filename.'.'.$ext.'"');
			header('Expires: 0');
			header('Pragma: no-cache');
		}

		//-----------------------------------------
		//	Query
		//-----------------------------------------

		$sql	= "SELECT 	* 
				   FROM 	exp_freeform_entries 
				   WHERE 	entry_id != ''";

		if ( ee()->input->get_post('form_name') != '' )
		{
			$sql	.= " AND form_name = '" . ee()->db->escape_str(ee()->input->get_post('form_name')) . "'";
		}

		if ( ee()->input->get_post('status') != '' )
		{
			$sql	.= " AND status = '" . ee()->db->escape_str(ee()->input->get_post('status')) . "'";
		}

		$sql	.= " ORDER BY entry_date ASC";

		$query	= ee()->db->query($sql);

		if ( $query->num_rows() == 0 )
		{
			echo ee()->lang->line('no_entries');
		}

		//-----------------------------------------
		//	Determine empties
		//-----------------------------------------

		if ( ee()->input->get_post('show_empties') != 'yes' )
		{
			foreach ( $query->result_array() as $row )
			{
				foreach ( $row as $key => $val )
				{
					if ( $val != '' )
					{
						$filled[$key]	= $val;
					}
				}
			}
		}

		//-----------------------------------------
		//	Create header
		//-----------------------------------------

		//is this csv?
		$csv = ($output_type === 'csv');

		$fields	= ee()->db->query(
			"SELECT 	name, label 
			 FROM 		exp_freeform_fields 
			 ORDER BY 	field_order ASC"
		);

		//cache
		$fnr = $fields->num_rows();

		echo $this->_csv_prep(ee()->lang->line('count'), $csv);

		echo $this->_csv_prep(ee()->lang->line('status'), $csv);

		echo $this->_csv_prep(ee()->lang->line('date'), $csv);

		echo $this->_csv_prep(ee()->lang->line('form_name'), $csv, FALSE);

		$items_added = array();

		if ( $fnr > 0 )
		{						
			foreach ( $fields->result_array() as $field )
			{
				if ( ee()->input->get_post('show_empties') == 'yes' OR 
				     isset( $filled[ $field['name'] ] ) )
				{
					$items_added[] = $field['label'];
				}
			}
		}

		//have to do things out of order so csv can get its commas correct
		if ( ! empty($items_added))
		{
			//because the last static item didnt add a comma
			if ($csv) echo ',';
			
			for ($i = 0, $l = count($items_added); $i < $l; $i++)
			{
				echo $this->_csv_prep($items_added[$i], $csv, ($i < ($l -1)));
			}
		}

		//-----------------------------------------
		//	Create body         
		//-----------------------------------------

		foreach ( $query->result_array() as $row )
		{			
			echo "\n";

			echo $this->_csv_prep($row_count, $csv);

			echo $this->_csv_prep(ucfirst( $row['status'] ), $csv);

			echo $this->_csv_prep(ee()->localize->set_human_time( $row['entry_date'] ), $csv);

			//third arg, if this is last, no comma for csv
			echo $this->_csv_prep($row['form_name'], $csv, FALSE);

			$items_added = array();

			if ( $fnr > 0 )
			{				
				foreach ( $fields->result_array() as $field )
				{
					if ( ee()->input->get_post('show_empties') == 'yes' OR 
					     isset( $filled[ $field['name'] ] ) )
					{
						$items_added[] = $row[ $field['name'] ];
					}
				}
			}
			
			
			//have to do things out of order so csv can get its commas correct
			if ( ! empty($items_added))
			{
				//because the last static item didnt add a comma
				if ($csv) echo ',';

				for ($i = 0, $l = count($items_added); $i < $l; $i++)
				{
					echo $this->_csv_prep($items_added[$i], $csv, ($i < ($l -1)));
				}
			}

			$row_count++;
		}

		//-----------------------------------------
		//	Return the finalized output
		//-----------------------------------------
        $buffer = ob_get_contents();

        ob_end_clean();

        echo $buffer;

        exit;
	}

	//	End export entries
	
	
	//---------------------------------------------------------------------

	/**
	 * delete_field 
	 * @access	public
	 * @return	(string) manage_fields html with action message
	 */

    function delete_field()
    {
        if ( ! ee()->input->post('delete'))
        {
            return $this->manage_fields();
        }

        $ids	= array();

        foreach (ee()->input->post('delete') as $val)
        {        
			$ids[] = "field_id = '" . ee()->db->escape_str($val) . "'";       
        }

        $IDS	= implode(" OR ", $ids);

        $query	= ee()->db->query(
			"SELECT field_id, name 
			 FROM 	exp_freeform_fields 
			 WHERE  " . $IDS);

        foreach ( $query->result_array() as $row )
        {
			ee()->db->query(
				"DELETE FROM 	exp_freeform_fields 
				 WHERE 			field_id = '" . ee()->db->escape_str($row['field_id']) . "'"
			);

			ee()->db->query(
				"ALTER TABLE exp_freeform_entries 
				 DROP `" . ee()->db->escape_str($row['name']) . "`"
			);
        }

        $message = ($query->num_rows() == 1) ? 
						str_replace( 
							'%i%', 
							$query->num_rows(), 
							ee()->lang->line('field_deleted') 
						) : 
						str_replace( 
							'%i%', 
							$query->num_rows(), 
							ee()->lang->line('fields_deleted') 
						);

        return $this->manage_fields($message);
    }

    //	End delete field
	
	
	//---------------------------------------------------------------------

	/**
	 * delete_entry_confirm 
	 * @access	public
	 * @return	(string) manage_fields html with action message
	 */  

    function edit_field()
    {
        $update	= FALSE;

		//-----------------------------------------
		//	Validate
		//-----------------------------------------

        if ( ! ee()->input->post('name') )
        {
			return $this->show_error('field_name_required');
        }

        if ( stristr( ee()->input->post('name'), " " ) )
        {
			return $this->show_error('no_spaces_allowed');
        }

        if ( stristr( ee()->input->post('name'), "-" ) )
        {
			return $this->show_error('no_dashes_allowed');
        }

        if ( ! ee()->input->post('label') )
        {
			return $this->show_error('field_label_required');
        }

        //	Check for duplicate
        if ( ! ee()->input->get_post('field_id') )
        {
			$query	= ee()->db->query(
				"SELECT COUNT(*) AS count 
				 FROM 	exp_freeform_fields 
				 WHERE 	name = '" . ee()->db->escape_str(ee()->input->post('name')) . "'"
			);

			if ( $query->row('count') > 0 )
			{
				return $this->show_error( 
					str_replace( 
						'%name%', 
						ee()->input->post('name'), 
						ee()->lang->line('field_name_exists') 
					), 
					FALSE 
				);
			}
		}

		//	Check for prohibited names
		$exclude	= array(
			'entry_id', 
			'group_id', 
			'weblog_id', 
			'author_id', 
			'ip_address', 
			'form_name', 
			'template', 
			'entry_date', 
			'edit_date', 
			'status'
		);

		if ( in_array( strtolower( ee()->input->post('name') ), $exclude ) )
		{
			return $this->show_error( 
				str_replace( 
					'%name%', 
					ee()->input->post('name'), 
					ee()->lang->line('reserved_field_name') 
				), 
				FALSE
			);
		}

        if ( ! is_numeric( ee()->input->post('field_length') ) )
        {
			return $this->show_error('numeric_field_length_required');
        }

        if ( ! is_numeric( ee()->input->post('field_order') ) )
        {
			return $this->show_error('numeric_field_order_required');
        }

		//-----------------------------------------
		//	Set field type
		//-----------------------------------------

        if ( ee()->input->get_post('field_type') == 'text' )
        {
			$field_type	= "varchar(".ee()->input->get_post('field_length').")";
        }
        else
        {
			$field_type	= "text";
        }

		//-----------------------------------------
		//	Filter post
		//-----------------------------------------

		//have to remove XID for non-secure
		if ($this->check_no(ee()->config->item('secure_forms')))
		{
			unset($_POST['XID']);
		}

		//-----------------------------------------
		//	Update or Create?
		//-----------------------------------------

		if ( ee()->input->post('field_id') != '' )
		{
			//	Get old name
			$query	= ee()->db->query(
				"SELECT * 
				 FROM 	exp_freeform_fields 
				 WHERE 	field_id = '" . ee()->db->escape_str(ee()->input->get_post('field_id')) . "'"
			);

			//only put items in the db that belong there
			$update_columns = array();
			foreach($query->row() as $key => $value)
			{
				if (array_key_exists($key, $_POST))
				{
					//NEVER TRUST AN ELF... er, USER!
					$update_columns[$key] = ee()->security->xss_clean($_POST[$key]);
				}
			}
						
			ee()->db->query( 
				ee()->db->update_string(
					'exp_freeform_fields', 
					$update_columns, 
					'field_id=' . ee()->input->get_post('field_id')
				) 
			);

			ee()->db->query( 
				"ALTER TABLE exp_freeform_entries 
				 CHANGE `" . ee()->db->escape_str($query->row('name')) . "` `" . 
					ee()->db->escape_str( 
						ee()->input->get_post('name') 
					) . "` " . ee()->db->escape_str($field_type) .
				 " NOT NULL" 
			);

			$message	= ee()->lang->line('field_updated');
		}
		else
		{		
			
			unset($_POST['submit']);
							
			ee()->db->query( 
				ee()->db->insert_string(
					'exp_freeform_fields', 
					 ee()->db->escape_str(ee()->security->xss_clean($_POST))
				) 
			);

			ee()->db->query(
				"ALTER TABLE exp_freeform_entries 
				 ADD `" . ee()->db->escape_str( 
					ee()->input->get_post('name') 
				) . "` " . ee()->db->escape_str($field_type) . 
				" NOT NULL" 
			);

			$message	= ee()->lang->line('field_created');
		}		

        return $this->manage_fields($message);
    }

    //	End edit field
	
	
	//---------------------------------------------------------------------

	/**
	 * field_order 
	 * @access	public
	 * @return	(null)
	 */

    function field_order()
    {
        if ( ! ee()->input->post('field_id'))
        {
            return $this->manage_fields();
        }

        foreach ( ee()->input->post('field_id') as $key => $val )
        {
        	ee()->db->query( 
				ee()->db->update_string( 
					'exp_freeform_fields', 
					array(
						'field_order' => $val
					), 
					array( 
						'field_id' => $key 
					) 
				) 
			);
        }

        $message = ee()->lang->line('fields_updated');

        return $this->field_order_form($message);
    }

    //	End field order
	
	
	//---------------------------------------------------------------------

	/**
	 * edit_template 
	 * @access	public
	 * @return	(string) manage_templates html with action message
	 */

    function edit_template()
    {
		//-----------------------------------------
		//	Update Template
		//-----------------------------------------

		if ( ! isset($_POST['template_data']) 	|| 
			 ! isset($_POST['template_id']) 	|| 
			 ! isset($_POST['template_name']) 	|| 
			 ! isset($_POST['template_label']) 	)
		{
			return FALSE;
		}

		//-----------------------------------------
		//	New or Edit?
		//-----------------------------------------

		$data['wordwrap']			= ee()->input->post('wordwrap');
		$data['html']				= ee()->input->post('html');
		$data['template_name']		= ee()->input->post('template_name');
		$data['template_label']		= ee()->input->post('template_label');
		$data['data_from_name']		= ee()->input->post('data_from_name');
		$data['data_from_email']	= ee()->input->post('data_from_email');
		$data['data_title']			= ee()->input->post('data_title');
		$data['template_data']		= ee()->input->post('template_data');

		if ( ee()->input->post('template_id') == '' )
		{
			//-----------------------------------------
			//	Test for unique template name
			//-----------------------------------------

			$query	= ee()->db->query(
				"SELECT COUNT(*) AS count 
				 FROM exp_freeform_templates 
				 WHERE template_name = '" . ee()->db->escape_str(ee()->input->post('template_name')) . "' 
				 LIMIT 1"
			);

			if ( $query->row('count') > 0 )
			{
				return $this->show_error('template_name_exists');
			}

			ee()->db->query( ee()->db->insert_string('exp_freeform_templates', $data ) );

			$success	= ee()->lang->line('template_created_successfully');
		}
		else
		{
			ee()->db->query( 
				ee()->db->update_string( 
					'exp_freeform_templates', 
					$data, 
					array( 
						'template_id' => ee()->input->get_post('template_id') 
					) 
				) 
			);

			$success	= ee()->lang->line('template_update_successful');
		}

        return $this->manage_templates( $success );
    }

    //	End edit template 

	
	//---------------------------------------------------------------------

	/**
	 * delete_template 
	 * @access	public
	 * @return	(string) manage_template html with action message
	 */

    function delete_template()
    {
        if ( ! ee()->input->post('delete'))
        {
            return $this->manage_templates();
        }

        $ids	= array();

        foreach (ee()->input->post('delete') as $val)
        {
			$ids[] = "template_id = '" . ee()->db->escape_str($val) . "'";       
        }

        $IDS	= implode(" OR ", $ids);

        $query	= ee()->db->query(
			"SELECT template_id 
			 FROM 	exp_freeform_templates 
			 WHERE " . $IDS);

        foreach ( $query->result_array() as $row )
        {
			ee()->db->query(
				"DELETE FROM 	exp_freeform_templates 
				 WHERE 			template_id = '" . ee()->db->escape_str($row['template_id']) . "'"
			);
        }

        $message = ($query->num_rows() == 1) ? 
						str_replace( 
							'%i%', 
							$query->num_rows(), 
							ee()->lang->line('template_deleted') 
						) : 
						str_replace( 
							'%i%', 
							$query->num_rows(), 
							ee()->lang->line('templates_deleted') 
						);

        return $this->manage_templates($message);
    }
    //	End delete field 


	// --------------------------------------------------------------------

	/**
	 * Saves the Preferences
	 * 
	 * @access	public
	 * @return	null
	 */
    
	public function save_preferences()
    {
		//defaults are in data.freeform.EXT
        $prefs_set = array();
		
		//check post input for all existing prefs and default if not present
		foreach( $this->prefs as $key => $value)
		{
			$prefs_set[$key] = (ee()->input->post($key) !== FALSE AND ee()->input->post($key) !== '') ? 
									ee()->security->xss_clean(ee()->input->post($key)) : $value;
		}
        
        //send all prefs to DB
        $this->data->set_prefs($prefs_set);
        
        // ----------------------------------
        //  Redirect to Homepage with Message
        // ----------------------------------
        
        ee()->functions->redirect($this->base . '&method=module_preferences&msg=preferences_updated');
        exit;
		
	}
	// END save_preferences

		
	//---------------------------------------------------------------------

	/**
	 * freeform_notification 
	 * @access	private
	 * @return	(string) default freeform notification form
	 */

	private function freeform_notification()
	{
		return 	implode("\n",
			array(
				"Someone has posted to Freeform.", 
		    	"Here are the details:", 
				"",
				"Entry Date: {entry_date}",
				"{all_custom_fields}"
			)
		);
	}
	//END freeform_notification 
	
	
	//---------------------------------------------------------------------

	/**
	 * show_error
	 * @access	public
	 * @param	(string) error string
	 * @param	(bool) 	 is the string a lang pointer?
	 * @return	(string) returns html string of error page
	 */

	function show_error($str, $do_lang = TRUE)
	{
		$this->cached_vars['error_message'] = $do_lang ? ee()->lang->line($str) : $str;
		return $this->ee_cp_view('error_page.html');
	}
	//END show_error
	
	
	//---------------------------------------------------------------------

	/**
	 * _sanitize_filename 
	 * @access	private
	 * @param	(string) filename to be cleaned
	 * @return	(string) sanitized filename
	 */
	
	private function _sanitze_filename($filename)
	{
		return (APP_VER < 2.0) ? 
			ee()->input->filename_security($filename) : 
			ee()->security->sanitize_filename($filename);
	}
	//END _sanitize_filename
	
	
	//---------------------------------------------------------------------

	/**
	 * _build_right_link 
	 * @access	public
	 * @param	(string)	lang string
	 * @param	(string)	html link for right link
	 * @return	(null)
	 */
	
	function _build_right_link($lang_line, $link)
	{	
		$msgs 		= array();
		$links 		= array();
		$ee2_links 	= array();
		
		if (is_array($lang_line))
		{
			for ($i = 0, $l= count($lang_line); $i < $l; $i++)
			{
				if (APP_VER < 2.0)
				{
					$msgs[$i]	= ee()->lang->line($lang_line[$i]);
					$links[$i]	= $link[$i];
				}
				else
				{
					$ee2_links[$lang_line[$i]] = $link[$i];
				}
			} 
		}
		else
		{
			if (APP_VER < 2.0)
			{
				$msgs[]		= ee()->lang->line($lang_line);
				$links[]	= $link;
			}
			else
			{
				$ee2_links[$lang_line] = $link;
			}
		}
					
		if (APP_VER < 2.0)
		{
			$this->cached_vars['right_crumb_msg']		= $msgs;
			$this->cached_vars['right_crumb_link'] 		= $links;
		}
		else
		{
			ee()->cp->set_right_nav($ee2_links);
		}
	}
}

// END CLASS Freeform

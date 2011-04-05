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
 * Freeform - User Side
 *
 * @package 	Solspace:Freeform
 * @author		Solspace DevTeam
 * @filesource 	./system/modules/freeform/mod.freeform.php
 */

if (APP_VER < 2.0)
{
	require_once PATH.'bridge/lib/addon_builder/module_builder.php';
}
else
{
	require_once PATH_THIRD . 'bridge/lib/addon_builder/module_builder.php';
}

class Freeform extends Module_builder_bridge 
{

	var $return_data			= '';
	                    		
	var $disabled				= FALSE;
                        		
	var $UP;            		
	                    		
	var $dynamic				= TRUE;
	var $multipart				= FALSE;
	                    		
	var $params_id				= 0;
	var $entry_id				= 0;
	var $upload_limit			= 3;
	                    		
	var $params_tbl				= 'exp_freeform_params';
	                    		
	var $params					= array();
	var $data					= array();
	var $upload					= array();
	var $attachments			= array();
	
	var $prefs					= array(); 
	
	var $upload_config			= array();

    // --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	null
	 */
	 
	function Freeform()
	{	
		parent::Module_builder_bridge('freeform');
		
		//load helpers for everything
		ee()->load->helper(array('text', 'form', 'url', 'security', 'string'));
        ee()->load->library('email');

        // -------------------------------------
		//  Module Installed and Up to Date?
		// -------------------------------------
				
		if ($this->database_version() == FALSE OR 
			$this->version_compare($this->database_version(), '<', FREEFORM_VERSION))
		{
			$this->disabled = TRUE;
			
			trigger_error(ee()->lang->line('freeform_module_disabled'), E_USER_NOTICE);
		}
		
		//get module preferences
		$this->prefs = $this->data->get_prefs();
	}
	// END Freeform() 

	
    // --------------------------------------------------------------------

	/**
	 * form submission
	 *
	 * @access	public
	 * @return	output
	 */
    
    function form()
    {
        $this->params['require_captcha']		= 'no';
    
		//	----------------------------------------
        //	Grab our tag data
		//	----------------------------------------
        
        $tagdata								= ee()->TMPL->tagdata;
        		
		//	----------------------------------------
		//	Set form name
		//	----------------------------------------
		
		$this->params['form_name']				= ( ee()->TMPL->fetch_param('form_name') !== FALSE 	AND 
													ee()->TMPL->fetch_param('form_name') !=  '' 	  	) ?
														ee()->TMPL->fetch_param('form_name') : 'freeform_form';
        		
		//	----------------------------------------
		//	Allow form name to be overridden by 'collection'
		//	----------------------------------------
		
		$this->params['form_name']				= ( ee()->TMPL->fetch_param('collection') !== FALSE 	AND 
													ee()->TMPL->fetch_param('collection') !=  '' 	  	) ?
														ee()->TMPL->fetch_param('collection') : $this->params['form_name'];
        		
		//	----------------------------------------
		//	Do we require IP address?
		//	----------------------------------------
		
		$this->params['require_ip']				= (ee()->TMPL->fetch_param('require_ip')) ? 
										   				ee()->TMPL->fetch_param('require_ip') : '';
        		
		//	----------------------------------------
		//	Are we establishing any required fields?
		//	----------------------------------------
		
		$this->params['ee_required']			= (ee()->TMPL->fetch_param('required')) ? 
														ee()->TMPL->fetch_param('required') : '' ;
        		
		//	----------------------------------------
		//	Are we notifying anyone?
		//	----------------------------------------
		
		$this->params['ee_notify']				= (ee()->TMPL->fetch_param('notify')) ? 
														ee()->TMPL->fetch_param('notify') : '' ;
														
														
        // ----------------------------------------------------------------------------------------
		//  Start recipients
		// ----------------------------------------------------------------------------------------
		
		//	----------------------------------------
		//	Do we allow dynamic tos?	
		//	----------------------------------------
		
		$this->params['recipients']				= ( $this->check_yes(ee()->TMPL->fetch_param('recipients')) ) ? 'y': 'n';
				
		$this->params['recipient_limit']		= ( ee()->TMPL->fetch_param('recipient_limit') !== FALSE AND 
													is_numeric( ee()->TMPL->fetch_param('recipient_limit') ) === TRUE) ?
													 	ee()->TMPL->fetch_param('recipient_limit') : 
																		$this->prefs['max_user_recipients'];

		//	----------------------------------------
		//	static recipients?
		//	----------------------------------------
																		
		$this->params['static_recipients']		= ( 
			! in_array(ee()->TMPL->fetch_param('recipient1'), array(FALSE, '')) 
		);
				
		//preload list with usable info if so
		$this->params['static_recipients_list'] 	= array();
		
		if ( $this->params['static_recipients'] )
		{
			$i = 1;
			
			while ( ! in_array(ee()->TMPL->fetch_param('recipient' . $i), array(FALSE, '')) )
			{
				$recipient = explode('|', ee()->TMPL->fetch_param('recipient' . $i));

				//has a name?
				if ( count($recipient) > 1)
				{		
					$recipient_name 	= trim($recipient[0]);
					$recipient_email 	= trim($recipient[1]);
				}
				//no name, we assume its just an email 
				//(though, this makes little sense, it needs a name to be useful)
				else
				{
					$recipient_name 	= '';
					$recipient_email 	= trim($recipient[0]);
				}
				
				//add to list
				$this->params['static_recipients_list'][$i] = array(
					'name' 	=> $recipient_name, 
					'email' => $recipient_email,
					'key'	=> uniqid()
				);
				
				$i++;
			}
		}

		//	----------------------------------------
		//	User recipient email template
		//	----------------------------------------

		$this->params['recipient_template']	= (ee()->TMPL->fetch_param('recipient_template')) ? 
														ee()->TMPL->fetch_param('recipient_template') : 'default_template';
														        		        		
		//	----------------------------------------
		//	Discard field contents?
		//	----------------------------------------
		
		$this->params['discard_field']			= (ee()->TMPL->fetch_param('discard_field')) ?
		 											ee()->TMPL->fetch_param('discard_field'): '';

        // ----------------------------------------------------------------------------------------
		//  End recipients
		// ----------------------------------------------------------------------------------------

		//	----------------------------------------
		//	Send attachments?
		//	----------------------------------------
		
		$this->params['send_attachment']		= (ee()->TMPL->fetch_param('send_attachment')) ? 
														ee()->TMPL->fetch_param('send_attachment') : '' ;
        		
		//	----------------------------------------
		//	Send user email?
		//	----------------------------------------
		
		$this->params['send_user_email']		= (ee()->TMPL->fetch_param('send_user_email')) ? 
														ee()->TMPL->fetch_param('send_user_email') : '' ;
        		
		//	----------------------------------------
		//	Send user attachments?
		//	----------------------------------------
		
		$this->params['send_user_attachment']	= (ee()->TMPL->fetch_param('send_user_attachment')) ? 
														ee()->TMPL->fetch_param('send_user_attachment') : '' ;
        		
		//	----------------------------------------
		//	User email template
		//	----------------------------------------
		
		$this->params['user_email_template']	= (ee()->TMPL->fetch_param('user_email_template')) ? 
														ee()->TMPL->fetch_param('user_email_template') : 'default_template' ;
														
		//	----------------------------------------
		//	Are we using a notification template?
		//	----------------------------------------
		
		$this->params['template']				= (ee()->TMPL->fetch_param('template')) ? 
														str_replace(SLASH, '/', ee()->TMPL->fetch_param('template')) : 
														'default_template' ;
        		
		//	----------------------------------------
		//	Mailing lists?
		//	----------------------------------------
		$mailinglist							= ( ee()->TMPL->fetch_param('mailinglist') AND 
													ee()->TMPL->fetch_param('mailinglist') != '' ) ?
														ee()->TMPL->fetch_param('mailinglist'): FALSE;
        		
		//	----------------------------------------
		//	Mailing list opt in?
		//	----------------------------------------
		
		$mailinglist_opt_in						= ( ee()->TMPL->fetch_param('mailinglist_opt_in') AND
		 											$this->check_no(ee()->TMPL->fetch_param('mailinglist_opt_in')) ) ? TRUE : FALSE;
        		
		//	----------------------------------------
		//	Are we redirecting on duplicate?
		//	----------------------------------------
		
		$redirect_on_duplicate					= (ee()->TMPL->fetch_param('redirect_on_duplicate')) ? 
														str_replace(SLASH, '/', 
															ee()->TMPL->fetch_param('redirect_on_duplicate')) : FALSE;
        		
		//	----------------------------------------
		//	Prevent duplicates on something specific
		//	----------------------------------------
		
		$this->params['prevent_duplicate_on']	= (ee()->TMPL->fetch_param('prevent_duplicate_on')) ?
		 												ee()->TMPL->fetch_param('prevent_duplicate_on') : '';
        		
		//	----------------------------------------
		//	File upload directory
		//	----------------------------------------
		
		$this->params['file_upload']			= (ee()->TMPL->fetch_param('file_upload')) ? 
														ee()->TMPL->fetch_param('file_upload') : '';
    	
		//	----------------------------------------
		//	Sniff for fields of type 'file'
		//	----------------------------------------
		
		if ( preg_match_all( "/type=['|\"]?file['|\"]?/", $tagdata, $match ) )
		{
			$this->multipart				= TRUE;
			$this->params['upload_limit']	= count( $match['0'] );
		}
    
        //	----------------------------------------
        //	Grab custom member profile fields
        //	----------------------------------------
        
        $query		= ee()->db->query(
			"SELECT m_field_id, m_field_name 
			 FROM 	exp_member_fields"
		);

		if ( $query->num_rows() > 0 )
		{
			foreach ($query->result_array() as $row)
			{ 
				$mfields[$row['m_field_name']] = $row['m_field_id'];
			}
		}
		
        //	End custom member fields 
        
    
        //	----------------------------------------
        //	Grab standard member profile fields
        //	----------------------------------------
        
        $mdata		= array();
        
        if ( ee()->session->userdata['member_id'] != '0' )
        {        
			$query		= ee()->db->query(
				"SELECT 	* 
				 FROM 	exp_members 
				 WHERE 	member_id = '" . ee()->db->escape_str(ee()->session->userdata['member_id']) . 
			  "' LIMIT 1"
			);
	
			if ( $query->num_rows() > 0 )
			{
				foreach ($query->result_array() as $row)
				{
					foreach ( $row as $key => $val )
					{
						$mdata[$key] = $val;
					}
				}
			}
		}
		
        //	End standard member fields 
        
    
        //	----------------------------------------
        //	Grab custom member data
        //	----------------------------------------
        
        if ( ee()->session->userdata['member_id'] != '0' )
        {
			$query		= ee()->db->query(
				"SELECT * 
				 FROM 	exp_member_data 
				 WHERE 	member_id = '" . 
					ee()->db->escape_str(ee()->session->userdata['member_id']) . 
			  "' LIMIT 1"
			);

			if ($query->num_rows() > 0)
			{					
				foreach ($query->row() as $key => $val)
				{ 
					$mdata[$key] = $val;
				}
			}
		}
		
        //	End custom member data 
        
        
		//	----------------------------------------
		//	Check for duplicate
		//	----------------------------------------
		
		if ( ee()->session->userdata['group_id'] == 1 AND ee()->input->ip_address() == '0.0.0.0')
		{
			$duplicate	= FALSE;
		}
		else
		{
			//	Begin the query
			$sql	= "	SELECT 	count(*) AS	count 
					   	FROM 	exp_freeform_entries 
						WHERE 	status != 'closed'";
			
			//	Handle form_name
			if ( $this->params['form_name'] != '' )
			{
				$sql	.= " AND form_name = '" . ee()->db->escape_str($this->params['form_name']) . "'";
			}
			
			//	Identify them
			if ( ee()->session->userdata['member_id'] != '0' )
			{
				$sql	.= " AND author_id = '" . 
						   ee()->db->escape_str(ee()->session->userdata['member_id']) . "'";
			}
			elseif ( ee()->input->ip_address() )
			{
				$sql	.= " AND ip_address = '" . 
				           ee()->db->escape_str(ee()->input->ip_address()) . "'";
			}
			
			//	Query
			$query	= ee()->db->query( $sql );
			
			$duplicate	= ( $query->row('count') > 0 ) ? TRUE: FALSE;
		}
		
		
		//	----------------------------------------
		//	Redirect on duplicate
		//	----------------------------------------
		
		if ( $redirect_on_duplicate AND $duplicate )
		{
			ee()->functions->redirect( ee()->functions->create_url( $redirect_on_duplicate ) );
			exit;
		}
		
    
        //	----------------------------------------
        //	Parse conditional pairs
        //	----------------------------------------
        
        $cond['duplicate']		= ( $duplicate ) ? TRUE: FALSE;
        $cond['not_duplicate']	= ( ! $duplicate ) ? TRUE: FALSE;
        $cond['captcha']		= ( $this->check_yes(ee()->config->item('captcha_require_members'))  OR  
									($this->check_no( ee()->config->item('captcha_require_members'))  AND 
									 	  ee()->session->userdata('member_id') == 0 ) 
								  ) ? TRUE: FALSE;
        
		$tagdata	= ee()->functions->prep_conditionals( $tagdata, $cond );
        
    
        //	----------------------------------------
        //	Parse variable pairs
        //	----------------------------------------
        
		$output	= '';
        
		if ( preg_match( "/" . LD . "mailinglists.*?(backspace=[\"|'](\d+?)[\"|'])?" . RD . "(.*?)" . 
						 LD . preg_quote(T_SLASH, '/') . "mailinglists" . RD . "/s", $tagdata, $match ) )
		{			
			if ( ee()->db->table_exists('exp_mailing_lists') )
			{
				$query	= ee()->db->query( "SELECT 	* 
										    FROM 	exp_mailing_lists" );
				
				if ( $query->num_rows() > 0 )
				{				
					foreach ( $query->result_array() as $row )
					{
						$chunk	= $match['3'];
						
						foreach ( $row as $key => $val )
						{
							$chunk	= str_replace( LD . $key . RD, $val, $chunk );
						}
						
						$output	.= trim( $chunk )."\n";
					}
						
					$tagdata	= str_replace( $match['0'], $output, $tagdata );
				}
				else
				{
					$tagdata	= str_replace( $match['0'], '', $tagdata );
				}
			}
			else
			{
				$tagdata	= str_replace( $match['0'], '', $tagdata );
			}
        }
        elseif ( $mailinglist )
        {
        	unset( ee()->TMPL->tagparams['mailinglist'] );
			
			if ( ee()->db->table_exists('exp_mailing_lists') )
			{        	
				$lists	= ee()->db->escape_str(implode( "','", preg_split( "/,|\|/" , $mailinglist ) ));
				
				$query	= ee()->db->query( 
					"SELECT list_id 
					 FROM 	exp_mailing_lists 
					 WHERE 	list_id 
					 IN 	('$lists') 
					 OR 	list_name 
					 IN 	('$lists')" 
				);
				
				if ( $query->num_rows() > 0 )
				{
					foreach ( $query->result_array() as $row )
					{
						$output	.= '<input type="hidden" name="mailinglist[]" value="'.$row['list_id'].'" />'."\n";
					}
				}
				
				$tagdata	.= '<div>'.$output.'</div>';
			}
        }

        //	----------------------------------------
        //	Parse recipient lists
        //	----------------------------------------

		if ( $this->params['static_recipients'] AND preg_match( "/" . LD . "recipients" . RD . "(.*?)" . 
						 LD . preg_quote(T_SLASH, '/') . "recipients" . RD . "/s", $tagdata, $match ) )
		{
			$repeater 		= $match[1];
			
			$output			= '';
			
			$recipient_list = $this->params['static_recipients_list'];
			
			for ( $i = 1, $l = count($recipient_list); $i <= $l; $i++ )
			{					
				$output	.= trim( 
					str_replace( 
						array( 
							LD . "recipient_name" . RD, 	
							LD . "recipient_value" . RD, 
							LD . "count" . RD 
						), 
						array( 
							$recipient_list[$i]['name'], 
							$recipient_list[$i]['key'], 
							$i 
						),
						$repeater
					) 
				)."\n";

				//conditionals
				$output	= ee()->functions->prep_conditionals( 
					$output, 
					array(
						'recipient_email'  	=> TRUE,
						'count'				=> $i,
						'recipient_name'	=> ($recipient_list[$i]['name'] != '')
					) 
				);
				
			}
				
			$tagdata	= str_replace( $match['0'], $output, $tagdata );
		}
    
        //	----------------------------------------
        //	Parse single variables
        //	----------------------------------------
                
        foreach (ee()->TMPL->var_single as $key => $val)
        {

            //	----------------------------------------
            //	parse {recipient_name1}, {recipient_value1}
            //	----------------------------------------

			if  ( preg_match("/^recipient_([name|value]+)([0-9]+)$/", $key, $matches) )
			{
				if ( array_key_exists($matches[2], $this->params['static_recipients_list']) )
				{
					$tagdata	= ee()->TMPL->swap_var_single(
						$key, 
						($matches[1] == 'value') ? 
							$this->params['static_recipients_list'][$matches[2]]['key'] : 
							$this->params['static_recipients_list'][$matches[2]]['name'], 
						$tagdata
					);
					
					//parse conditionals using the data
					$tagdata	= ee()->functions->prep_conditionals( 
						$tagdata, 
						array(
							$key  => 	($matches[1] == 'value') ? 
									$this->params['static_recipients_list'][$matches[2]]['key'] : 
									$this->params['static_recipients_list'][$matches[2]]['name'],
							'recipient' . $matches[2] => TRUE
						) 
					);
				}
			}
	
            //	----------------------------------------
            //	parse {name}
            //	----------------------------------------
            
            if ($key == 'name')
            {
                $name		= (ee()->session->userdata['screen_name'] != '') 	? 
								ee()->session->userdata['screen_name'] 		: 
								ee()->session->userdata['username'];
            
                $tagdata	= ee()->TMPL->swap_var_single($key, form_prep($name), $tagdata);
            }
                    
            //	----------------------------------------
            //	parse {email}
            //	----------------------------------------
            
            if ($key == 'email')
            {
                $email		= ( ! ee()->input->post('email')) ? 
									ee()->session->userdata['email'] : 
									ee()->input->post('email');
              
                $tagdata	= ee()->TMPL->swap_var_single($key, form_prep($email), $tagdata);
            }

            //	----------------------------------------
            //	parse {url}
            //	----------------------------------------
            
            if ($key == 'url')
            {
                $url	= ( ! ee()->input->post('url')) ? 
								ee()->session->userdata['url'] : 
								ee()->input->post('url');
                
                if ($url == '')
                {
                    $url = 'http://';
				}

                $tagdata = ee()->TMPL->swap_var_single($key, form_prep($url), $tagdata);
            }

            //	----------------------------------------
            //	parse {location}
            //	----------------------------------------
            
            if ($key == 'location')
            {
                $location	= ( ! ee()->input->post('location')) ? 
									ee()->session->userdata['location'] : ee()->input->post('location');

                $tagdata	= ee()->TMPL->swap_var_single($key, form_prep($location), $tagdata);
            }
            
            //	----------------------------------------
            //	parse {captcha}
            //	----------------------------------------

			if ( preg_match("/({captcha})/", $tagdata) )
			{
				$tagdata	= preg_replace("/{captcha}/", ee()->functions->create_captcha(), $tagdata);
				$this->params['require_captcha']	= 'yes';
			}
                
			//	----------------------------------------
			//	parse custom member fields
			//	----------------------------------------
			
			if ( isset( $mfields[$key] ) )
			{
				if ( isset( $mdata[$key] ) )
				{
					$tagdata = ee()->TMPL->swap_var_single($key, $mdata[$key], $tagdata);
				}
				//	If a custom member field is set
				elseif ( isset( $mdata['m_field_id_'.$mfields[$key]] ) )
				{
					$tagdata = ee()->TMPL->swap_var_single( $key,  $mdata['m_field_id_'.$mfields[$key]], $tagdata );
				}
				else
				{
					$tagdata = ee()->TMPL->swap_var_single($key, '', $tagdata);
				}
			}
        }
        		
		//	----------------------------------------
		//	Do we have a return parameter?
		//	----------------------------------------
		
		$return	= ( ee()->TMPL->fetch_param('return') ) ? ee()->TMPL->fetch_param('return'): '';
        
        //	----------------------------------------
        //	Create form
        //	----------------------------------------
               
        $hidden = array(
			'ACT'					=> ee()->functions->fetch_action_id('Freeform', 'insert_new_entry'),
			'URI'					=> (ee()->uri->uri_string == '') ? 'index' : ee()->uri->uri_string,
			'XID'					=> ( ! ee()->input->post('XID')) ? '' : ee()->input->post('XID'),
			'status'				=> ( ee()->TMPL->fetch_param('status') !== FALSE 	AND 
										 ee()->TMPL->fetch_param('status') == 'closed' ) ? 
											'closed' : 'open',
			'return'				=> $this->_chars_decode(str_replace(SLASH, '/', $return)),
			'redirect_on_duplicate'	=> $redirect_on_duplicate
		);
                           
        // unset( ee()->TMPL->tagparams['notify'] );
                              
		// $hidden	= array_merge( $hidden, ee()->TMPL->tagparams );
		
		if ( $mailinglist_opt_in )
		{
			$hidden['mailinglist_opt_in'] = 'no';
		}
    	
		//	----------------------------------------
		//	Create form
		//	----------------------------------------
		
		$this->data					= $hidden;
		
		$this->data['RET']			= ee()->input->post('RET') ? 
											ee()->input->post('RET') : ee()->functions->fetch_current_uri();

		$this->data['form_name']	= $this->params['form_name'];
				
		$this->data['tagdata']		= $tagdata;
    	
		//	----------------------------------------
		//	Return
		//	----------------------------------------
		
		$r	= $this->_form();
		
		//	----------------------------------------
		//	Add class
		//	----------------------------------------
		
		if ( $class = ee()->TMPL->fetch_param('form_class') )
		{
			$r	= str_replace( "<form", "<form class=\"$class\"", $r );
		}
		
		//	----------------------------------------
		//	Add title
		//	----------------------------------------
		
		if ( $form_title = ee()->TMPL->fetch_param('form_title') )
		{
			$r	= str_replace( "<form", "<form title=\"".htmlspecialchars($form_title)."\"", $r );
		}
		
		//	----------------------------------------
		//	'freeform_module_form_end' hook.
		//	 - This allows developers to change the form before output.
        //	----------------------------------------
        
		if (ee()->extensions->active_hook('freeform_module_form_end') === TRUE)
		{
			$r = ee()->extensions->universal_call('freeform_module_form_end', $r);
			if (ee()->extensions->end_script === TRUE) return;
		}
        //	----------------------------------------
                		
		//return str_replace('&#47;', '/', $r);
		//return $this->_chars_decode($r);
		
		//uh, so it seems that EE needs these to be {&#47;exp: to work
		return $r;
    }
    
    //	End form 
    
    // --------------------------------------------------------------------

	/**
	 * insert new entry to db
	 *
	 * @access	public
	 * @return	null
	 */

    function insert_new_entry()
    {

    
        $default	= array('name', 'email');
        
        $all_fields	= '';
        
        $fields		= array();
        
        $entry_id	= '';

		$msg		= array();
        
        foreach ($default as $val)
        {
			if ( ! isset($_POST[$val]))
			{
				$_POST[$val] = '';
			}
        }        
               
        //	----------------------------------------
        //	Fetch the freeform language pack
        //	----------------------------------------
        
        ee()->lang->loadfile('freeform');        
                
        //	----------------------------------------
        //	Is the user banned?
        //	----------------------------------------
        
        if (ee()->session->userdata['is_banned'] == TRUE)
        {
        	return ee()->output->show_user_error('general', array(ee()->lang->line('not_authorized')));
        }
                
        //	----------------------------------------
        //	Is the IP address and User Agent required?
        //	----------------------------------------
                
        if ( $this->check_yes($this->_param('require_ip')) )
        {
        	if (ee()->session->userdata['group_id'] != 1 AND ee()->input->ip_address() == '0.0.0.0')
        	{            
            	return ee()->output->show_user_error('general', array(ee()->lang->line('not_authorized')));
        	}        	
        }
        
        //	----------------------------------------
		//	Is the nation of the user banned?
        //	----------------------------------------
        
		ee()->session->nation_ban_check();
        
        //	----------------------------------------
        //	Blacklist/Whitelist Check
        //	----------------------------------------
        
        if ($this->check_yes(ee()->blacklist->blacklisted) && $this->check_no(ee()->blacklist->whitelisted))
        {
        	return ee()->output->show_user_error('general', array(ee()->lang->line('not_authorized')));
        }
        
        //	----------------------------------------
        //	Check duplicates
        //	----------------------------------------
        
        if ( $this->_param('prevent_duplicate_on') 			AND 
			 $this->_param('prevent_duplicate_on') != '' 	AND 
				( 	ee()->session->userdata['group_id'] != 1 	OR 	
					ee()->input->get_post('email') != '' ) 
		   )
        {
        	$sql	= "	SELECT 	COUNT(*) 
						AS 		count 
						FROM 	exp_freeform_entries 
						WHERE 	status != 'closed'";

			if ( $this->_param('form_name') )
			{
				$sql	.= " AND form_name = '".ee()->db->escape_str($this->_param('form_name'))."'";
			}

			if ( $this->_param('prevent_duplicate_on') == 'member_id' AND ee()->session->userdata['member_id'] != '0' )
			{
				$sql	.= " AND author_id = '".ee()->db->escape_str(ee()->session->userdata['member_id'])."'";
			}
			elseif ( $this->_param('prevent_duplicate_on') == 'ip_address' 	AND 
					  ee()->input->ip_address() != '0.0.0.0' 				AND 
					  ee()->session->userdata['group_id'] != 1)
			{
				$sql	.= " AND ip_address = '".ee()->db->escape_str(ee()->input->ip_address())."'";
			}
			else
			{
				$sql	.= " AND email = '".ee()->db->escape_str(ee()->input->get_post('email'))."'";
			}
        	
        	$dup	= ee()->db->query( $sql );
        	
        	if ( $dup->row('count') > 0 )
        	{
				return ee()->output->show_user_error('general', array(ee()->lang->line('no_duplicates')));
        	}
        }        
        
        //	----------------------------------------
        //	Start error trapping on required fields
        //	----------------------------------------
        
        $errors	= array();
        
        // Are there any required fields?
        
        if ( $this->_param('ee_required') != '' )
        {
        	$required_fields	= preg_split("/,|\|/" ,$this->_param('ee_required'));
        	
			//	----------------------------------------
			//	Let's get labels from the DB
			//	----------------------------------------
			
        	$query	= ee()->db->query(
				"SELECT * 
				 FROM 	exp_freeform_fields"
			);
        	
        	$labels	= array();
        	
        	if ( $query->num_rows() > 0 )
        	{        	
				foreach ($query->result_array() as $row)
				{
					$labels[$row['name']]	= $row['label'];
				}        	
        	
				// Check for empty fields
				
				foreach ( $required_fields as $val )
				{
					if ( ! ee()->input->post($val) OR ee()->input->post($val) == '' )
					{
						if (array_key_exists($val, $labels))
						{
							$errors[] = ee()->lang->line('field_required') . '&nbsp;' . $labels[$val];
						}
						else
						{
							$errors[] = ee()->lang->line('not_in_field_list') . '&nbsp;' . $val;
						}  
					}
				}
				
				//	End empty check 
			}
			
        	//	End labels from DB 
        
			//	----------------------------------------
			//	Do we require an email address?
			//	----------------------------------------
			
			if ( isset( $labels['email'] ) AND ee()->input->get_post('email') )
			{
				//	----------------------------------------
				//	Valid email address?
				//	----------------------------------------
				
				//1.x
				if (APP_VER < 2.0)
				{
					if ( ! class_exists('Validate'))
					{
						require PATH_CORE.'core.validate'.EXT;
					}
					
					$VAL = new Validate( array( 'email' => ee()->input->get_post('email') ) );
				}
				//2.x
				else
				{
					if ( ! class_exists('EE_Validate'))
					{
						require APPPATH . 'libraries/Validate'.EXT;
					}
					
					$VAL = new EE_Validate( array( 'email' => ee()->input->get_post('email') ) );
				}
					
				$VAL->validate_email();
		
				//	----------------------------------------
				//	Display errors if there are any
				//	----------------------------------------
		
				if (count($VAL->errors) > 0)
				{
					return ee()->output->show_user_error('general', $VAL->errors );
				}
			}
        }
        
		//	----------------------------------------
		//	Are we trying to accept file uploads?
		//	----------------------------------------
        
        if ( $this->_param('file_upload') != '' AND $this->upload_limit = $this->_param('upload_limit') )
        {
        	$this->_upload_files( TRUE );
        }
		
		//	----------------------------------------
		//	'freeform_module_validate_end' hook.
		//	 - This allows developers to do more form validation.
		//	----------------------------------------
		
		if (ee()->extensions->active_hook('freeform_module_validate_end') === TRUE)
		{
			$errors = ee()->extensions->universal_call('freeform_module_validate_end', $errors);
			if (ee()->extensions->end_script === TRUE) return;
		}
        //	----------------------------------------
        
        //	----------------------------------------
        //	Do we have errors to display?
        //	----------------------------------------
        
        if (count($errors) > 0)
        {
           return ee()->output->show_user_error('submission', $errors);
        }
        
        //	----------------------------------------
        //	Do we require captcha?
        //	----------------------------------------
		
		if ( $this->_param('require_captcha') AND $this->check_yes($this->_param('require_captcha')) )
		{
			if ( $this->check_yes(ee()->config->item('captcha_require_members'))  OR  
					( $this->check_no(ee()->config->item('captcha_require_members')) AND 
					  ee()->session->userdata('member_id') == 0)
			   )
			{
				if ( ! ee()->input->post('captcha') OR ee()->input->post('captcha') == '')
				{
					return ee()->output->show_user_error('submission', ee()->lang->line('captcha_required'));
				}
				else
				{
					$res = ee()->db->query(
						"SELECT COUNT(*) 
						 AS 	count 
						 FROM 	exp_captcha 
						 WHERE 	word='" . ee()->db->escape_str(ee()->input->post('captcha')) . "' 
						 AND 	ip_address = '" . ee()->db->escape_str(ee()->input->ip_address()) . "' 
						 AND 	date > UNIX_TIMESTAMP()-7200"
					);
				
					if ($res->row('count') == 0)
					{
						return ee()->output->show_user_error('submission', ee()->lang->line('captcha_incorrect'));
					}
				
					// Moved because of file uploading errors
					/*
					  ee()->db->query("DELETE FROM exp_captcha 
											WHERE (word='".ee()->db->escape_str($_POST['captcha'])."' 
											AND ip_address = '".ee()->db->escape_str(ee()->input->ip_address())."') 
											OR date < UNIX_TIMESTAMP()-7200");
					*/
				}
			}
		}        
        
        //	----------------------------------------
        //	Check Form Hash
        //	----------------------------------------
        
        if ( $this->check_yes(ee()->config->item('secure_forms')) )
        {        	
            $query = ee()->db->query(
				"SELECT 	COUNT(*) 
				 AS 		count 
				 FROM 		exp_security_hashes 
				 WHERE 		hash='" . ee()->db->escape_str(ee()->input->post('XID')) . "' 
				 AND 		ip_address = '" . ee()->db->escape_str(ee()->input->ip_address())."' 
				 AND	 	date > UNIX_TIMESTAMP()-7200"
			);
        
			//email_change
            if ($query->row('count') == 0)
            {
				return ee()->output->show_user_error('general', array(ee()->lang->line('not_authorized')));
            }
            
            // Moved because of file uploading errors                    
			/* ee()->db->query("DELETE FROM exp_security_hashes 
									 WHERE (hash='".ee()->db->escape_str($_POST['XID'])."' 
									 AND ip_address = '".ee()->db->escape_str(ee()->input->ip_address())."') 
									 OR date < UNIX_TIMESTAMP()-7200");
			*/
        }
                        
        //	----------------------------------------
        //	Let's get all of the fields from the
        //	database for testing purposes
        //	----------------------------------------
        
        $fields['form_name']	= "Collection Name";
        
        $query		= ee()->db->query(
			"SELECT 	name, label 
			 FROM 		exp_freeform_fields 
			 ORDER BY 	field_order 
			 ASC"
		);
        
        if ($query->num_rows() > 0)
        {
        	foreach($query->result_array() as $row)
        	{
        		$fields[$row['name']]	= $row['label'];
        	}
        }
        else
        {
        	return false;
        }        
        
        //	----------------------------------------
        //	Build the data array
        //	----------------------------------------
        
        $exclude	= array('ACT', 'RET', 'URI', 'PRV', 'XID', 'return', 'ee_notify', 'ee_required', 'submit');
							
		$include	= array('status');
        
        $data		= array(
            'author_id'		=> ee()->session->userdata['member_id'],
            'group_id'		=> ee()->session->userdata['group_id'],
            'ip_address'	=> ee()->input->ip_address(),
            'entry_date'	=> ee()->localize->now,
            'edit_date'		=> ee()->localize->now
		);
        			
        foreach ( $_POST as $key => $val )
        {
			//	----------------------------------------
        	//	If the given field is not a FreeForm
        	//	field or not in our include list, then
        	//	skip it.
			//	----------------------------------------
        	
        	if ( ! array_key_exists( $key, $fields ) AND ! in_array( $key, $include ) ) continue;
        	
			//	----------------------------------------
        	//	If the given field is in our exclude
        	//	list, then skip it.
			//	----------------------------------------
			
        	if ( in_array( $key, $exclude ) ) continue;
        	
        	if ( $key == 'website' )
        	{
        		ee()->security->xss_clean( prep_url( ee()->input->post('website') ) );
        		
        		$data[$key]	= ee()->input->post($key);
        	}
        	
			// If the field is a multi-select field, then handle it as such.
			if ( is_array( $val ) )
			{
				$val = implode( "\n", $val );
				
				$data[$key] = ee()->security->xss_clean($val);
			}
			else
			{
				$data[$key] = ee()->security->xss_clean($val);
			}
        }
		
		//backup for form name in case it isnt in the post data
		if ( ! isset($data['form_name']) AND $this->_param('form_name') !== FALSE)
		{
			$data['form_name'] = $this->_param('form_name');
		}
		
		//check to see if there is any missing data that we have in the params:
		/*foreach($fields as $f_key => $f_value)
		{
			if ( ! isset($data[$f_key]) AND $this->_param($f_key) !== FALSE)
			{
				$data[$f_key] = $this->_param($f_key);
			}
		}*/
		
		//i dont want to remove this because we might need it for some god awful reason, but it screws with stuff.
		$fields['subject']		= "Subject";
		
		//	----------------------------------------
		//	'freeform_module_insert_begin' hook.
		//	 - This allows developers to do one last thing before Freeform submit is ended.
		//	----------------------------------------
		
		if (ee()->extensions->active_hook('freeform_module_insert_begin') === TRUE)
		{
			$data = ee()->extensions->universal_call('freeform_module_insert_begin', $data);
			if (ee()->extensions->end_script === TRUE) return;
		}
        
		//	------------------------------------------------------------------------------------
      	//  Discarded data email_change
		//  ------------------------------------------------------------------------------------
              
        //	----------------------------------------
        //	Are we discarding some field values and preventing data save on them?
        //	----------------------------------------
        
        if ( $this->_param('discard_field') != '' )
        {        
        	foreach ( explode( "|", $this->_param('discard_field') ) as $val )
        	{
        		if ( ! empty( $data[ $val ] ) )
        		{
        			$data[ $val ]	= ee()->lang->line('discarded_field_data');
        		}
        	}       
        }

		//	------------------------------------------------------------------------------------
      	//  end Discarded data email_change
		//  ------------------------------------------------------------------------------------


        //	----------------------------------------
        //	Submit data into DB
        //	----------------------------------------

		$sql			= ee()->db->insert_string( 'exp_freeform_entries', $data ); //email_change
		
		$query			= ee()->db->query( $sql );
		
		$this->entry_id	= ee()->db->insert_id();
        
        //	----------------------------------------
        //	Process file uploads
        //	----------------------------------------
        
        if ( count( $this->upload ) > 0 )
        {
        	$this->_upload_files();
        }	
        
		//----------------------------------------
		//	 Delete CAPTCHA and Form Hash - Moved here because of File Upload Error possibilities
		//	----------------------------------------
		
		if ( $this->check_yes($this->_param('require_captcha')) && isset($_POST['captcha']))
		{
			ee()->db->query(
				"DELETE FROM 	exp_captcha 
				 WHERE	 		(word='" . ee()->db->escape_str(ee()->input->post('captcha')) . "' 
				 AND 			ip_address = '" . ee()->db->escape_str(ee()->input->ip_address()) . "') 
				 OR 			date < UNIX_TIMESTAMP()-7200"
			);
		}
        
        if ( $this->check_yes(ee()->config->item('secure_forms')) && ee()->input->post('XID') )
        {        	
            ee()->db->query(
				"DELETE FROM 	exp_security_hashes 
				 WHERE 			(hash='" . ee()->db->escape_str(ee()->input->post('XID')) . "' 
				 AND 			ip_address = '" . ee()->db->escape_str(ee()->input->ip_address()) . "') 
				 OR 			date < UNIX_TIMESTAMP()-7200"
			);
        }
		
        //	----------------------------------------
        //	Send notifications
        //	----------------------------------------
        
        if ( $this->_param('ee_notify') != '' )
        {
        	$recipients	= preg_split("/,|\|/" , $this->_param('ee_notify') );
        	
        	$template	= ( $this->_param('template') AND $this->_param('template') != '' ) ? 
							$this->_param('template'): 'default_template';
		
			//	----------------------------------------
			//	Generate message
			//	----------------------------------------
			
			$msg		= array();
			
			$query		= ee()->db->query(
				"SELECT * 
				 FROM 	exp_freeform_templates 
				 WHERE 	template_name = '" . ee()->db->escape_str($template) . "' 
				 AND 	enable_template = 'y' 
				 LIMIT 	1"
			);

			if ( $query->num_rows() == 0 )
			{
				return ee()->output->show_user_error('general', array(ee()->lang->line('template_not_available')));
			}
			
			$msg['from_name']	= ( $query->row('data_from_name') != '' ) ?
			 							$query->row('data_from_name'): ee()->config->item('webmaster_name');

			$msg['from_email']	= ( $query->row('data_from_email') != '' ) ?
			 							$query->row('data_from_email'): ee()->config->item('webmaster_email');

			$msg['subject']		= $query->row('data_title');

			$msg['msg']			= $query->row('template_data');

			$wordwrap			= $this->check_yes($query->row('wordwrap'));
			
			$msg['subject']		= str_replace( 	LD.'entry_date'.RD, 
											   	ee()->localize->set_human_time(ee()->localize->now), 
												$msg['subject'] );
			
			$msg['msg']			= str_replace( 	LD.'entry_date'.RD, 
												ee()->localize->set_human_time(ee()->localize->now), 
												$msg['msg'] );
			
			$msg['subject']		= str_replace( 	LD.'freeform_entry_id'.RD, $this->entry_id, $msg['subject'] );
			$msg['msg']			= str_replace( 	LD.'freeform_entry_id'.RD, $this->entry_id, $msg['msg'] );
			
			if (preg_match_all("/".LD."(entry_date)\s+format=([\"'])(.*?)\\2".RD."/is", 
							   $msg['subject'].$msg['msg'], $matches)
			   )
			{
				for ($j = 0; $j < count($matches[0]); $j++)
				{	
					$val = $matches[3][$j];
					
					foreach (ee()->localize->fetch_date_params($matches[3][$j]) AS $dvar)
					{
						$val = str_replace($dvar, ee()->localize->convert_timestamp($dvar, ee()->localize->now, TRUE), $val);					
					}
					
					$msg['subject']		= str_replace( $matches[0][$j], $val, $msg['subject'] );
			
					$msg['msg']			= str_replace( $matches[0][$j], $val, $msg['msg'] );
				}
			}
			
			//	----------------------------------------
			//	Parse conditionals
			//	----------------------------------------
			
			//template isn't defined yet, so we have to fetch it
			//1.x
			if(APP_VER < 2.0)
			{
				if ( ! class_exists('Template'))
				{
					require PATH_CORE.'core.template'.EXT;
				}
			
				$local_TMPL	= new Template();
			}
			//2.x
			else
			{
				ee()->load->library('template');
				$local_TMPL =& ee()->template;
			}
			
			$data['attachment_count']		= count( $this->attachments );
			
			//i have no idea why this is being done instead of just using $data...			
			$cond		= $data;
			
			foreach( $msg as $key => $val )
			{
				$msg[$key]	= $local_TMPL->advanced_conditionals( 
					ee()->functions->prep_conditionals( $msg[$key], $cond ) 
				);
			}

			unset( $cond );

			//	----------------------------------------
			//	Parse individual fields
			//	----------------------------------------
			
			$exclude	= array('submit');
			
			foreach ( $msg as $key => $val )
			{
				//	----------------------------------------
				//	Handle attachments
				//	----------------------------------------
				
				$msg[$key]	= str_replace( LD."attachment_count".RD, $data['attachment_count'], $msg[$key] );
						
				if ( $key == 'msg' )
				{
					$all_fields	.= "Attachments: ".$data['attachment_count']."\n";
					
					$n		= 0;
					
					foreach ( $this->attachments as $file )
					{
						$n++;						
						$all_fields	.= "Attachment $n: ".$file['filename']." ".$this->upload['url'].$file['filename']."\n";
					}
				}
				
				if ( preg_match( "/".LD."attachments".RD."(.*?)".LD."\/attachments".RD."/s", $msg[$key], $match ) )
				{
					if ( count( $this->attachments ) > 0 )
					{
						$str	= '';
						
						foreach ( $this->attachments as $file )
						{
							$tagdata	= $match['1'];
							$tagdata	= str_replace( LD."fileurl".RD, $this->upload['url'].$file['filename'], $tagdata );
							$tagdata	= str_replace( LD."filename".RD, $file['filename'], $tagdata );
							$str		.= $tagdata;
						}
						
						$msg[$key]	= str_replace( $match['0'], $str, $msg[$key] );
					}
					else
					{
						$msg[$key]	= str_replace( $match['0'], "", $msg[$key] );
					}
				}
				
				//	----------------------------------------
				//	Loop
				//	----------------------------------------
				
				foreach ( $fields as $name => $label )
				{
					if ( isset( $data[$name] ) AND ! in_array( $name, $exclude ) )
					{
						$msg[$key]	= str_replace( LD.$name.RD, $data[$name], $msg[$key] );
						
						//	----------------------------------------
						//	We don't want to concatenate for every
						//	time through the main loop.
						//	----------------------------------------
						
						if ( $key == 'msg' )
						{
							$all_fields	.= $label.": ".$data[$name]."\n";
						}
					}
					else
					{
						$msg[$key]	= str_replace( LD.$name.RD, '', $msg[$key] );
					}
				}
			}
			
			
			//	----------------------------------------
			//	Parse all fields variable
			//	----------------------------------------
			
			if ( stristr( $msg['msg'], LD.'all_custom_fields'.RD ) )
			{
				$msg['msg']	= str_replace( LD.'all_custom_fields'.RD, $all_fields, $msg['msg'] );
			}
			
			
			//	----------------------------------------
			//	'freeform_module_admin_notification' hook.
			//	 - This allows developers to alter the 
			//	   $msg array before admin notification is sent.
			//	----------------------------------------
			
			if (ee()->extensions->active_hook('freeform_module_admin_notification') === TRUE)
			{
				$msg = ee()->extensions->universal_call('freeform_module_admin_notification', $fields, $this->entry_id, $msg);
				if (ee()->extensions->end_script === TRUE) return;
			}
			//	----------------------------------------
			
			//	----------------------------------------
			//	Send email
			//	----------------------------------------
			
			ee()->email->wordwrap	= $wordwrap;
			ee()->email->mailtype	= ( $this->check_yes($query->row('html')) ) ? 'html': 'text';
			
			if ( count( $this->attachments ) > 0 AND $this->check_yes($this->_param('send_attachment')) )
			{
				foreach ( $this->attachments as $file_name )
				{
					ee()->email->attach( $file_name['filepath'] );
				}
				
				ee()->db->query( 
					ee()->db->update_string( 
						'exp_freeform_attachments', 
						array( 'emailed' 	=> 'y' ), 
						array( 'entry_id' 	=> $this->entry_id ) 
					) 
				);
			}
			
			foreach ($recipients as $val)
			{								
				ee()->email->initialize();
				ee()->email->from($msg['from_email'], $msg['from_name']);	
				ee()->email->to($val); 
				ee()->email->subject($msg['subject']);	
				ee()->email->message(entities_to_ascii($msg['msg']));						
				ee()->email->send();
				
			}
			ee()->email->clear(TRUE);

			$msg = array();
		
			//	----------------------------------------
			//	Register the template used
			//	----------------------------------------
			
			ee()->db->query( 
				ee()->db->update_string( 
					'exp_freeform_entries', 
					array( 'template' 	=> $template), 
					array( 'entry_id' 	=> $this->entry_id ) 
				) 
			);
		}
		
        //	----------------------------------------
        //	Send user email email_change
        //	----------------------------------------
        
        if ($this->check_yes($this->_param('recipients')) AND 
			( ee()->session->userdata['group_id'] == 1 OR ee()->input->ip_address() != '0.0.0.0' ) AND 
			ee()->input->post('recipient_email') !== FALSE)
        {	
			$all_fields	= '';
			
			
			
			//don't we already do this...?
        	$template	= ( $this->_param('recipient_template') AND $this->_param('recipient_template') != '' ) ? 
							$this->_param('recipient_template') : 'default_template';
	
			//	----------------------------------------
			//	Array of recipients?
			//	----------------------------------------

			if ( is_array( ee()->input->post('recipient_email') ) === TRUE AND 
				count( ee()->input->post('recipient_email') ) > 0 )
			{
				$recipient_email	= ee()->input->post('recipient_email');
			}
			else
			{
				$recipient_email	= array( ee()->input->post('recipient_email') );
			}

			

			// if we are using 'static recipients'. e.g., recipient1='bob|bob@email.com'
			// parse out the uniqids and replace them with the real stored emails
			if ( $this->_param('static_recipients') == TRUE )
			{
				//prevents injection and only uses hashed emails from the form
				$temp_email			= $recipient_email;
				$recipient_email 	= array();	
				
				//parse email
				$stored_recipients = $this->_param('static_recipients_list');
								
				//have to check each email against the entire list.
				foreach ( $temp_email as $key => $value )
				{
					foreach ( $stored_recipients as $recipient_data )
					{
						if ( $value == $recipient_data['key'] )
						{
							$recipient_email[] = $recipient_data['email'];
						}
					}
				}
			}

			//	----------------------------------------
			//	Validate recipients?
			//	----------------------------------------

			$array			= $this->_validate_recipients( implode( ",", $recipient_email ) );

			$error			= $array['error'];

			$approved_tos	= $array['approved'];
			
			//	----------------------------------------
			//	Over our spam limit?
			//	----------------------------------------

			if ( $this->_param('static_recipients') != TRUE AND 
				 count( $approved_tos ) > $this->_param( 'recipient_limit' ) )
			{
				$error[]	= ee()->lang->line( 'recipient_limit_exceeded' );
			}

			//	----------------------------------------
			//	Errors?
			//	----------------------------------------

			if ( count( $error ) > 0 )
			{
				return ee()->output->show_user_error( 'general', $error );
			}

			//	----------------------------------------
			//	Check for spamming or hacking
			//	----------------------------------------

			$query	= ee()->db->query( 
				"SELECT 	SUM(exp_freeform_user_email.email_count) AS count 
				 FROM 		exp_freeform_entries, exp_freeform_user_email 
				 WHERE		exp_freeform_entries.entry_id   = exp_freeform_user_email.entry_id
				 AND 		exp_freeform_entries.ip_address = '" . ee()->db->escape_str( ee()->input->ip_address() )."' 
				 AND 		exp_freeform_entries.entry_date > '" . ee()->db->escape_str( 
					ee()->localize->now - ( 60 * ( (int) $this->prefs['spam_interval'] ) ) 
				) . "'" 
			);

			if ( $query->row('count') > $this->prefs['spam_count'] )
			{
				return ee()->email->output->show_user_error(
					'general', array(ee()->lang->line('em_limit_exceeded')));
			}

			//	----------------------------------------
			//	Log the number of emails sent
			//	----------------------------------------

			ee()->db->query( 
				ee()->db->insert_string( 
					"exp_freeform_user_email", 
					array( 
						'email_count' 	=> count( $approved_tos ) ,
						'entry_id' 		=> $this->entry_id 
					) 
				)
			);

			//	----------------------------------------
			//	Generate message
			//	----------------------------------------
			
			$msg		= array();
			
			$query		= ee()->db->query(
				"SELECT * 
				 FROM 	exp_freeform_templates 
				 WHERE 	template_name = '" . ee()->db->escape_str($template) . "' 
				 AND 	enable_template = 'y' 
				 LIMIT 	1"
			);

			if ( $query->num_rows() == 0 )
			{
				return ee()->output->show_user_error('general', array(ee()->lang->line('template_not_available')));
			}
			
			$msg['from_name']	= ( $query->row('data_from_name') != '' ) ?
			 							$query->row('data_from_name'): ee()->config->item('webmaster_name');

			$msg['from_email']	= ( $query->row('data_from_email') != '' ) ?
			 							$query->row('data_from_email'): ee()->config->item('webmaster_email');

			$msg['subject']		= $query->row('data_title');

			$msg['msg']			= $query->row('template_data');

			$wordwrap			= $this->check_yes($query->row('wordwrap'));
			
			$msg['subject']		= str_replace( 	LD.'entry_date'.RD, 
											   	ee()->localize->set_human_time(ee()->localize->now), 
												$msg['subject'] );
			
			$msg['msg']			= str_replace( 	LD.'entry_date'.RD, 
												ee()->localize->set_human_time(ee()->localize->now), 
												$msg['msg'] );
			
			$msg['subject']		= str_replace( 	LD.'freeform_entry_id'.RD, $this->entry_id, $msg['subject'] );
			$msg['msg']			= str_replace( 	LD.'freeform_entry_id'.RD, $this->entry_id, $msg['msg'] );
			
			if (preg_match_all("/".LD."(entry_date)\s+format=([\"'])(.*?)\\2".RD."/is", 
							   $msg['subject'].$msg['msg'], $matches)
			   )
			{
				for ($j = 0; $j < count($matches[0]); $j++)
				{	
					$val = $matches[3][$j];
					
					foreach (ee()->localize->fetch_date_params($matches[3][$j]) AS $dvar)
					{
						$val = str_replace($dvar, ee()->localize->convert_timestamp($dvar, ee()->localize->now, TRUE), $val);					
					}
					
					$msg['subject']		= str_replace( $matches[0][$j], $val, $msg['subject'] );
			
					$msg['msg']			= str_replace( $matches[0][$j], $val, $msg['msg'] );
				}
			}
			
			//	----------------------------------------
			//	Parse conditionals
			//	----------------------------------------
			
			//template isn't defined yet, so we have to fetch it
			//1.x
			if(APP_VER < 2.0)
			{
				if ( ! class_exists('Template'))
				{
					require PATH_CORE.'core.template'.EXT;
				}
			
				$local_TMPL	= new Template();
			}
			//2.x
			else
			{
				ee()->load->library('template');
				$local_TMPL =& ee()->template;
			}
			
			$data['attachment_count']		= count( $this->attachments );
						
			$cond		= $data;
			
			foreach( $msg as $key => $val )
			{
				$msg[$key]	= $local_TMPL->advanced_conditionals( 
					ee()->functions->prep_conditionals( $msg[$key], $cond ) 
				);
			}

			unset( $cond );

			//	----------------------------------------
			//	Parse individual fields
			//	----------------------------------------
			
			$exclude	= array('submit');
			
			foreach ( $msg as $key => $val )
			{
				//	----------------------------------------
				//	Handle attachments
				//	----------------------------------------
				
				$msg[$key]	= str_replace( LD."attachment_count".RD, $data['attachment_count'], $msg[$key] );
						
				if ( $key == 'msg' )
				{
					$all_fields	.= "Attachments: ".$data['attachment_count']."\n";
					
					$n		= 0;
					
					foreach ( $this->attachments as $file )
					{
						$n++;						
						$all_fields	.= "Attachment $n: ".$file['filename']." ".$this->upload['url'].$file['filename']."\n";
					}
				}
				
				if ( preg_match( "/".LD."attachments".RD."(.*?)".LD."\/attachments".RD."/s", $msg[$key], $match ) )
				{
					if ( count( $this->attachments ) > 0 )
					{
						$str	= '';
						
						foreach ( $this->attachments as $file )
						{
							$tagdata	= $match['1'];
							$tagdata	= str_replace( LD."fileurl".RD, $this->upload['url'].$file['filename'], $tagdata );
							$tagdata	= str_replace( LD."filename".RD, $file['filename'], $tagdata );
							$str		.= $tagdata;
						}
						
						$msg[$key]	= str_replace( $match['0'], $str, $msg[$key] );
					}
					else
					{
						$msg[$key]	= str_replace( $match['0'], "", $msg[$key] );
					}
				}
				
				//	----------------------------------------
				//	Loop
				//	----------------------------------------
				
				foreach ( $fields as $name => $label )
				{
					if ( isset( $data[$name] ) AND ! in_array( $name, $exclude ) )
					{
						$msg[$key]	= str_replace( LD.$name.RD, $data[$name], $msg[$key] );
						
						//	----------------------------------------
						//	We don't want to concatenate for every
						//	time through the main loop.
						//	----------------------------------------
						
						if ( $key == 'msg' )
						{
							$all_fields	.= $label.": ".$data[$name]."\n";
						}
					}
					else
					{
						$msg[$key]	= str_replace( LD.$name.RD, '', $msg[$key] );
					}
				}
			}
			
			
			//	----------------------------------------
			//	Parse all fields variable
			//	----------------------------------------
			
			if ( stristr( $msg['msg'], LD.'all_custom_fields'.RD ) )
			{
				$msg['msg']	= str_replace( LD.'all_custom_fields'.RD, $all_fields, $msg['msg'] );
			}
			
			
			//	----------------------------------------
			//	'freeform_recipient_email' hook.
			//	 - This allows developers to alter the 
			//	   $msg array before admin notification is sent.
			//	----------------------------------------
			
			if (ee()->extensions->active_hook('freeform_recipient_email') === TRUE)
			{
				$msg = ee()->extensions->universal_call('freeform_recipient_email', $fields, $this->entry_id, $msg);
				if (ee()->extensions->end_script === TRUE) return;
			}
			//	----------------------------------------
			
			//	----------------------------------------
			//	Send email
			//	----------------------------------------
			
			ee()->email->wordwrap	= $wordwrap;
			ee()->email->mailtype	= ( $this->check_yes($query->row('html')) ) ? 'html': 'text';
			
			if ( count( $this->attachments ) > 0 AND $this->check_yes($this->_param('send_attachment')) )
			{
				foreach ( $this->attachments as $file_name )
				{
					ee()->email->attach( $file_name['filepath'] );
				}
				
				ee()->db->query( 
					ee()->db->update_string( 
						'exp_freeform_attachments', 
						array( 'emailed' 	=> 'y' ), 
						array( 'entry_id' 	=> $this->entry_id ) 
					) 
				);
			}
			
			foreach ($approved_tos as $val)
			{								
				ee()->email->initialize();
				ee()->email->from($msg['from_email'], $msg['from_name']);	
				ee()->email->to($val); 
				ee()->email->subject($msg['subject']);	
				ee()->email->message(entities_to_ascii($msg['msg']));						
				ee()->email->send();
				
			}
			ee()->email->clear(TRUE);

			$msg = array();
		
			//	----------------------------------------
			//	Register the template used
			//	----------------------------------------
			
			ee()->db->query( 
				ee()->db->update_string( 
					'exp_freeform_entries', 
					array( 'template' 	=> $template), 
					array( 'entry_id' 	=> $this->entry_id ) 
				) 
			);
		}
		
		//	End send user recipients
				
		
        //	----------------------------------------
        //	Send user email
        //	----------------------------------------
        
        //$msg = array(); email_change
        
        if ( $this->check_yes($this->_param('send_user_email')) AND ee()->input->get_post('email') )
        {
        	$all_fields		= '';
        	
        	$recipients		= array();
        	
        	$recipients[]	= ee()->input->get_post('email');
        	
        	$template	= ( $this->_param('user_email_template') AND $this->_param('user_email_template') != '' ) ?
 								$this->_param('user_email_template'): 'default_template';
		
			//	----------------------------------------
			//	Generate message
			//	----------------------------------------
			
			$msg = array();
			
			$query		= ee()->db->query(
				"SELECT * 
				 FROM 	exp_freeform_templates 
				 WHERE 	template_name = '" . ee()->db->escape_str($template) . "' 
				 AND 	enable_template = 'y' 
				 LIMIT 	1"
			);

			if ( $query->num_rows() == 0 )
			{
				return ee()->output->show_user_error('general', array(ee()->lang->line('template_not_available')));
			}
			
			$msg['from_name']	= ( $query->row('data_from_name') != '' ) ?
			 							$query->row('data_from_name') : ee()->config->item('webmaster_name');

			$msg['from_email']	= ( $query->row('data_from_email') != '' ) ?
			 							$query->row('data_from_email') : ee()->config->item('webmaster_email');

			$msg['subject']		= $query->row('data_title');

			$msg['msg']			= $query->row('template_data');

			$wordwrap			= ( $this->check_yes($query->row('wordwrap')) ) ? TRUE: FALSE;
			
			$msg['subject']		= str_replace( 	LD.'entry_date'.RD, 	
											   	ee()->localize->set_human_time(ee()->localize->now), 
												$msg['subject'] );
			
			$msg['msg']			= str_replace( 	LD.'entry_date'.RD, 
												ee()->localize->set_human_time(ee()->localize->now), 
												$msg['msg'] );
			
			$msg['subject']		= str_replace( LD.'freeform_entry_id'.RD, $this->entry_id, $msg['subject'] );
			$msg['msg']			= str_replace( LD.'freeform_entry_id'.RD, $this->entry_id, $msg['msg'] );
		
			/* email_change*/
			if (preg_match_all("/".LD."(entry_date)\s+format=([\"'])(.*?)\\2".RD."/is", $msg['subject'].$msg['msg'], $matches))
			{
				for ($j = 0; $j < count($matches[0]); $j++)
				{	
					$val = $matches[3][$j];
					
					foreach (ee()->localize->fetch_date_params($matches[3][$j]) AS $dvar)
					{
						$val = str_replace(	$dvar, 
											ee()->localize->convert_timestamp($dvar, ee()->localize->now, TRUE), 
											$val);					
					}
					
					$msg['subject']		= str_replace( $matches[0][$j], $val, $msg['subject'] );
			
					$msg['msg']			= str_replace( $matches[0][$j], $val, $msg['msg'] );
				}
			}
			
			//	----------------------------------------
			//	Parse conditionals
			//	----------------------------------------
		
			//template isn't defined yet, so we have to fetch it
			//1.x
			if(APP_VER < 2.0)
			{
				if ( ! class_exists('Template'))
				{
					require PATH_CORE.'core.template'.EXT;
				}
			
				$local_TMPL	= new Template();
			}
			//2.x
			else
			{
				ee()->load->library('template');
				$local_TMPL =& ee()->template;
			}
			
			$data['attachment_count']		= count( $this->attachments );
			
			$cond							= $data;
			
			foreach( $msg as $key => $val )
			{
				$msg[$key]	= $local_TMPL->advanced_conditionals( 
					ee()->functions->prep_conditionals( $msg[$key], $cond ) 
				);
			}

			unset( $cond );

			//	----------------------------------------
			//	Parse individual fields
			//	----------------------------------------
			
			$exclude	= array('submit');
			
			foreach ( $msg as $key => $val )
			{
				//	----------------------------------------
				//	Handle attachments
				//	----------------------------------------
				
				$msg[$key]	= str_replace( LD."attachment_count".RD, $data['attachment_count'], $msg[$key] );
						
				if ( $key == 'msg' )
				{
					$all_fields	.= "Attachments: ".$data['attachment_count']."\n";
					
					$n		= 0;
					
					foreach ( $this->attachments as $file )
					{
						$n++;						
						$all_fields	.= "Attachment $n: ".$file['filename']." ".$this->upload['url'].$file['filename']."\n";
					}
				}
				
				if ( preg_match( "/".LD."attachments".RD."(.*?)".LD."\/attachments".RD."/s", $msg[$key], $match ) )
				{
					if ( count( $this->attachments ) > 0 )
					{
						$str	= '';
						
						foreach ( $this->attachments as $file )
						{
							$tagdata	= $match['1'];
							$tagdata	= str_replace( LD."fileurl".RD, $this->upload['url'].$file['filename'], $tagdata );
							$tagdata	= str_replace( LD."filename".RD, $file['filename'], $tagdata );
							$str		.= $tagdata;
						}
						
						$msg[$key]	= str_replace( $match['0'], $str, $msg[$key] );
					}
					else
					{
						$msg[$key]	= str_replace( $match['0'], "", $msg[$key] );
					}
				}
				
				//	----------------------------------------
				//	Loop
				//	----------------------------------------
				
				foreach ( $fields as $name => $label )
				{
					if ( isset( $data[$name] ) AND ! in_array( $name, $exclude ) )
					{
						$msg[$key]	= str_replace( LD.$name.RD, $data[$name], $msg[$key] );
						
						//	----------------------------------------
						//	We don't want to concatenate for every
						//	time through the main loop.
						//	----------------------------------------
						
						if ( $key == 'msg' )
						{
							$all_fields	.= $label.": ".$data[$name]."\n";
						}
					}
					else
					{
						$msg[$key]	= str_replace( LD.$name.RD, '', $msg[$key] );
					}
				}
			}
			
			
			//	----------------------------------------
			//	Parse all fields variable
			//	----------------------------------------
			
			if ( stristr( $msg['msg'], LD.'all_custom_fields'.RD ) )
			{
				$msg['msg']	= str_replace( LD.'all_custom_fields'.RD, $all_fields, $msg['msg'] );
			}
			
			//	----------------------------------------
			//	'freeform_module_user_notification' hook.
			//	 - This allows developers to alter the $msg array before user notification is sent.
			//	----------------------------------------
			
			if (ee()->extensions->active_hook('freeform_module_user_notification') === TRUE)
			{
				$msg = ee()->extensions->universal_call('freeform_module_user_notification', $fields, $this->entry_id, $msg);
				if (ee()->extensions->end_script === TRUE) return;
			}
			//	----------------------------------------
		
			//	----------------------------------------
			//	Send email
			//	----------------------------------------
			
			//ee()->load->library('email');
			ee()->email->wordwrap	= $wordwrap;
			ee()->email->mailtype	= ( $this->check_yes($query->row('html')) ) ? 'html': 'text';
			
			if ( count( $this->attachments ) > 0 AND $this->check_yes($this->_param('send_user_attachment')) )
			{
				foreach ( $this->attachments as $file_name )
				{
					ee()->email->attach( $file_name['filepath'] );
				}
				
				ee()->db->query( 
					ee()->db->update_string( 
						'exp_freeform_attachments', 
						array( 'emailed' => 'y' ), 
						array( 'entry_id' => $this->entry_id ) 
					) 
				);
			}
			
			foreach ($recipients as $val)
			{								
				ee()->email->initialize();
				ee()->email->from($msg['from_email'], $msg['from_name']);	
				ee()->email->to($val); 
				ee()->email->subject($msg['subject']);	
				ee()->email->message(entities_to_ascii($msg['msg']));		
				ee()->email->send();
			}
			
			$msg = array();
			ee()->email->clear(TRUE);
		}
		
		//	End send user email 
		
		
		//	----------------------------------------
		//	Subscribe to mailing lists
		//	----------------------------------------
		
		if ( ee()->input->get_post('mailinglist') )
		{			
			if ( ee()->db->table_exists('exp_mailing_lists') )
			{
				//	----------------------------------------
				//	Do we have an email?
				//	----------------------------------------
				
				if ( $email = ee()->input->get_post('email') )
				{
					//	----------------------------------------
					//	Explode mailinglist parameter
					//	----------------------------------------
					
					if ( is_array( ee()->input->post('mailinglist') ) )
					{
						$lists	= implode( "','", ee()->db->escape_str(ee()->input->post('mailinglist')));
					}
					else
					{
						$lists	= ee()->db->escape_str(ee()->input->post('mailinglist'));
					}
					
					//	----------------------------------------
					//	Get lists
					//	----------------------------------------
					
					$subscribed	= '';
					
					$sub	= ee()->db->query( 
						"SELECT list_id 
						 FROM exp_mailing_list 
						 WHERE email = '" . ee()->db->escape_str($email) . "' 
						 GROUP BY list_id"
					);

					if ( $sub->num_rows() > 0 )
					{
						foreach( $sub->result_array() as $row )
						{
							$subscribed[] = $row['list_id'];
						}
						
						$subscribed	= " AND list_id NOT IN (".implode(',', $subscribed).") ";
					}
					
					$query	= ee()->db->query( 
						"SELECT DISTINCT 	list_id, list_title 
						 FROM 				exp_mailing_lists 
						 WHERE 				( list_id IN ('" . $lists . "') OR 
						 					  list_name IN ('" . $lists . "') ) " . $subscribed
					);
					
					if ( $query->num_rows() > 0 AND $query->num_rows() < 50 )
					{				
						// Kill duplicate emails from authorization queue.  This prevents an error if a user
						// signs up but never activates their email, then signs up again.
						
						ee()->db->query(
							"DELETE FROM 	exp_mailing_list_queue 
							 WHERE 			email = '" . ee()->db->escape_str($email) . "'"
						);
					
						foreach ( $query->result_array() as $row )
						{
							//	----------------------------------------
							//	Insert email
							//	----------------------------------------
									
							$code	= ee()->functions->random('alpha', 10);
							
							if (  $this->check_no(ee()->input->get_post('mailinglist_opt_in')) )
							{
								ee()->db->query(
									ee()->db->insert_string(	
										'exp_mailing_list',
										array(	
											'user_id'		=> '',
											'list_id'		=> $row['list_id'],
											'authcode'		=> $code,
											'email'			=> $email,
											'ip_address'	=> ee()->input->ip_address()
										)
									)
								);
														
								// ----------------------------------------
								//  Is there an admin notification to send?
								// ----------------------------------------
						
								if ($this->check_yes(ee()->config->item('mailinglist_notify'))  AND
								    ee()->config->item('mailinglist_notify_emails') != '')
								{
									$query = ee()->db->query(
										"SELECT list_title 
										 FROM 	exp_mailing_lists 
										 WHERE 	list_id = '" . ee()->db->escape_str($row['list_id']) . "'"
									);
								
									$swap = array(
										'email'			=> $email,
										'mailing_list'	=> $query->row('list_title')
									 );
									
									$template = ee()->functions->fetch_email_template('admin_notify_mailinglist');
									$email_tit = ee()->functions->var_swap($template['title'], $swap);
									$email_msg = ee()->functions->var_swap($template['data'], $swap);
																		
									// ----------------------------
									//  Send email
									// ----------------------------
						
									$notify_address = $this->remove_extra_commas(
										ee()->config->item('mailinglist_notify_emails')
									);
									
									if ($notify_address != '')
									{				
										// ----------------------------
										//  Send email
										// ----------------------------
										
										//ee()->load->library('email');
										
										foreach (explode(',', $notify_address) as $addy)
										{
											ee()->email->initialize();
											ee()->email->wordwrap = true;
											ee()->email->from(
												ee()->config->item('webmaster_email'), 
												ee()->config->item('webmaster_name')
											);	
											ee()->email->to($addy); 
											ee()->email->reply_to(ee()->config->item('webmaster_email'));
											ee()->email->subject($email_tit);	
											ee()->email->message(entities_to_ascii($email_msg));		
											ee()->email->Send();
										}
										ee()->email->clear(TRUE);
									}
								}
							}        
							else
							{        	
								ee()->db->query(
									"INSERT INTO exp_mailing_list_queue (email, list_id, authcode, date) 
									 VALUES ('" . ee()->db->escape_str($email) . "', '" . 
									 			  ee()->db->escape_str($row['list_id']) ."', '" . 
												  ee()->db->escape_str($code) . "', '" . time() . "')"
									);
								
								$this->send_email_confirmation($email, $row, $code);
							}
						}
					}
				}
			}
		}
		
		//	End subscribe to mailinglists 
		
		//	----------------------------------------
		//	'freeform_module_insert_end' hook.
		//	 - This allows developers to do one last thing before Freeform submit is ended.
		//	----------------------------------------
		
		if (ee()->extensions->active_hook('freeform_module_insert_end') === TRUE)
		{
			$edata = ee()->extensions->universal_call('freeform_module_insert_end', $fields, $this->entry_id, $msg);
			if (ee()->extensions->end_script === TRUE) return;
		}
        //	----------------------------------------
		
		//	----------------------------------------
		//	Set return
		//	----------------------------------------
        
        if ( ! $return = ee()->input->get_post('return') )
        {
        	$return	= ee()->input->get_post('RET');
        }
		
		if ( preg_match( "/".LD."\s*path=(.*?)".RD."/", $return, $match ) > 0 )
		{
			$return	= ee()->functions->create_url( $match['1'] );
		}
		elseif ( stristr( $return, "http://" ) === FALSE && stristr( $return, "https://" ) === FALSE )
		{
			$return	= ee()->functions->create_url( $return );
		}
		
		$return	= str_replace( "%%entry_id%%", $this->entry_id, $return );
		
		$return	= $this->_chars_decode( $return );
				
        //	----------------------------------------
        //	Return the user
        //	----------------------------------------

        if ( $return != '' )
        {
			ee()->functions->redirect( $return );
        }
        else
        {
        	ee()->functions->redirect( ee()->functions->fetch_site_index() );
        }
		
		exit;
    }
    
    //	End insert 
    
	
    // --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	(string) email 	- valid email address to send confirmation to
	 * @param 	(array)  row	- row from db query from mailing list
	 * @param	(string) code	- confirmation code for email 
	 * @return	null
	 */

	function send_email_confirmation($email, $row, $code)
	{
		if ( ! is_array($row) OR ! isset($row['list_title']))
		{
			return FALSE;
		}
        
        $qs			= ( $this->check_yes(ee()->config->item('force_query_string')) ) ? '' : '?';        
		$action_id  = ee()->functions->insert_action_ids(ee()->functions->fetch_action_id('Mailinglist', 'authorize_email'));

		$swap		= array(
			'activation_url'	=> ee()->functions->fetch_site_index(0, 0).$qs.'ACT='.$action_id.'&id='.$code,
			'site_name'			=> stripslashes(ee()->config->item('site_name')),
			'site_url'			=> ee()->config->item('site_url'),
			'mailing_list'		=> $row['list_title']
		);
		
		foreach ( $row as $key => $val )
		{
			$swap[$key]	= $val;
		}
		
		$template	= ee()->functions->fetch_email_template('mailinglist_activation_instructions');
		$email_tit	= ee()->functions->var_swap($template['title'], $swap);
		$email_msg	= ee()->functions->var_swap($template['data'], $swap);
		
		//	----------------------------------------
		//	Send email
		//	----------------------------------------
		
		//ee()->load->library('email');
		
		ee()->email->initialize();        
		ee()->email->wordwrap = true;
		ee()->email->mailtype = 'plain';
		ee()->email->priority = '3';		
		ee()->email->from(ee()->config->item('webmaster_email'), ee()->config->item('webmaster_name'));	
		ee()->email->to($email); 
		ee()->email->subject($email_tit);	
		ee()->email->message($email_msg);	
		ee()->email->Send();
		
		//cleanup, ilse 4
		ee()->email->clear(TRUE);
	}
	
	//	End confirmation email 
    
    
    // --------------------------------------------------------------------

	/**
	 * freeform entries tag for template display
	 *
	 * @access	public
	 * @return	rendered template data
	 */

    function entries()
    {
		//	----------------------------------------
		//	Trigger benchmarking for performance
		//	tracking
		//	----------------------------------------
		
		ee()->TMPL->log_item('Freeform Module: Prep Query');
		
		//	----------------------------------------
		//	Dynamic
		//	----------------------------------------
		
		$this->dynamic	= ! $this->check_no(ee()->TMPL->fetch_param('dynamic'));
    	
		//	----------------------------------------
		//	Get entries
		//	----------------------------------------
		
		$sql	= "SELECT 	* 
				   FROM 	exp_freeform_entries 
				   WHERE 	entry_date != ''";
						
		//	----------------------------------------
		//	Entry id
		//	----------------------------------------
		
		//if the passed entry ID isn't something we can use,
		//send no results
		if ( ee()->TMPL->fetch_param('entry_id') !== FALSE AND 
			 ! is_numeric( trim(ee()->TMPL->fetch_param('entry_id')) ) )
		{
			return ee()->TMPL->no_results();
		}
		elseif ( $this->_entry_id() === TRUE )
		{
			$sql	.= " AND entry_id = '".ee()->db->escape_str($this->entry_id)."'";
		}
		
		if ( ee()->TMPL->fetch_param('collection') !== FALSE )
		{
			$sql	.= " AND form_name = '".ee()->db->escape_str(ee()->TMPL->fetch_param('collection'))."'";
		}		
		elseif ( ee()->TMPL->fetch_param('form_name') !== FALSE )
		{
			$sql	.= " AND form_name = '".ee()->db->escape_str(ee()->TMPL->fetch_param('form_name'))."'";
		}
		
		if ( ee()->TMPL->fetch_param('status') !== FALSE )
		{
			$stats	= preg_split( "/,|\|/", ee()->TMPL->fetch_param('status') );
			
			$arr	= array_intersect( array('open','closed'), $stats );
			
			if ( count($arr) > 0 )
			{
				$sql	.= " AND status IN ('".implode( "','", $arr )."')";
			}
			else
			{
				$sql	.= " AND status = 'open'";
			}
		}
		else
		{
			$sql	.= " AND status = 'open'";
		}
		
		if ( ee()->TMPL->fetch_param('orderby') !== FALSE AND 
			 $this->_column_exists( ee()->TMPL->fetch_param('orderby'), 'exp_freeform_entries' )
		   )
		{
			$sql	.= " ORDER BY " . ee()->TMPL->fetch_param('orderby');
		}
		else
		{
			$sql	.= " ORDER BY entry_date";
		}		
		
		$sql	.= ( strtolower(ee()->TMPL->fetch_param('sort')) == 'asc' ) ? ' ASC': ' DESC';
		
		if ( ee()->TMPL->fetch_param('limit') !== FALSE )
		{
			$sql	.= " LIMIT " . ee()->TMPL->fetch_param('limit');
		}
		else
		{
			$sql	.= " LIMIT 100";
		}
		
		//	----------------------------------------
		//	Run query
		//	----------------------------------------
		
		ee()->TMPL->log_item('Freeform Module: Run Query');
		
		$query	= ee()->db->query($sql);
    	
		//	----------------------------------------
		//	Results?
		//	----------------------------------------
		
		if ( $query->num_rows() == 0 )
		{
			return ee()->TMPL->no_results();
		}
    	
		//	----------------------------------------
		//	Grab attachments
		//	----------------------------------------
		
		ee()->TMPL->log_item('Freeform Module: Loop Query');
		
		$entry_ids	= array();
		
		foreach ( $query->result_array() as $row )
		{
			$entry_ids[]	= $row['entry_id'];
		}
		
		$attachmentsq	= ee()->db->query( 
			"SELECT 	a.*, CONCAT( p.url, a.filename, a.extension ) AS fileurl 
			 FROM 		exp_freeform_attachments a 
			 LEFT JOIN 	exp_upload_prefs p 	ON p.id = a.pref_id 
			 WHERE 		a.entry_id 			IN ('".implode( "','", $entry_ids )."')" 
		);
		
		$attachments	= array();
		
		foreach( $attachmentsq->result_array() as $row )
		{
			$attachments[ $row['entry_id'] ][]	= $row;
		}
		
        //	----------------------------------------
        //	Fetch all the date-related variables
        //	----------------------------------------
        
        $entry_date 		= array();
        $gmt_entry_date		= array();
        $edit_date 			= array();
        $gmt_edit_date		= array();
        
        // We do this here to avoid processing cycles in the foreach loop
        
        $date_vars = array('entry_date', 'gmt_entry_date', 'edit_date', 'gmt_edit_date');
                
		foreach ($date_vars as $val)
		{
			if (preg_match_all("/".LD.$val."\s+format=[\"'](.*?)[\"']".RD."/s", ee()->TMPL->tagdata, $matches))
			{
				for ($j = 0; $j < count($matches['0']); $j++)
				{
					$matches['0'][$j] = str_replace(array(LD,RD), '', $matches['0'][$j]);
					
					switch ($val)
					{
						case 'entry_date' 		: $entry_date[$matches['0'][$j]] = ee()->localize->fetch_date_params($matches['1'][$j]);
							break;
						case 'gmt_entry_date'	: $gmt_entry_date[$matches['0'][$j]] = ee()->localize->fetch_date_params($matches['1'][$j]);
							break;
						case 'edit_date' 		: $edit_date[$matches['0'][$j]] = ee()->localize->fetch_date_params($matches['1'][$j]);
							break;
						case 'gmt_edit_date'	: $gmt_edit_date[$matches['0'][$j]] = ee()->localize->fetch_date_params($matches['1'][$j]);
							break;
					}
				}
			}
		}
    	
		//	----------------------------------------
		//	Parse
		//	----------------------------------------
		
		$return			= '';
		
		$count			= 1;
		
		$reverse_count	= $query->num_rows();
		
		foreach ( $query->result_array() as $row )
		{
			$row['count']			= $count++;
			
			$row['reverse_count']	= $reverse_count--;
			
			$row['total_entries']	= $query->num_rows();

			$row['collection_name'] = $row['form_name'];

			$tagdata				= ee()->TMPL->tagdata;
			
			//	----------------------------------------
			//	Conditionals
			//	----------------------------------------
			
			$cond			= $row;
			
			$tagdata		= ee()->functions->prep_conditionals( $tagdata, $cond );
			
			//	----------------------------------------
			//	Var pairs
			//	----------------------------------------
			
			foreach ( ee()->TMPL->var_pair as $key => $val )
			{				
				$out		= '';
				
				if ( $key == 'attachments' )
				{
					if ( isset( $attachments[ $row['entry_id'] ] ) )
					{
						preg_match( '/' . LD . $key . RD . "(.*?)" . LD . preg_quote(T_SLASH, '/') . $key . RD . '/s', $tagdata, $match );
						
						$r	=	'';
						
						foreach ( $attachments[ $row['entry_id'] ] as $att )
						{
							$str	= $match['1'];
							
							foreach ( $att as $k => $v )
							{
								$str	= str_replace( LD.$k.RD, $v, $str );
							}
							
							$r	.= $str;
						}
						
						$tagdata	= str_replace( $match['0'], $r, $tagdata );
					}
					else
					{
						$tagdata	= ee()->TMPL->delete_var_pairs( $key, $key, $tagdata );
					}
				}
			}
			
			//	----------------------------------------
			//	Single Vars
			//	----------------------------------------
			
			foreach ( ee()->TMPL->var_single as $key => $val )
			{
				//	----------------------------------------
				//	parse {attachment_count} variable
				//	----------------------------------------
				
				if ( $key == 'attachment_count' )
				{
					if ( isset( $attachments[ $row['entry_id'] ] ) )
					{
						$tagdata = ee()->TMPL->swap_var_single($key, count( $attachments[ $row['entry_id'] ] ), $tagdata);
					}
					else
					{
						$tagdata = ee()->TMPL->swap_var_single($key, '0', $tagdata);
					}
				}
				
				//	----------------------------------------
				//	parse {switch} variable
				//	----------------------------------------
				
				if (preg_match("/^switch\s*=.+/i", $key))
				{
					$sparam = ee()->functions->assign_parameters($key);
					
					$sw = '';
	
					if (isset($sparam['switch']))
					{
						$sopt = explode("|", $sparam['switch']);
						
						if (count($sopt) == 2)
						{
							if (isset($switch[$sparam['switch']]) AND $switch[$sparam['switch']] == $sopt['0'])
							{
								$switch[$sparam['switch']] = $sopt['1'];
								
								$sw = $sopt['1'];									
							}
							else
							{
								$switch[$sparam['switch']] = $sopt['0'];
								
								$sw = $sopt['0'];									
							}
						}
					}
					
					$tagdata = ee()->TMPL->swap_var_single($key, $sw, $tagdata);
				}
                                
                //	----------------------------------------
                //	parse entry date
                //	----------------------------------------
                
                if (isset($entry_date[$key]))
                {
					foreach ($entry_date[$key] as $dvar)
					{
						$val = str_replace(
							$dvar, 
							ee()->localize->convert_timestamp($dvar, $row['entry_date'], TRUE), 
							$val
						);
					}					

					$tagdata = ee()->TMPL->swap_var_single($key, $val, $tagdata);					
                }
            
                //	----------------------------------------
                //	GMT date - entry date in GMT
                //	----------------------------------------
                
                if (isset($gmt_entry_date[$key]))
                {
					foreach ($gmt_entry_date[$key] as $dvar)
					{
						$val = str_replace(
							$dvar, 
							ee()->localize->convert_timestamp($dvar, $row['entry_date'], FALSE), 
							$val
						);
					}					

					$tagdata = ee()->TMPL->swap_var_single($key, $val, $tagdata);					
                }
                
                if (isset($gmt_date[$key]))
                {
					foreach ($gmt_date[$key] as $dvar)
					{	
						$val = str_replace(
							$dvar, 
							ee()->localize->convert_timestamp($dvar, $row['entry_date'], FALSE), 
							$val
						);
					}					

					$tagdata = ee()->TMPL->swap_var_single($key, $val, $tagdata);					
                }
                                
                //	----------------------------------------
                //	parse edit date
                //	----------------------------------------
                
                if (isset($edit_date[$key]))
                {
					foreach ($edit_date[$key] as $dvar)
					{	
						$val = str_replace(
							$dvar, 
							ee()->localize->convert_timestamp(
								$dvar, 
								ee()->localize->timestamp_to_gmt($row['edit_date']), 
								TRUE
							), 
							$val
						);
					}					

					$tagdata = ee()->TMPL->swap_var_single($key, $val, $tagdata);					
                }
                
                //	----------------------------------------
                //	Edit date as GMT
                //	----------------------------------------
                
                if (isset($gmt_edit_date[$key]))
                {
					foreach ($gmt_edit_date[$key] as $dvar)
					{	
						$val = str_replace(
							$dvar, 
							ee()->localize->convert_timestamp(
								$dvar, 
								ee()->localize->timestamp_to_gmt($row['edit_date']), 
								FALSE
							), 
							$val
						);
					}					

					$tagdata = ee()->TMPL->swap_var_single($key, $val, $tagdata);					
                }
                
                //	----------------------------------------
                //	Remaining variables
                //	----------------------------------------
			
				if ( isset( $row[$key] ) )
				{
					$tagdata	= ee()->TMPL->swap_var_single( $key, $row[$key], $tagdata );
				}
			}
			
			//	----------------------------------------
			//	Concat
			//	----------------------------------------
			
			$return	.=	$tagdata;			
		}
			
		//	----------------------------------------
		//	Return
		//	----------------------------------------
		
		ee()->TMPL->log_item('Freeform Module: Return Data');
		
		return $return;
    }
    //	End entries 
    
    
    // --------------------------------------------------------------------

	/**
	 * count tag - gets different count data for freeform entries
	 *
	 * @access	public
	 * @return	parsed template data
	 */

    function count()
    {
		//	----------------------------------------
		//	Date fields
		//	----------------------------------------
		
		$date		= array('entry_date', 'edit_date');
    	
		//	----------------------------------------
		//	Primary fields
		//	----------------------------------------
		
		$primary	= array(
			'entry_id', 
			'group_id', 
			$this->sc->db->id, 
			'author_id', 
			'ip_address', 
			'form_name', 
			'template', 
			'status' 
		);
    	
		//	----------------------------------------
		//	Custom fields
		//	----------------------------------------
		
		$custom		= array();
				
		$query		= ee()->db->query("SELECT name FROM exp_freeform_fields");
		
		if ( $query->num_rows() > 0 )
		{
			foreach( $query->result_array() as $row )
			{
				$custom[]	= $row['name'];
			}
		}
		
		//	----------------------------------------
		//	Merge
		//	----------------------------------------
		
		$fields	= array_merge( $primary, $custom );
    	
		//	----------------------------------------
		//	Assemble
		//	----------------------------------------
    	
    	$sql	= "SELECT 	COUNT(*) AS count 
				   FROM 	exp_freeform_entries 
				   WHERE 	entry_id != ''";
    	
		//	----------------------------------------
		//	Date fields
		//	----------------------------------------
		
		foreach ( $date as $key )
		{
			if ( $val = ee()->TMPL->fetch_param($key) )
			{
				if ( is_numeric( $val ) )
				{
					$sql .= " AND " . $key . " >= " . (ee()->localize->now - ($val * 60 * 60));
				}
			}
		}
    	
		//	----------------------------------------
		//	Fields
		//	----------------------------------------
		
		foreach ( $fields as $key )
		{
			//is this the 'collection' param instead of 'form_name'?
			if ($key == 'form_name' and in_array(ee()->TMPL->fetch_param('form_name'), array(FALSE, '')))
			{
				if ( ! in_array(ee()->TMPL->fetch_param('collection'), array(FALSE, '')) )
				{
					$sql	.= " AND form_name = '".ee()->db->escape_str(ee()->TMPL->fetch_param('collection'))."'";
					continue;
				}
			}
			
			if ( $val = ee()->TMPL->fetch_param($key) )
			{
				$sql	.= " AND " . $key . " = '" . ee()->db->escape_str($val) . "'";
			}
		}
    	
		//	----------------------------------------
		//	Query
		//	----------------------------------------
		
		$query	= ee()->db->query( $sql );
		
		//	----------------------------------------
		//	Output
		//	----------------------------------------
		
		return str_replace( LD.'count'.RD, $query->row('count'), ee()->TMPL->tagdata );
    }
    
    //	End count 
	
	
    // --------------------------------------------------------------------

	/**
	 * _entry_id, sets entry_id class var
	 *
	 * @access	public
	 * @param	(string) id - entry id
	 * @return	boolean
	 */
    
    function _entry_id( $id = 'entry_id' )
    {	
		$cat_segment	= ee()->config->item("reserved_category_word");
		
		if ( $this->entry_id != '' )
		{
			return TRUE;
		}    	
		elseif ( isset($GLOBALS['TMPL']) AND is_numeric( trim( ee()->TMPL->fetch_param($id) ) ) )
		{
			$this->entry_id	= trim( ee()->TMPL->fetch_param($id) );
			
			return TRUE;
		}
		elseif ( is_numeric( ee()->input->get_post($id) ) )
		{
			$this->entry_id	= ee()->input->get_post($id);
			
			return TRUE;
		}
		elseif ( ee()->uri->query_string != '' AND $this->dynamic )
		{
			$qstring	= ee()->uri->query_string;
			
			//	----------------------------------------
			//	Do we have a pure ID number?
			//	----------------------------------------
		
			if ( is_numeric( $qstring) )
			{
				$this->entry_id	= $qstring;
				
				return TRUE;
			}
			else
			{
				//	----------------------------------------
				//	Parse day
				//	----------------------------------------
				
				if (preg_match("#\d{4}/\d{2}/(\d{2})#", $qstring, $match))
				{											
					$partial	= substr($match['0'], 0, -3);
										
					$qstring	= trim_slashes(str_replace($match['0'], $partial, $qstring));
				}
				
				//	----------------------------------------
				//	Parse /year/month/
				//	----------------------------------------
										
				if (preg_match("#(\d{4}/\d{2})#", $qstring, $match))
				{					
					$qstring	= trim_slashes(str_replace($match['1'], '', $qstring));
				}				

				//	----------------------------------------
				//	Parse page number
				//	----------------------------------------
				
				if (preg_match("#^P(\d+)|/P(\d+)#", $qstring, $match))
				{					
					$qstring	= trim_slashes(str_replace($match['0'], '', $qstring));
				}

				//	----------------------------------------
				//	Parse category indicator
				//	----------------------------------------
				
				// Text version of the category
				
				if (preg_match("#^".$cat_segment."/#", $qstring, $match) AND ee()->TMPL->fetch_param($this->sc->channel))
				{		
					$qstring	= str_replace($cat_segment.'/', '', $qstring);
						
					$sql		= "SELECT DISTINCT 	cat_group 
								   FROM 			{$this->sc->db->channels} 
								   WHERE ";
					
					if ( defined('USER_BLOG') AND defined('UB_BLOG_ID') AND USER_BLOG !== FALSE)
					{
						$sql	.= " {$this->sc->db->id} ='" . UB_BLOG_ID . "'";
					}
					else
					{
						$xsql	= ee()->functions->sql_andor_string(
							ee()->TMPL->fetch_param($this->sc->channel), 
							$this->sc->db->channel_name
						);
						
						if (substr($xsql, 0, 3) == 'AND') $xsql = substr($xsql, 3);
						
						$sql	.= ' '.$xsql;
					}
						
					$query	= ee()->db->query($sql);
					
					if ($query->num_rows() == 1)
					{
						$result	= ee()->db->query(
							"SELECT cat_id 
							 FROM 	exp_categories 
							 WHERE 	cat_name='" . ee()->db->escape_str($qstring) . "' 
							 AND 	group_id='" . $query->row('cat_group') . "'"
						);
					
						if ($result->num_rows() == 1)
						{
							$qstring	= 'C' . $result->row('cat_id');
						}
					}
				}

				//	----------------------------------------
				//	Numeric version of the category
				//	----------------------------------------

				if (preg_match("#^C(\d+)#", $qstring, $match))
				{														
					$qstring	= trim_slashes(str_replace($match['0'], '', $qstring));
				}
				
				//	----------------------------------------
				//	Remove "N"
				//	----------------------------------------
				//	The recent comments feature uses "N" as
				//	the URL indicator
				//	It needs to be removed if present
				//	----------------------------------------

				if (preg_match("#^N(\d+)|/N(\d+)#", $qstring, $match))
				{					
					$qstring	= trim_slashes(str_replace($match['0'], '', $qstring));
				}
				
				//	----------------------------------------
				//	Try numeric id again
				//	----------------------------------------
				
				if ( preg_match( "/(\d+)/", $qstring, $match ) )
				{
					$this->entry_id	= $match['1'];
					
					return TRUE;
				}

				//	----------------------------------------
				//	Parse URL title
				//	----------------------------------------
				
				if (strstr($qstring, '/'))
				{
					$xe			= explode('/', $qstring);
					$qstring	= current($xe);
				}
				
				$sql	= "SELECT {$this->sc->db->titles}.entry_id 
						   FROM   {$this->sc->db->titles}, {$this->sc->db->channels} 
						   WHERE  {$this->sc->db->titles}.{$this->sc->db->id} = {$this->sc->db->channels}.{$this->sc->db->id}
						   AND    {$this->sc->db->titles}.url_title = '" . ee()->db->escape_str($qstring) . "'";
				
				//user blog is 1.6.x legacy and not defined in 2.x
				if (APP_VER < 2.0 AND defined('USER_BLOG') AND defined('UB_BLOG_ID') AND USER_BLOG !== FALSE)
				{
					$sql	.= " AND {$this->sc->db->titles}.{$this->sc->db->id} = '" . UB_BLOG_ID . "'";
				}
				//.is_user_blog
				elseif (APP_VER < 2.0 AND $this->_column_exists('is_user_blog', $this->sc->db->titles))
				{
					$sql	.= " AND {$this->sc->db->titles}.is_user_blog = 'n'";
				}
								
				$query	= ee()->db->query($sql);
				
				if ( $query->num_rows() > 0 )
				{
					$this->entry_id = $query->row('entry_id');
					
					return TRUE;
				}
			}
		}
		
		return FALSE;
	}
	
	//	End entry id 


	// --------------------------------------------------------------------

	/**
	 * _column_exists
	 *
	 * @access	public
	 * @param	string column name
	 * @param	string table name
	 * @return	null
	 */

	function _column_exists( $column, $table )
	{
		// ----------------------------------------
		// Check for columns in tags table
		// ----------------------------------------

		$query	= ee()->db->query( 
			"DESCRIBE `" . ee()->db->escape_str( $table )  . "` `" . 
						   ee()->db->escape_str( $column ) . "`" 
		);

		if ( $query->num_rows() > 0 )
		{
			return TRUE;
		}

		return FALSE;
	}
	// End _column_exists()
	
	
    // --------------------------------------------------------------------

	/**
	 * _form - builds form for template
	 *
	 * @access	public
	 * @param	(array) data - tagdata
	 * @return	tagdata
	 */
    
    function _form( $data = array() )
    {
    	if ( count( $data ) == 0 AND ! isset( $this->data ) ) {return '';}
    	
    	if ( ! isset( $this->data['tagdata'] ) OR $this->data['tagdata'] == '' )
    	{
			$tagdata	=	ee()->TMPL->tagdata;
    	}
    	else
    	{
    		$tagdata	= $this->data['tagdata'];
    		unset( $this->data['tagdata'] );
    	}

		//	----------------------------------------
		//	Insert params
		//	----------------------------------------
		
		if ( ! $this->params_id = $this->_insert_params() )
		{
			$this->params_id	= 0;
		}
		
		$this->data['params_id']	= $this->params_id;

		//	----------------------------------------
		//	Generate form
		//	----------------------------------------
		
		$arr	= array(
			'hidden_fields'	=> $this->data,
			'action'		=> ee()->functions->fetch_site_index(),
			'id'			=> ( ee()->TMPL->fetch_param('form_id') ) ? 
											ee()->TMPL->fetch_param('form_id') : 'freeform',
			'name'			=> $this->params['form_name'],
			'enctype'		=> ( $this->multipart ) ? 'multi': '',
			'onsubmit'		=> ( ee()->TMPL->fetch_param('onsubmit') ) ? ee()->TMPL->fetch_param('onsubmit'): ''
		);
						
		if ( ee()->TMPL->fetch_param('name') !== FALSE )
		{
			$arr['name']	= ee()->TMPL->fetch_param('name');
		}
		
		// --------------------------------------------
        //  HTTPS URLs?
        // --------------------------------------------
		
		if ( $this->check_yes(ee()->TMPL->fetch_param('secure_action')) )
		{
			if (isset($arr['action']))
			{
				$arr['action'] = str_replace('http://', 'https://', $arr['action']);
			}
		}
		
		if ( $this->check_yes(ee()->TMPL->fetch_param('secure_return')))
		{
			foreach(array('return', 'RET') as $return_field)
			{
				if (isset($arr['hidden_fields'][$return_field]))
				{
					if ( preg_match( "/".LD."\s*path=(.*?)".RD."/", $arr['hidden_fields'][$return_field], $match ) > 0 )
					{
						$arr['hidden_fields'][$return_field] = ee()->functions->create_url( $match['1'] );
					}
					elseif ( stristr( $arr['hidden_fields'][$return_field], "http://" ) === FALSE )
					{
						$arr['hidden_fields'][$return_field] = ee()->functions->create_url( $arr['hidden_fields'][$return_field] );
					}
				
					$arr['hidden_fields'][$return_field] = str_replace('http://', 'https://', $arr['hidden_fields'][$return_field]);
				}
			}
		}
		
		/** --------------------------------------------
        /**  Override Form Attributes with form:xxx="" parameters
        /** --------------------------------------------*/
        
        $extra_attributes = array();
        
        if (is_object(ee()->TMPL) AND ! empty(ee()->TMPL->tagparams))
		{
			foreach(ee()->TMPL->tagparams as $key => $value)
			{
				if (strncmp($key, 'form:', 5) == 0)
				{
					if (isset($arr[substr($key, 5)]))
					{
						$arr[substr($key, 5)] = $value;
					}
					else
					{
						$extra_attributes[substr($key, 5)] = $value;
					}
				}
			}
		}
		
		// --------------------------------------------
        //  Create and Return Form
        // --------------------------------------------
				
        $r		= ee()->functions->form_declaration( $arr );
        
        $r	.= stripslashes($tagdata);

        $r	.= "</form>";

		/**	----------------------------------------
		/**	 Add <form> attributes from 
		/**	----------------------------------------*/
		
		$allowed = array(
			'accept', 'accept-charset', 'enctype', 'method', 'action', 'name', 'target', 'class', 'dir', 'id', 'lang', 'style', 'title', 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onkeydown', 'onkeyup', 'onkeypress', 'onreset', 'onsubmit'
		);
		
		foreach($extra_attributes as $key => $value)
		{
			if ( in_array($key, $allowed) == FALSE AND strncmp($key, 'data-', 5) != 0) continue;
			
			$r = str_replace( "<form", '<form '.$key.'="'.htmlspecialchars($value).'"', $r );
		}
        
		return $r;
    }
    
    //	End form 
	
	
    // --------------------------------------------------------------------

	/**
	 * _chars_decode - decodes characters in a string
	 *
	 * @access	public
	 * @param	(string) str - characters to decode
	 * @return	string of decoded characters
	 */
    
    function _chars_decode( $str = '' )
    {
		if ( $str == '' ) return;
		
		$charset = ee()->config->item('charset');
		
		if ( version_compare('5.0.0', PHP_VERSION, '>') )
		{
			$valid_sets = array (	
				'ISO-8859-1',	'ISO8859-1',
				'ISO-8859-15',	'ISO8859-15',
				'UTF-8',
				'cp866',		'ibm866',		'866',
				'cp1251',		'Windows-1251',	'win-1251',	'1251',
				'cp1252',		'Windows-1252',	'1252',
				'KOI8-R',		'koi8-ru',		'koi8r',
				'BIG5',			'950',
				'GB2312',		'936',
				'BIG5-HKSCS',
				'Shift_JIS','	SJIS',			'932',
				'EUC-JP'
			);
			
			if ( ! in_array($charset, $valid_sets) ) {$charset = 'ISO-8859-1';}
		}
    	
    	if ( function_exists( 'html_entity_decode' ) === TRUE )
    	{
    		$str	= $this->_html_entity_decode_full( $str, ENT_COMPAT, $charset );
    	}

		//$str	= str_replace( array( '&amp;', '&#47;', '&#39;' ), array( '&', '/', '' ), $str );
    	
    	$str	= stripslashes( $str );
    	
    	return $str;
    }
	// END _chars_decode
	

    // --------------------------------------------------------------------

	/**
	 * _html_entity_decode_full - decodes html entities in a string
	 *
	 * @access	public
	 * @param	(string) string	 - characters to decode
	 * @param	(string) quotes  - entity type to decode
	 * @param	(string) charset - charset to decode from
	 * @return	string of decoded characters
	 */
	
	function _html_entity_decode_full($string, $quotes = ENT_COMPAT, $charset = 'ISO-8859-1')
	{
		return html_entity_decode(
			preg_replace_callback(
				'/&([a-zA-Z][a-zA-Z0-9]+);/', 
				array($this, '_convert_entity'), 
				$string
			), 
			$quotes, 
			$charset
		);
	}
	//END _html_entity_decode_full
	
	
	// --------------------------------------------------------------------

	/**
	 * _convert_entity - decodes entities from regex matches to real
	 *
	 * @access	public
	 * @param	(array)  matches - characters to decode
	 * @param	(bool) 	 destory - remove without returning string
	 * @return	string of decoded characters
	 */
	
	function _convert_entity($matches, $destroy = TRUE)
	{
		$table = array(
			'quot' 		=> '&#34;',		'eta' 		=> '&#951;', 	'cup' 		=> '&#8746;',	'Aacute' 	=> '&#193;',
			'amp' 		=> '&#38;',     'theta' 	=> '&#952;',    'int' 		=> '&#8747;',	'Acirc' 	=> '&#194;',
			'lt' 		=> '&#60;',     'iota' 		=> '&#953;',    'there4' 	=> '&#8756;',	'Atilde' 	=> '&#195;',
			'gt' 		=> '&#62;',     'kappa' 	=> '&#954;',    'sim' 		=> '&#8764;',   'Auml' 		=> '&#196;',
			'OElig' 	=> '&#338;',    'lambda' 	=> '&#955;',    'cong' 		=> '&#8773;',   'Aring' 	=> '&#197;',
			'oelig' 	=> '&#339;',    'mu' 		=> '&#956;',    'asymp' 	=> '&#8776;',   'AElig' 	=> '&#198;',
			'Scaron' 	=> '&#352;',    'nu' 		=> '&#957;',    'ne' 		=> '&#8800;',   'Ccedil' 	=> '&#199;',
			'scaron' 	=> '&#353;',    'xi' 		=> '&#958;',    'equiv' 	=> '&#8801;',   'Egrave' 	=> '&#200;',
			'Yuml' 		=> '&#376;',    'omicron' 	=> '&#959;',    'le' 		=> '&#8804;',   'Eacute' 	=> '&#201;',
			'circ' 		=> '&#710;',    'pi' 		=> '&#960;',    'ge' 		=> '&#8805;',   'Ecirc' 	=> '&#202;',
			'tilde' 	=> '&#732;',    'rho' 		=> '&#961;',    'sub' 		=> '&#8834;',   'Euml' 		=> '&#203;',
			'ensp' 		=> '&#8194;',   'sigmaf' 	=> '&#962;',    'sup' 		=> '&#8835;',   'Igrave' 	=> '&#204;',
			'emsp' 		=> '&#8195;',   'sigma' 	=> '&#963;',    'nsub' 		=> '&#8836;',   'Iacute' 	=> '&#205;',
			'thinsp' 	=> '&#8201;',   'tau' 		=> '&#964;',    'sube' 		=> '&#8838;',   'Icirc' 	=> '&#206;',
			'zwnj' 		=> '&#8204;',   'upsilon' 	=> '&#965;',    'supe' 		=> '&#8839;',   'Iuml' 		=> '&#207;',
			'zwj' 		=> '&#8205;',   'phi' 		=> '&#966;',    'oplus' 	=> '&#8853;',   'ETH' 		=> '&#208;',
			'lrm' 		=> '&#8206;',   'chi' 		=> '&#967;',    'otimes' 	=> '&#8855;',   'Ntilde' 	=> '&#209;',
			'rlm' 		=> '&#8207;',   'psi' 		=> '&#968;',    'perp' 		=> '&#8869;',   'Ograve' 	=> '&#210;',
			'ndash' 	=> '&#8211;',   'omega' 	=> '&#969;',    'sdot' 		=> '&#8901;',   'Oacute' 	=> '&#211;',
			'mdash' 	=> '&#8212;',   'thetasym' 	=> '&#977;',    'lceil' 	=> '&#8968;',   'Ocirc' 	=> '&#212;',
			'lsquo' 	=> '&#8216;',   'upsih' 	=> '&#978;',    'rceil' 	=> '&#8969;',   'Otilde' 	=> '&#213;',
			'rsquo' 	=> '&#8217;',   'piv' 		=> '&#982;',    'lfloor' 	=> '&#8970;',   'Ouml' 		=> '&#214;',
			'sbquo' 	=> '&#8218;',   'bull' 		=> '&#8226;',   'rfloor' 	=> '&#8971;',   'times' 	=> '&#215;',
			'ldquo' 	=> '&#8220;',   'hellip' 	=> '&#8230;',   'lang' 		=> '&#9001;',   'Oslash' 	=> '&#216;',
			'rdquo' 	=> '&#8221;',   'prime' 	=> '&#8242;',   'rang' 		=> '&#9002;',   'Ugrave' 	=> '&#217;',
			'bdquo' 	=> '&#8222;',   'Prime' 	=> '&#8243;',   'loz' 		=> '&#9674;',   'Uacute' 	=> '&#218;',
			'dagger' 	=> '&#8224;',   'oline' 	=> '&#8254;',   'spades' 	=> '&#9824;',   'Ucirc' 	=> '&#219;',
			'Dagger' 	=> '&#8225;',   'frasl' 	=> '&#8260;',   'clubs' 	=> '&#9827;',   'Uuml' 		=> '&#220;',
			'permil' 	=> '&#8240;',   'weierp' 	=> '&#8472;',   'hearts' 	=> '&#9829;',   'Yacute' 	=> '&#221;',
			'lsaquo' 	=> '&#8249;',   'image' 	=> '&#8465;',   'diams' 	=> '&#9830;',   'THORN' 	=> '&#222;',
			'rsaquo' 	=> '&#8250;',   'real' 		=> '&#8476;',   'nbsp' 		=> '&#160;',    'szlig' 	=> '&#223;',
			'euro' 		=> '&#8364;',   'trade' 	=> '&#8482;',   'iexcl' 	=> '&#161;',    'agrave' 	=> '&#224;',
			'fnof' 		=> '&#402;',    'alefsym' 	=> '&#8501;',   'cent' 		=> '&#162;',    'aacute' 	=> '&#225;',
			'Alpha' 	=> '&#913;',    'larr' 		=> '&#8592;',   'pound' 	=> '&#163;',    'acirc' 	=> '&#226;',
			'Beta' 		=> '&#914;',    'uarr' 		=> '&#8593;',   'curren' 	=> '&#164;',    'atilde' 	=> '&#227;',
			'Gamma' 	=> '&#915;',    'rarr' 		=> '&#8594;',   'yen' 		=> '&#165;',    'auml' 		=> '&#228;',
			'Delta' 	=> '&#916;',    'darr' 		=> '&#8595;',   'brvbar' 	=> '&#166;',    'aring' 	=> '&#229;',
			'Epsilon' 	=> '&#917;',    'harr' 		=> '&#8596;',   'sect' 		=> '&#167;',    'aelig' 	=> '&#230;',
			'Zeta' 		=> '&#918;',    'crarr' 	=> '&#8629;',   'uml' 		=> '&#168;',    'ccedil' 	=> '&#231;',
			'Eta' 		=> '&#919;',    'lArr' 		=> '&#8656;',   'copy' 		=> '&#169;',    'egrave' 	=> '&#232;',
			'Theta' 	=> '&#920;',    'uArr' 		=> '&#8657;',   'ordf' 		=> '&#170;',    'eacute' 	=> '&#233;',
			'Iota' 		=> '&#921;',    'rArr' 		=> '&#8658;',   'laquo' 	=> '&#171;',    'ecirc' 	=> '&#234;',
			'Kappa' 	=> '&#922;',    'dArr' 		=> '&#8659;',   'not' 		=> '&#172;',    'euml' 		=> '&#235;',
			'Lambda' 	=> '&#923;',    'hArr' 		=> '&#8660;',   'shy' 		=> '&#173;',    'igrave' 	=> '&#236;',
			'Mu' 		=> '&#924;',    'forall' 	=> '&#8704;',   'reg' 		=> '&#174;',    'iacute' 	=> '&#237;',
			'Nu' 		=> '&#925;',    'part' 		=> '&#8706;',   'macr' 		=> '&#175;',    'icirc' 	=> '&#238;',
			'Xi' 		=> '&#926;',    'exist' 	=> '&#8707;',   'deg' 		=> '&#176;',    'iuml' 		=> '&#239;',
			'Omicron' 	=> '&#927;',    'empty' 	=> '&#8709;',   'plusmn' 	=> '&#177;',    'eth' 		=> '&#240;',
			'Pi' 		=> '&#928;',    'nabla' 	=> '&#8711;',   'sup2' 		=> '&#178;',    'ntilde' 	=> '&#241;',
			'Rho' 		=> '&#929;',    'isin' 		=> '&#8712;',   'sup3' 		=> '&#179;',    'ograve' 	=> '&#242;',
			'Sigma' 	=> '&#931;',    'notin' 	=> '&#8713;',   'acute' 	=> '&#180;',    'oacute' 	=> '&#243;',
			'Tau' 		=> '&#932;',    'ni' 		=> '&#8715;',   'micro' 	=> '&#181;',    'ocirc' 	=> '&#244;',
			'Upsilon' 	=> '&#933;',    'prod' 		=> '&#8719;',   'para' 		=> '&#182;',    'otilde' 	=> '&#245;',
			'Phi' 		=> '&#934;',    'sum' 		=> '&#8721;',   'middot' 	=> '&#183;',    'ouml' 		=> '&#246;',
			'Chi' 		=> '&#935;',    'minus' 	=> '&#8722;',   'cedil' 	=> '&#184;',    'divide' 	=> '&#247;',
			'Psi' 		=> '&#936;',    'lowast' 	=> '&#8727;',   'sup1' 		=> '&#185;',    'oslash' 	=> '&#248;',
			'Omega' 	=> '&#937;',    'radic' 	=> '&#8730;',   'ordm' 		=> '&#186;',    'ugrave' 	=> '&#249;',
			'alpha' 	=> '&#945;',    'prop' 		=> '&#8733;',   'raquo' 	=> '&#187;',    'uacute' 	=> '&#250;',
			'beta' 		=> '&#946;',    'infin' 	=> '&#8734;',   'frac14' 	=> '&#188;',    'ucirc' 	=> '&#251;',
			'gamma' 	=> '&#947;',    'ang' 		=> '&#8736;',   'frac12' 	=> '&#189;',    'uuml' 		=> '&#252;',
			'delta' 	=> '&#948;',    'and' 		=> '&#8743;',   'frac34' 	=> '&#190;',    'yacute' 	=> '&#253;',
			'epsilon' 	=> '&#949;',    'or' 		=> '&#8744;',   'iquest' 	=> '&#191;',    'thorn' 	=> '&#254;',
		    'zeta' 		=> '&#950;',    'cap' 		=> '&#8745;',   'Agrave' 	=> '&#192;',    'yuml' 		=> '&#255;'
		);
		
		if (isset($table[$matches[1]])) {return $table[$matches[1]];}
	  	// else 
	  	return $destroy ? '' : $matches[0];
	}
	// END _convert_entity


	//  ----------------------------------------------------------------------
	//	email_change
	//	----------------------------------------------------------------------
		
	// --------------------------------------------------------------------

	/**
	 * _convert_entity - decodes entities from regex matches to real
	 *
	 * @access	public
	 * @param	(array)  matches - characters to decode
	 * @param	(bool) 	 destory - remove without returning string
	 * @return	string of decoded characters
	 */

    function _validate_recipients($str)
    {
		//need this for email validation
		//ee()->load->library('email');
    
    	// Remove white space and replace with comma
		$recipients = preg_replace("/\s*(\S+)\s*/", "\\1,", $str);
        	
        // Remove any existing doubles
        $recipients = str_replace(",,", ",", $recipients);
        	
        // Remove any comma at the end
        if (substr($recipients, -1) == ",")
		{
			$recipients = substr($recipients, 0, -1);
		}
		
		// Break into an array via commas and remove duplicates
		$emails = preg_split('/[;,]/', $recipients);
		$emails = array_unique($emails);
			
		// Emails to send email to...
		
		$error = array();
		$approved_emails = array();
		
		foreach ($emails as $email)
		{
			 if (trim($email) == '') continue;
			 			
		     if (ee()->email->valid_email($email))
		     {
		          if ( ! ee()->session->ban_check('email', $email))
		          {
		               $approved_emails[] = $email;
		          }
		          else
		          {
		               $error['ban_recp'] = ee()->lang->line('em_banned_recipient');
		          }
		     }
		     else
		     {
		     	$error['bad_recp'] = ee()->lang->line('em_invalid_recipient');
		     }
		}
		
		return array('approved' => $approved_emails, 'error' => $error);
    }
    
    //	End validate recipients	


	// --------------------------------------------------------------------	
	
	/**
	 * _param - gets store paramaters
	 *
	 * @access	public
	 * @param	(string)  which - which param needed
	 * @param	(string)  type - type of param
	 * @return	param or FALSE
	 */
    
    function _param( $which = '', $type = 'all' )
    {
		//	----------------------------------------
		//	Which?
		//	----------------------------------------
		
		if ( $which == '' ) return FALSE;
    	
		//	----------------------------------------
		//	Params set?
		//	----------------------------------------
		
		if ( count( $this->params ) == 0 )
		{
			//	----------------------------------------
			//	Empty id?
			//	----------------------------------------
			
			if ( ee()->input->get_post('params_id') == FALSE )
			{
				return FALSE;
			}
			
			$this->params_id = ee()->security->xss_clean(ee()->input->get_post('params_id'));
			
			//	----------------------------------------
			//	Select from DB
			//	----------------------------------------
			
			$query	= ee()->db->query( 
				"SELECT data 
				 FROM 	{$this->params_tbl} 
				 WHERE 	params_id = '" . ee()->db->escape_str( $this->params_id ) . "'" 
			);
			
			//	----------------------------------------
			//	Empty?
			//	----------------------------------------
			
			if ( $query->num_rows() == 0 ) return FALSE;
			
			//	----------------------------------------
			//	Unserialize
			//	----------------------------------------
			
			$this->params			= unserialize( $query->row('data') );
			$this->params['set']	= TRUE;
			
			//	----------------------------------------
			//	Delete
			//	----------------------------------------
			
			ee()->db->query( 
				"DELETE FROM {$this->params_tbl} 
				 WHERE entry_date < " . ee()->db->escape_str( (ee()->localize->now - 7200) ) . "" 
			);
		}
		
		//	----------------------------------------
		//	Fetch from params array
		//	----------------------------------------
		
		if ( isset( $this->params[$which] ) )
		{
			$return	= str_replace( "&#47;", "/", $this->params[$which] );
			
			return $return;
		}
		
		//	----------------------------------------
		//	Fetch TMPL
		//	----------------------------------------
		
		if ( isset( $GLOBALS['TMPL'] ) AND ee()->TMPL->fetch_param($which) )
		{
			return ee()->TMPL->fetch_param($which);
		}
    	
		//	----------------------------------------
		//	Return (if which is blank, we are just getting data)
		//	else if we are looking for something that doesn't exist...
		//	----------------------------------------
		
		return ($which === '');
    }
    
    //	End params 
	
	
	// --------------------------------------------------------------------

	/**
	 * _insert_params - adds multiple params to stored params
	 *
	 * @access	public
	 * @param	(array)  associative array of params to send
	 * @return	insert id or false
	 */
    
    function _insert_params( $params = array() )
    {

    	
		//	----------------------------------------
		//	Empty?
		//	----------------------------------------
    	
    	if ( count( $params ) > 0 )
    	{
    		$this->params	= $params;
    	}
    	elseif ( ! isset( $this->params ) OR count( $this->params ) == 0 )
    	{
    		return FALSE;
    	}
    	
		//	----------------------------------------
		//	Serialize
		//	----------------------------------------
		
		$this->params	= serialize( $this->params );
    	
		//	----------------------------------------
		//	Delete excess when older than 2 hours
		//	----------------------------------------
			
		ee()->db->query( 
			"DELETE FROM {$this->params_tbl} 
			 WHERE entry_date < " . ee()->db->escape_str( (ee()->localize->now - 7200) ) . "" 
		);
    	
		//	----------------------------------------
		//	Insert
		//	----------------------------------------
		
		ee()->db->query( 
			ee()->db->insert_string( 
				$this->params_tbl, 
				array( 
					'entry_date' 	=> ee()->localize->now, 
					'data' 			=> $this->params 
				) 
			) 
		);
    	
		//	----------------------------------------
		//	Return
		//	----------------------------------------
		
		return ee()->db->insert_id();
    }
    
    //	End insert params 
    
    
	// --------------------------------------------------------------------

	/**
	 * _upload_files - loads files from $_POST
	 *
	 * @access	public
	 * @param	(bool)  only check errors
	 * @return	null or false
	 */
    
    function _upload_files ( $errors_only = FALSE )
    {
        ee()->lang->loadfile('upload');
        
		//	----------------------------------------
		//	Invoke upload class
		//	----------------------------------------
		
		ee()->load->library('upload');       

		//	----------------------------------------
        //	Handle files from submission
		//	----------------------------------------
		//	Note that if you have trouble getting
		//	files to submit, if the FILES array is
		//	empty, make sure that you are not
		//	submitting the gallery upload form inside
		//	of another form. If the forms are nested,
		//	the FILES array can be wiped out.
		//	----------------------------------------
        
        if ( ! isset($_FILES) OR count( $_FILES ) == 0 OR count( $_FILES ) > $this->upload_limit )
        {
        	return FALSE;
        }
        
        $full	= FALSE;
        
        foreach ( $_FILES as $key => $val )
        {
        	if ( $val['name'] != '' )
        	{
        		$full	= TRUE;
        	}
        }
        
        if ( ! $full )
        {
        	return FALSE;
        }
        
		//	----------------------------------------
		//	Check destination
		//	----------------------------------------
		
		$query	= ee()->db->query( 
			"SELECT	* 
			 FROM 	exp_upload_prefs 
			 WHERE 	name = '" . ee()->db->escape_str($this->_param('file_upload')) . "'
			 LIMIT 	1" 
		);
		
		if ( $query->num_rows() == 0 )
		{
			return ee()->output->show_user_error( 
				'general', 
				ee()->lang->line( 'upload_destination_not_exists' ) 
			);
		}
		else
		{
			$result 		= $query->result_array(); 
			$this->upload 	= $result[0];
		}
        
		//	----------------------------------------
		//	Check path
		//	----------------------------------------
       
        if ( ! @is_dir( $this->upload['server_path'] ) )
        {
        	$this->upload['server_path']	= str_replace( "..", ".", $this->upload['server_path'] );
        	
			if ( ! @is_dir( $this->upload['server_path'] ) )
			{
				return ee()->output->show_user_error( 'general', ee()->lang->line( 'path_does_not_exist' ) );
			}
			else
			{
				$this->upload_config['upload_path'] = $this->upload['server_path'];
			}
        }
		else
		{
			$this->upload_config['upload_path'] = $this->upload['server_path'];
		}
        
		//	----------------------------------------
		//	Only checking errors?
		//	----------------------------------------
		
		if ( $errors_only ) {return;}
        
		//	----------------------------------------
		//	Set attributes
		//	----------------------------------------
        
        $this->upload_config['max_width']		= $this->upload['max_width'];
        $this->upload_config['max_height']		= $this->upload['max_height'];
        $this->upload_config['max_size']		= $this->upload['max_size'];

		//if admin, or type = 'all' (ee1 default)
		if ( ee()->session->userdata['group_id'] 			  == 1		OR 
			 strtolower(trim($this->upload['allowed_types'])) == 'all' 	OR
			 trim($this->upload['allowed_types'])			  == '*'
		   )  									
		{
			$allowed_types = '*';
		}
		//default for EE2.x is 'img' instead of 'all'
		elseif ( trim($this->upload['allowed_types']) == 'img' )
		{
			$allowed_types = 'gif|png|jpg';
		}
		//rely on user preference from CP -_-
		else
		{
			$allowed_types = trim($this->upload['allowed_types']);
		}	
			
        $this->upload_config['allowed_types'] = $allowed_types;							
        
		//	----------------------------------------
		//	Loop
		//	----------------------------------------
		
		$data	= array();
        
        foreach ( $_FILES as $key => $val )
        {
        	if ( preg_match( "/file(\d+)/s", $key, $match ) )
        	{
        		if ( $_FILES[ $match['0'] ]['name'] == '' ) continue;
        		
        		$n	= ( $match['1'] != '' ) ? $match['1']: 0;
        	
				//	----------------------------------------
				//	Set data
				//	----------------------------------------
			
				$data[$n]['userfile']	= $val;
			}
		}
        
		//	----------------------------------------
		//	Loop and insert
		//	----------------------------------------
		
		foreach ( $data as $key => $val )
		{
			$this->_upload_file( $val );
		}
    }
    
    //	End upload files 
    
    
	// --------------------------------------------------------------------

	/**
	 * _upload_file - loads individual files from _upload_files to $this->attachments
	 *
	 * @access	public
	 * @param	(array)  associative array of values of upload info
	 * @return	null
	 */
    
    function _upload_file ( $val )
    {
        ee()->lang->loadfile('upload');
						
		//	----------------------------------------
        //	Force the userfile in post
		//	----------------------------------------
		
		$_FILES['userfile']	= $val['userfile'];

		$file_name = $this->_rename_file($this->upload['server_path'], $_FILES['userfile']['name']);
		
		//	----------------------------------------
        //	Set filename
		//	----------------------------------------
        
        $x 			= explode(".", $file_name);
		$extension	= '.' . end($x);
		$name		= str_replace($extension, '', $file_name);
		
		$this->upload_config['file_name'] = (APP_VER < 2.0) ? $name : $file_name; 

		ee()->upload->initialize($this->upload_config);

		//	----------------------------------------
        //	Perform the upload
		//	----------------------------------------
	
		if ( ! ee()->upload->do_upload())
		{
        	return ee()->output->show_user_error( 'general', ee()->upload->display_errors() );
		}
		
		$this->upload_data = ee()->upload->data();

		//	----------------------------------------
        //	Log in DB
		//	----------------------------------------
		
		$data	= array(
			'entry_id'		=> $this->entry_id,
			'pref_id'		=> $this->upload['id'],
			'server_path'	=> $this->upload_data['file_path'],
			'filename'		=> $this->upload_data['raw_name'],
			'extension'		=> $this->upload_data['file_ext'],
			'filesize'		=> $this->upload_data['file_size'],
			'entry_date'	=> ee()->localize->now
		);
						
		ee()->db->query( ee()->db->insert_string( 'exp_freeform_attachments', $data ) );
		
		$this->attachments[ ee()->db->insert_id() ]['filepath']	= $this->upload_data['full_path'];
		$this->attachments[ ee()->db->insert_id() ]['filename']	= $this->upload_data['file_name'];
    }
    
    //	End upload file 
  	
    
	// --------------------------------------------------------------------

	/**
	 * _rename_file - determines if a file
     *	exists. If so, it'll append a number to
     *	the filename and call itself again. It
     *	does this as many times as necessary
     *	until a filename is clear.
	 *
	 * @access	public
	 * @param	(string)  	full path of file
	 * @param	(string)  	file name
	 * @param	(int)  		iterator for end of filename
	 * @return	(string)	filename
	 */

	function _rename_file($path, $name, $i = 0)
	{
		if (file_exists($path.$name))
		{	
			$xy = explode(".", $name);
			$ext = end($xy);
			
			$name = str_replace('.'.$ext, '', $name);
					
			if (preg_match('/' . $i . '$/is', $name))
			{
				$name = substr($name, 0, -1);
			}	
			
			$i++;

			$name .= $i . '.' . $ext;

			return $this->_rename_file($path, $name, $i);
		}
				
		return $name;
	}
	
	//	End rename file
	
	
	// --------------------------------------------------------------------

	/**
	 * remove_extra_commas - removes extra commas from strings
	 *
	 * @access	public
	 * @param	(string)  	string to remove commas from
	 * @return	(string)	parsed string (with less commas :D)
	 */
	function remove_extra_commas($str)
    {
		// Removes space separated commas as well as leading and trailing commas
		return implode(',', preg_split('/[\s,]+/', $str, -1,  PREG_SPLIT_NO_EMPTY));
    }
	
}
// END CLASS Freeform

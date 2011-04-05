<?php  

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MX Title Control
 * 
 * MX Title Control  is an ExpressionEngine extension that allows you to change the *Title field label for each of channel and (optional) for each of languages. Also you can change default length of title and url_title fields.
 * 
 * @package		ExpressionEngine
 * @category	Extension
 * @author    Max Lazar <max@eec.ms>
 * @copyright Copyright (c) 2010 Max Lazar (http://eec.ms)
 * @license   http://creativecommons.org/licenses/MIT/  MIT License
 * @version 2.1.2
 * @Thanks to Leevi Graham (http://leevigraham.com/) for permission to use his SET/GET settings function for EE2!
 */


class Mx_title_control_ext
{
		var $settings        = array();
    
		var $addon_name            = 'MX Title Control';
		var $name			            = 'MX Title Control';
		var $version         = '2.2';
		var $description     = '';
		var $settings_exist  = 'y';
		var $docs_url        = '';

		 /**
		* Defines the ExpressionEngine hooks that this extension will intercept.
		*
		* @since Version 1.0.0
		* @access private
		* @var mixed an array of strings that name defined hooks
		* @see http://codeigniter.com/user_guide/general/hooks.html
		**/
		private $hooks = array(
							'foreign_character_conversion_array'       => 'foreign_character_conversion_array',
		);
    	// -------------------------------
		// Constructor 
		// -------------------------------             
		function Mx_title_control_ext($settings=''){
			$this->EE =& get_instance();
			$this->settings = $settings;
		}
	
		 public function __construct($settings=FALSE)
		{
			$this->EE =& get_instance();

			// define a constant for the current site_id rather than calling $PREFS->ini() all the time
			if(defined('SITE_ID') == FALSE)
				define('SITE_ID', $this->EE->config->item('site_id'));

			// set the settings for all other methods to access
			$this->settings = ($settings == FALSE) ? $this->_getSettings() : $this->_saveSettingsToSession($settings);
		}


		 /**
		* Prepares and loads the settings form for display in the ExpressionEngine control panel.
		* @since Version 1.0.0
		* @access public
		* @return void
		**/
		public function settings_form()
		{
			$this->EE->lang->loadfile('mx_title_control');
		
			 // Create the variable array
			$vars = array(
				'addon_name' => $this->addon_name,
				'error' => FALSE,
				'input_prefix' => __CLASS__,
				'message' => FALSE,
				'settings_form' =>FALSE,
				'channel_data' => $this->EE->channel_model->get_channels()->result(),
				'language_packs' => ''
			);

			$vars['settings'] = $this->settings;
			$vars['settings_form'] = TRUE;			
			
			 if($new_settings = $this->EE->input->post(__CLASS__))
			{
					$vars['settings'] = $new_settings;
					 $this->_saveSettingsToDB($new_settings);
					$vars['message'] = $this->EE->lang->line('extension_settings_saved_success');
					
					$this->EE->load->dbforge();
					
					$vars['settings']['max_title'] = ((int)$vars['settings']['max_title'] > 1000 ) ? 1000 :  (int)$vars['settings']['max_title'];
					$vars['settings']['max_url_title'] = ((int)$vars['settings']['max_url_title'] > 1000) ? 1000 :  (int)$vars['settings']['max_url_title'];
					
					$fields = array(
												'title' => array(
                                                         'name' => 'title',
                                                         'type' => 'VARCHAR('.$vars['settings']['max_title'].')',
														 
                                                ),
												'url_title' => array(
                                                         'name' => 'url_title',
                                                           'type' => 'VARCHAR('.$vars['settings']['max_url_title'].')',
                                                ),
					);
					$this->EE->dbforge->modify_column('channel_titles', $fields);
			}
			
			 if  ($vars['settings']['multilanguage'] != 'y') {
				$vars['language_packs'][] = 'default';
			}
			else {
				$vars['language_packs'] =  $this->language_packs() ;
			}

			$js = str_replace('"','\"',str_replace("\n", "", $this->EE->load->view('form_settings', $vars, TRUE)));

			return $this->EE->load->view('form_settings', $vars, true);

		}
		// END
	function foreign_character_conversion_array () {
	/*
		if  ($this->EE->input->get('channel_id') != '') {
			if  ($this->EE->input->get('channel_id') !== FALSE) {
				$channel_id =  $this->EE->input->get('channel_id');
			}
			
			$lang  = ( $this->settings['multilanguage']  == 'y') ?  $this->EE->session->userdata('language') : 'default';		
			
			$title = (isset ($this->settings['title_'.$lang.'_'.$channel_id])) ? $this->settings['title_'.$lang.'_'.$channel_id]  : '' ;
			$url_title = (isset ($this->settings['url_title_'.$lang.'_'.$channel_id])) ? $this->settings['url_title_'.$lang.'_'.$channel_id]  : '' ;
			
			if   ($url_title  !='') {
				$settings = $this->EE->api_channel_fields->get_settings('url_title');
				$settings ['field_label'] = $url_title;

				
				$this->EE->api_channel_fields->set_settings('url_title', $settings);
			}
			
		//	print $this->$field_output;
			if   ($title  !='') {
			
				$settings = $this->EE->api_channel_fields->get_settings('title');
				$settings ['field_label'] = $title;
				//echo "asdasd00". print_r($this->EE->api_channel_fields->get_settings('title')['field_label']);
				$this->EE->api_channel_fields->set_settings('title', $settings);
				
			}
			return false;
		}
		else
		{
			return array();
		} */
		return array();
	}	
	
	function language_packs()
	{
		static $languages;
		
		if ( ! isset($languages))
		{
			$this->EE->load->helper('directory');
			
			$source_dir = APPPATH.'language/';
			
			if (($list = directory_map($source_dir, TRUE)) !== FALSE)
			{
				foreach ($list as $file)
				{
					if (is_dir($source_dir.$file) && $file[0] != '.')
					{
						$languages[$file] = ucfirst($file);
					}
				}
			
				ksort($languages);
			}
		}

		return $languages;
	}
	
		// --------------------------------
		//  Activate Extension
		// --------------------------------

		function activate_extension()
		{
			$this->_createHooks();
		}
		
		/**
		* Saves the specified settings array to the database.
		*
		* @since Version 1.0.0
		* @access protected
		* @param array $settings an array of settings to save to the database.
		* @return void
		**/
		private function _getSettings($refresh = FALSE)
		{
			$settings = FALSE;
			if(isset($this->EE->session->cache[$this->addon_name][__CLASS__]['settings']) === FALSE || $refresh === TRUE)
			{
				$settings_query = $this->EE->db->select('settings')
				->where('enabled', 'y')
				->where('class', __CLASS__)
				->get('extensions', 1);

			if($settings_query->num_rows())
			{
				$settings = unserialize($settings_query->row()->settings);
				$this->_saveSettingsToSession($settings);
			}
			}
			else
			{
				$settings = $this->EE->session->cache[$this->addon_name][__CLASS__]['settings'];
			}
			return $settings;
		}

		 /**
		* Saves the specified settings array to the session.
		* @since Version 1.0.0
		* @access protected
		* @param array $settings an array of settings to save to the session.
		* @param array $sess A session object
		* @return array the provided settings array
		**/
		private function _saveSettingsToSession($settings, &$sess = FALSE)
		{
			// if there is no $sess passed and EE's session is not instaniated
			if($sess == FALSE && isset($this->EE->session->cache) == FALSE)
			return $settings;

			// if there is an EE session available and there is no custom session object
			if($sess == FALSE && isset($this->EE->session) == TRUE)
			$sess =& $this->EE->session;

			// Set the settings in the cache
			$sess->cache[$this->addon_name][__CLASS__]['settings'] = $settings;

			// return the settings
			return $settings;
		}


		/**
		* Saves the specified settings array to the database.
		*
		* @since Version 1.0.0
		* @access protected
		* @param array $settings an array of settings to save to the database.
		* @return void
		**/
		private function _saveSettingsToDB($settings)
		{
			$this->EE->db->where('class', __CLASS__)
			->update('extensions', array('settings' => serialize($settings)));
		}
		 /**
		* Sets up and subscribes to the hooks specified by the $hooks array.
		* @since Version 1.0.0
		* @access private
		* @param array $hooks a flat array containing the names of any hooks that this extension subscribes to. By default, this parameter is set to FALSE.
		* @return void
		* @see http://codeigniter.com/user_guide/general/hooks.html
		**/
		private function _createHooks($hooks = FALSE)
		{
			if (!$hooks)
			{
			$hooks = $this->hooks;
			}

			$hook_template = array(
				'class' => __CLASS__,
				'settings' =>'',
				'priority' => '1',
				'version' => $this->version,
			);

			$hook_template['settings']['multilanguage'] = 'n';
			
			foreach ($hooks as $key => $hook)
			{
				if (is_array($hook))
				{
					$data['hook'] = $key;
					$data['method'] = (isset($hook['method']) === TRUE) ? $hook['method'] : $key;
					
					$data = array_merge($data, $hook);
				}
				else
				{
					$data['hook'] = $data['method'] = $hook;
				}

				$hook = array_merge($hook_template, $data);
				$hook['settings'] = serialize($hook['settings']);
				$this->EE->db->query($this->EE->db->insert_string('exp_extensions', $hook));
			}
		}
		
		 /**
		* Removes all subscribed hooks for the current extension.
		*
		* @since Version 1.0.0
		* @access private
		* @return void
		* @see http://codeigniter.com/user_guide/general/hooks.html
		**/
		private function _deleteHooks()
		{
			$this->EE->db->query("DELETE FROM `exp_extensions` WHERE `class` = '".__CLASS__."'");
		}

	
		// END
	


	
		// --------------------------------
		//  Update Extension
		// --------------------------------  

		function update_extension ( $current='' )
		{


			if ($current == '' OR $current == $this->version)
			{
				return FALSE;
			}

			if ($current < '2.1.2')
			{
			
				$this->enable_acc();
				// Update to next version
			}

			$this->EE->db->query("UPDATE exp_extensions SET version = '".$this->EE->db->escape_str($this->version)."' WHERE class = '".get_class($this)."'");
		}
		// END
		
		// --------------------------------
		//  Enable Acc
		// --------------------------------
		function enable_acc ()
		{
			/*$data =  array (
				'accessory_id' => '',
				'controllers' => 'content_edit|content_files|content_publish',
				'class ' => 'Mx_title_control_acc',
				'member_groups' => '',
				'accessory_version ' => $this->version 			
			);
			*/
			
			$this->EE->load->library('addons/addons_installer');
			
			$accessory = 'mx_title_control';
		
			if ($this->EE->addons_installer->install($accessory, 'accessory', FALSE))
			{
				$this->EE->session->set_flashdata('message_success', 'installed');	
			}

		}

		// --------------------------------
		//  Disable Extension
		// --------------------------------

		function disable_extension()
		{

			$this->EE->db->delete('exp_extensions', array('class' => get_class($this)));
		}
		// END
}

/* End of file ext.mx_title_control.php */
/* Location: ./system/expressionengine/third_party/mx_title_control/ext.mx_title_control.php */
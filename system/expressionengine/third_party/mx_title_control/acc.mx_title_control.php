<?php
/**
 * MX Cloner Accessory
 *
 * @package		ExpressionEngine
 * @category	Accessory
 * @author    Max Lazar <max@eec.ms>
 * @copyright Copyright (c) 2010 Max Lazar (http://eec.ms)
 * @license   http://creativecommons.org/licenses/MIT/  MIT License
 * @version 2.1.2
 */

class mx_title_control_acc 
{
	var $name	 		= 'MX Title Control';
	var $id	 				= 'mx_cloner';
	var $addon_name            = 'MX Title Control';
	var $ext_class	= 'Mx_title_control_ext';
	var $version	 	= '2.1.2';
	var $description	= 'MX Title Control  is an ExpressionEngine extension that allows you to change the *Title field label for each of channel and (optional) for each of languages.';
	var $sections	 	= array();

	/**
	* Set Sections
	*
	* Set content for the accessory
	*
	* @access	public
	* @return	void
	*/
	function mx_title_control_acc($settings=''){
			$this->EE =& get_instance();
			$this->settings = ($settings == FALSE) ? $this->_getSettings() : $this->_saveSettingsToSession($settings);
	}
		
	function set_sections()
	{

		$out = '<script type="text/javascript" charset="utf-8">$("#accessoryTabs a.mx_cloner").parent().remove();';


		if  ($this->EE->input->get('M') == 'entry_form') {
			
			$settings =  $this->_getSettings();

			if  ($this->EE->input->get('channel_id') != '') {
				if  ($this->EE->input->get('channel_id') !== FALSE) {
					$channel_id =  $this->EE->input->get('channel_id');
				}
				
				$lang  = ( $this->settings['multilanguage']  == 'y') ?  $this->EE->session->userdata('language') : 'default';		
				
				$title = (isset ($this->settings['title_'.$lang.'_'.$channel_id])) ? $this->settings['title_'.$lang.'_'.$channel_id]  : '' ;
				$url_title = (isset ($this->settings['url_title_'.$lang.'_'.$channel_id])) ? $this->settings['url_title_'.$lang.'_'.$channel_id]  : '' ;
				$max_title = (isset ($this->settings['max_title'])) ? $this->settings['max_title']  : 100 ;
				$max_url_title = (isset ($this->settings['max_url_title'])) ? $this->settings['max_url_title']  : 75 ;
				
				$field_exp = $this->EE->functions->remove_double_slashes($this->EE->config->item('theme_folder_url').'/cp_themes/default/images/field_expand.png');
				
				if ($url_title  !='') {
					$out .= '$("#sub_hold_field_url_title").prev("label").html(\'<span><img class="field_collapse" src="'.$field_exp.'" alt=""> '.$url_title.'</span>\');';
				}
				
				if  ($title  !='') {		
					$out .= '$("#sub_hold_field_title").prev("label").html(\'<span><img class="field_collapse" src="'.$field_exp.'" alt=""> '.$title.'</span>\');';
				}				
				
				if ($max_title  != 100) {
					$out .= '$("input[name=title]").attr("maxlength", '.$max_title.');';
				}
				if ($max_url_title  != 75) {
					$out .= '$("input[name=url_title]").attr("maxlength", '.$max_url_title.');';
				}


				
			}
		};
		
		$out .= '
		</script>';
		$this->sections[]  = $out;
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
			$EE =& get_instance();
			$settings = FALSE;
			if(isset($this->EE->session->cache[$this->addon_name][$this->ext_class]['settings']) === FALSE || $refresh === TRUE)
			{
				$settings_query = $this->EE->db->select('settings')
				->where('enabled', 'y')
				->where('class', $this->ext_class)
				->get('extensions', 1);

			if($settings_query->num_rows())
			{
				$settings = unserialize($settings_query->row()->settings);

			}
			}
			else
			{
				$settings = $this->EE->session->cache[$this->addon_name][$this->ext_class]['settings'];
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
			$sess->cache[$this->addon_name][$this->ext_class]['settings'] = $settings;

			// return the settings
			return $settings;
		}		
}
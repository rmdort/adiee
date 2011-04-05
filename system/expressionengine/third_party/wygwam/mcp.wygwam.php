<?php if (! defined('BASEPATH')) exit('Invalid file request');


require_once PATH_THIRD.'wygwam/helper.php';

/**
 * Wygwam Module CP Class for EE2
 *
 * @package   Wygwam
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Wygwam_mcp {

	/**
	 * Constructor
	 */
	function Wygwam_mcp()
	{
		$this->EE =& get_instance();

		if (isset($this->EE->cp))
		{
			$this->base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=wygwam';
		}

		$this->helper = new Wygwam_Helper();
	}

	// --------------------------------------------------------------------

	/**
	 * Set Page Title
	 */
	private function _set_page_title($line = 'wygwam_module_name')
	{
		if ($line != 'wygwam_module_name')
		{
			$this->EE->cp->set_breadcrumb(BASE.AMP.$this->base, $this->EE->lang->line('wygwam_module_name'));
		}

		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line($line));
	}

	// --------------------------------------------------------------------

	/**
	 * Index
	 */
	function index()
	{
		$this->_set_page_title();

		$vars['base'] = $this->base;

		// configs
		$this->EE->db->select('config_id, config_name');
		$this->EE->db->order_by('config_name');
		$query = $this->EE->db->get('wygwam_configs');
		$vars['configs'] = $query->result_array();

		// license key
		$this->EE->db->select('settings');
		$query = $this->EE->db->get_where('fieldtypes', array('name' => 'wygwam'));
		$settings = unserialize(base64_decode($query->row('settings')));
		$vars['license_key'] = isset($settings['license_key']) ? $settings['license_key'] : '';

		$this->EE->load->library('table');

		return $this->EE->load->view('index', $vars, TRUE);
	}

	/**
	 * Save License Key
	 */
	function save_license_key()
	{
		$settings['license_key'] = $this->EE->input->post('license_key');
		$data['settings'] = base64_encode(serialize($settings));

		$this->EE->db->where('name', 'wygwam');
		$this->EE->db->update('fieldtypes', $data);

		// redirect to Index
		$this->EE->session->set_flashdata('message_success', lang('wygwam_license_key_saved'));
		$this->EE->functions->redirect(BASE.AMP.$this->base);
	}

	// --------------------------------------------------------------------

	/**
	 * Edit Config
	 */
	function config_edit()
	{
		$default_config_settings = $this->helper->default_config_settings();

		if (($config_id = $this->EE->input->get('config_id'))
			&& ($query = $this->EE->db->get_where('wygwam_configs', array('config_id' => $config_id)))
			&& $query->num_rows()
		)
		{
			$config = $query->row_array();
			$config['settings'] = unserialize(base64_decode($config['settings']));
			$config['settings'] = array_merge($default_config_settings, $config['settings']);

			// duplicate?
			if ($this->EE->input->get('clone') == 'y')
			{
				$config['config_id'] = '';
				$config['config_name'] .= ' '.lang('wygwam_clone');
				$this->_set_page_title(lang('wygwam_create_config'));
			}
			else
			{
				$this->_set_page_title(lang('wygwam_edit_config').' - '.$config['config_name']);
			}
		}
		else
		{
			$config = array(
				'config_id' => '',
				'config_name' => '',
				'settings' => $default_config_settings
			);

			$this->_set_page_title(lang('wygwam_create_config'));
		}

		$vars['config'] = $config;
		$vars['base'] = $this->base;
		$vars['helper'] =& $this->helper;

		$this->EE->load->library('table');

		// css and js
		$this->helper->include_theme_css('lib/ckeditor/skins/wygwam2/editor.css');
		$this->helper->include_theme_css('styles/config_edit.css');
		$this->EE->cp->add_js_script(array('ui' => 'draggable'));
		$this->helper->include_theme_js('scripts/config_edit_toolbar.js');

		// -------------------------------------------
		//  Upload Directory
		// -------------------------------------------

		$dir_query = $this->EE->db->query('SELECT u.id, u.name, s.site_label
		                               FROM exp_upload_prefs u, exp_sites s
		                               WHERE u.site_id = s.site_id
		                               ORDER BY site_label, name');

		if ($dir_query->num_rows)
		{
			$msm = ($this->EE->config->item('multiple_sites_enabled') == 'y');

			$upload_dirs = array('' => '--');
			foreach($dir_query->result_array() as $row)
			{
				$upload_dirs[$row['id']] = ($msm ? $row['site_label'].' - ' : '') . $row['name'];
			}

			$vars['upload_dir'] = form_dropdown('settings[upload_dir]', $upload_dirs, $config['settings']['upload_dir'], 'id="upload_dir"');
		}
		else
		{
			$this->EE->lang->loadfile('admin_content');
			$vars['upload_dir'] = lang('no_upload_prefs');
		}

		// -------------------------------------------
		//  Advanced Settings
		// -------------------------------------------

		// which settings have we already shown?
		$skip = array_keys($default_config_settings);

		// get settings that should be treated as lists
		$config_lists = $this->helper->config_lists();

		// sort settings by key
		ksort($config['settings']);

		$js = '';

		foreach($config['settings'] as $setting => $value)
		{
			// skip?
			if (in_array($setting, $skip)) continue;

			// format_tags?
			if ($setting == 'format_tags')
			{
				$value = explode(';', $value);
			}

			// list?
			if (in_array($setting, $config_lists))
			{
				$value = implode("\n", $value);
			}

			$json = $this->EE->javascript->generate_json($value, TRUE);
			$js .= 'new wygwam_addSettingRow("'.$setting.'", '.$json.');' . NL;
		}

		$this->helper->include_theme_js('scripts/config_edit_advanced.js');
		$this->helper->insert_js('jQuery(document).ready(function(){' . NL . $js . '});');

		return $this->EE->load->view('config_edit', $vars, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Config
	 */
	function config_save()
	{
		// -------------------------------------------
		//  Advanced Settings
		// -------------------------------------------

		$settings = $this->EE->input->post('settings');

		// empty toolbar
		if ($settings['toolbar'] === 'n')
		{
			$settings['toolbar'] = array();
		}

		// format_tags
		if (isset($settings['format_tags']))
		{
			$settings['format_tags'] = implode(';', $settings['format_tags']);
		}

		// lists
		foreach($this->helper->config_lists() as $list)
		{
			if (isset($settings[$list]))
			{
				$settings[$list] = array_filter(preg_split('/[\r\n]+/', $settings[$list]));
			}
		}

		// -------------------------------------------
		//  Save and redirect to Index
		// -------------------------------------------

		$config_id = $this->EE->input->post('config_id');

		$config_name = $this->EE->input->post('config_name');
		if (! $config_name) $config_name = 'Untitled';

		$data = array(
			'config_name' => $config_name,
			'settings' => base64_encode(serialize($settings))
		);

		if ($config_id)
		{
			$this->EE->db->where('config_id', $config_id);
			$this->EE->db->update('wygwam_configs', $data);
		}
		else
		{
			$this->EE->db->insert('wygwam_configs', $data);
		}

		// redirect to Index
		$this->EE->session->set_flashdata('message_success', lang('wygwam_config_saved'));
		$this->EE->functions->redirect(BASE.AMP.$this->base);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete Config Confirmation
	 */
	function config_delete_confirm()
	{
		$config_id = $this->EE->input->get('config_id');

		$this->EE->db->select('config_name');
		$query = $this->EE->db->get_where('wygwam_configs', array('config_id' => $config_id));

		$this->_set_page_title(lang('wygwam_delete_config').' - '.$query->row('config_name'));

		$vars['base'] = $this->base;
		$vars['config_id'] = $config_id;
		$vars['config_name'] = $query->row('config_name');

		return $this->EE->load->view('config_delete_confirm', $vars, TRUE);
	}

	/**
	 * Delete Config
	 */
	function config_delete()
	{
		$config_id = $this->EE->input->post('config_id');

		$this->EE->db->delete('wygwam_configs', array('config_id' => $config_id));

		// redirect to Index
		$this->EE->session->set_flashdata('message_success', lang('wygwam_config_deleted'));
		$this->EE->functions->redirect(BASE.AMP.$this->base);
	}

}

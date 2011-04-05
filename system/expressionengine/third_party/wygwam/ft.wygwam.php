<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


require_once PATH_THIRD.'wygwam/config.php';
require_once PATH_THIRD.'wygwam/helper.php';


/**
 * Wygwam Fieldtype Class
 *
 * @package   Wygwam
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Wygwam_ft extends EE_Fieldtype {

	var $info = array(
		'name'    => WYGWAM_NAME,
		'version' => WYGWAM_VER
	);

	/**
	 * Fieldtype Constructor
	 */
	function Wygwam_ft()
	{
		parent::EE_Fieldtype();

		$this->helper = new Wygwam_Helper();

		// -------------------------------------------
		//  Prepare Cache
		// -------------------------------------------

		if (! isset($this->EE->session->cache['wygwam']))
		{
			$this->EE->session->cache['wygwam'] = array();
		}
		$this->cache =& $this->EE->session->cache['wygwam'];
	}

	// --------------------------------------------------------------------

	/**
	 * Install
	 */
	function install()
	{
		if (! class_exists('FF2EE2')) require_once PATH_THIRD.'wygwam/lib/ff2ee2/ff2ee2.php';

		$converter = new FF2EE2('wygwam');
		return $converter->global_settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Update
	 */
	function update($from)
	{
		// update the module version number
		$this->EE->db->where('module_name', WYGWAM_NAME)
		             ->update('modules', array('module_version' => WYGWAM_VER));

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Display Global Settings
	 */
	function display_global_settings()
	{
		if ($this->EE->addons_model->module_installed('wygwam'))
		{
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=wygwam');
		}
		else
		{
			$this->EE->lang->loadfile('wygwam');
			$this->EE->session->set_flashdata('message_failure', lang('wygwam_no_module'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Field Settings
	 */
	private function _field_settings($settings, $matrix = FALSE)
	{
		// load the language file
		$this->EE->lang->loadfile('wygwam');

		$r = array();

		// -------------------------------------------
		//  Editor Configuration
		// -------------------------------------------

		if ($this->EE->db->table_exists('wygwam_configs'))
		{
			$this->EE->db->select('config_id, config_name');
			$this->EE->db->order_by('config_name');
			$query = $this->EE->db->get('wygwam_configs');

			if ($query->num_rows())
			{
				$configs = array();
				foreach($query->result_array() as $config)
				{
					$configs[$config['config_id']] = $config['config_name'];
				}

				$config = isset($settings['config']) ? $settings['config'] : '';
				$config_setting = form_dropdown('wygwam[config]', $configs, $config, 'id="wygwam_config"');
			}
			else
			{
				$config_setting = lang('wygwam_no_configs');
			}
		}
		else
		{
			$config_setting = lang('wygwam_no_module');
		}

		$r[] = array(
			lang('wygwam_editor_config', 'wygwam_config'),
			$config_setting
		);

		// -------------------------------------------
		//  Defer
		// -------------------------------------------

		$defer = isset($settings['defer']) ? $settings['defer'] : 'n';

		$r[] = array(
			lang('wygwam_defer', 'wygwam_defer') . ($matrix ? '' : '<br/>' . lang('wygwam_defer_desc')),
			form_dropdown('wygwam[defer]', array('n'=>lang('no'), 'y'=>lang('yes')), $defer, 'id="wygwam_defer"')
		);


		return $r;
	}

	/**
	 * Display Field Settings
	 */
	function display_settings($settings)
	{
		$settings = array_merge($this->helper->default_settings(), $settings);

		$rows = $this->_field_settings($settings);

		// -------------------------------------------
		//  Field Conversion
		// -------------------------------------------

		// was this previously a different fieldtype?
		if ($settings['field_id'] && $settings['field_type'] != 'wygwam')
		{
			array_unshift($rows, array(
				lang('wygwam_convert', 'wygwam_convert').'<br />'.$this->EE->lang->line('wygwam_convert_desc'),
				form_dropdown('wygwam[convert]',
					array(
						''        => '--',
						'auto'    => 'Auto &lt;br /&gt; or XHTML',
						'textile' => 'Textile'
					),
					(in_array($settings['field_fmt'], array('br', 'xhtml')) ? 'auto' : ''),
					'id="wygwam_convert"'
				)
			));
		}

		// add the rows
		foreach ($rows as $row)
		{
			$this->EE->table->add_row($row[0], $row[1]);
		}
	}

	/**
	 * Display Cell Settings
	 */
	function display_cell_settings($settings)
	{
		global $DSP;

		$settings = array_merge($this->helper->default_settings(), $settings);

		return $this->_field_settings($settings, TRUE);
	}

	/**
	 * Display Variable Settings
	 */
	function display_var_settings($settings)
	{
		$this->helper->insert_js('(function($){
		                            $("#wygwam").wrap($("<div />").attr("id", "ft_wygwam"));
		                          })(jQuery);');

		return $this->_field_settings($settings);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field Settings
	 */
	function save_settings($settings)
	{
		$settings = array_merge($this->EE->input->post('wygwam'));

		// cross the T's
		$settings['field_fmt'] = 'none';
		$settings['field_show_fmt'] = 'n';
		$settings['field_type'] = 'wygwam';

		// -------------------------------------------
		//  Field Conversion
		// -------------------------------------------

		if (isset($settings['convert']))
		{
			$field_id = $this->EE->input->post('field_id');
			if ($field_id && $settings['convert'])
			{
				$this->EE->db->select('entry_id, field_id_'.$field_id.' data, field_ft_'.$field_id.' format');
				$query = $this->EE->db->get_where('channel_data', 'field_id_'.$field_id.' != ""');

				if ($query->num_rows())
				{
					// prepare Typography
					$this->EE->load->library('typography');
					$this->EE->typography->initialize();

					// prepare Textile
					if ($settings['convert'] == 'textile')
					{
						if (! class_exists('Textile'))
						{
							require_once PATH_THIRD.'wygwam/lib/textile/textile.php';
						}

						$textile = new Textile();
					}

					foreach ($query->result_array() as $row)
					{
						$data = $row['data'];
						$convert = FALSE;

						// Auto <br /> and XHTML
						switch ($row['format'])
						{
							case 'br':    $convert = TRUE; $data = $this->EE->typography->nl2br_except_pre($data); break;
							case 'xhtml': $convert = TRUE; $data = $this->EE->typography->auto_typography($data); break;
						}

						// Textile
						if ($settings['convert'] == 'textile')
						{
							$convert = TRUE;
							$data = $textile->TextileThis($data);
						}

						// Save the new field data
						if ($convert)
						{
							$this->EE->db->query($this->EE->db->update_string('exp_channel_data',
								array(
									'field_id_'.$field_id => $data,
									'field_ft_'.$field_id => 'none'
								),
								'entry_id = '.$row['entry_id']
							));
						}
					}
				}
			}

			unset($settings['convert']);
		}

		return $settings;
	}

	/**
	 * Save Cell Settings
	 */
	function save_cell_settings($settings)
	{
		return $settings['wygwam'];
	}

	/**
	 * Save Variable Settings
	 */
	function save_var_settings()
	{
		return $this->EE->input->post('wygwam');
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch File Tags
	 */
	private function _fetch_file_tags()
	{
		if (! isset($this->cache['file_tags']))
		{
			$tags = array();
			$urls = array();

			if ($file_paths = $this->EE->functions->fetch_file_paths())
			{
				foreach ($file_paths as $id => $url)
				{
					$tags[] = LD.'filedir_'.$id.RD;
					$urls[] = $url;
				}
			}

			$this->cache['file_tags'] = array($tags, $urls);
		}

		return $this->cache['file_tags'];
	}

	/**
	 * Replace File Tags
	 */
	private function _replace_file_tags($data)
	{
		$tags = $this->_fetch_file_tags();
		return str_replace($tags[0], $tags[1], $data);
	}

	/**
	 * Replace File Paths
	 */
	private function _replace_file_paths($data)
	{
		$tags = $this->_fetch_file_tags();
		return str_replace($tags[1], $tags[0], $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Config JSON
	 */
	private function _config_json()
	{
		// starting point
		$config = $this->helper->base_config();

		// -------------------------------------------
		//  Editor Config
		// -------------------------------------------

		if ($this->EE->db->table_exists('wygwam_configs')
			&& isset($this->settings['config']) && is_numeric($this->settings['config'])
			&& ($query = $this->EE->db->select('settings')->get_where('wygwam_configs', array('config_id' => $this->settings['config'])))
			&& $query->num_rows()
		)
		{
			// merge custom settings into config
			$custom_settings = unserialize(base64_decode($query->row('settings')));
			$config = array_merge($config, $custom_settings);
		}
		else
		{
			$this->settings['config'] = 'default';
		}

		// skip if already included
		if (in_array($this->settings['config'], $this->cache['included_configs']))
		{
			return;
		}

		// language
		if (! isset($config['language']) || ! $config['language'])
		{
			$lang_map = $this->helper->lang_map();
			$language = $this->EE->session->userdata('language');
			$config['language'] = isset($lang_map[$language]) ? $lang_map[$language] : 'en';
		}

		// toolbar
		if (is_array($config['toolbar']))
		{
			$config['toolbar'] = $this->helper->custom_toolbar($config['toolbar']);
		}

		// css
		if (! $config['contentsCss'])
		{
			unset($config['contentsCss']);
		}

		// -------------------------------------------
		//  CKFinder Config
		// -------------------------------------------

		if ($config['upload_dir'])
		{
			$this->EE->db->select('server_path, url, allowed_types, max_size, max_height, max_width');
			$query = $this->EE->db->get_where('upload_prefs', array('id' => $config['upload_dir']));

			if ($query->num_rows())
			{
				$row = $query->row_array();

				if (! isset($_SESSION)) @session_start();
				if (! isset($_SESSION['wygwam_'.$config['upload_dir']])) $_SESSION['wygwam_'.$config['upload_dir']] = array();
				$sess =& $_SESSION['wygwam_'.$config['upload_dir']];

				$sess['p'] = (substr($row['server_path'], 0, 1) == '/' ? '' : FCPATH) . $row['server_path'];
				$sess['u'] = $row['url'];
				$sess['t'] = $row['allowed_types'];
				$sess['s'] = $row['max_size'];
				$sess['w'] = $row['max_width'];
				$sess['h'] = $row['max_height'];

				$config['filebrowserImageBrowseUrl'] = $this->helper->theme_url().'lib/ckfinder/ckfinder.html?Type=Images&id='.$config['upload_dir'];
				$config['filebrowserImageUploadUrl'] = $this->helper->theme_url().'lib/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&id='.$config['upload_dir'];

				if ($row['allowed_types'] == 'all')
				{
					$config['filebrowserBrowseUrl'] = $this->helper->theme_url().'lib/ckfinder/ckfinder.html?id='.$config['upload_dir'];
					$config['filebrowserUploadUrl'] = $this->helper->theme_url().'lib/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&id='.$config['upload_dir'];
					$config['filebrowserFlashBrowseUrl'] = $this->helper->theme_url().'lib/ckfinder/ckfinder.html?Type=Flash&id='.$config['upload_dir'];
					$config['filebrowserFlashUploadUrl'] = $this->helper->theme_url().'lib/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash&id='.$config['upload_dir'];
				}
			}
		}

		// -------------------------------------------
		//  'wygwam_config' hook
		//   - Override any of the config settings
		// 
			if ($this->EE->extensions->active_hook('wygwam_config'))
			{
				$config = $this->EE->extensions->call('wygwam_config', $config, $this->settings);
			}
		// 
		// -------------------------------------------

		unset($config['upload_dir']);

		// -------------------------------------------
		//  JSONify Config and Return
		// -------------------------------------------

		$config_literals = $this->helper->config_literals();
		$config_booleans = $this->helper->config_booleans();

		$js = '';

		foreach ($config as $setting => $value)
		{
			if (! in_array($setting, $config_literals))
			{
				if (in_array($setting, $config_booleans))
				{
					$value = ($value == 'y' ? TRUE : FALSE);
				}

				$value = $this->EE->javascript->generate_json($value, TRUE);
			}

			$js .= ($js ? ','.NL : '')
			     . "\t\t".'"'.$setting.'": '.$value;
		}

		$this->helper->insert_js(NL."\t".'Wygwam.configs["'.$this->settings['config'].'"] = {'.NL.$js.NL."\t".'};'.NL);
		$this->cache['included_configs'][] = $this->settings['config'];
	}

	/**
	 * Field Includes
	 */
	private function _field_includes()
	{
		if (! isset($this->cache['included_configs']))
		{
			$this->helper->include_theme_js('lib/ckeditor/ckeditor.js');
			$this->helper->include_theme_js('scripts/wygwam.js');
			$this->helper->include_theme_css('styles/wygwam.css');
			$this->helper->insert_js('Wygwam.themeUrl = "'.$this->helper->theme_url().'";');

			$this->cache['included_configs'] = array();
		}
	}

	/**
	 * Display Field
	 */
	function display_field($data)
	{
		$this->_field_includes();
		$this->_config_json();

		$id = str_replace(array('[', ']'), array('_', ''), $this->field_name);
		$defer = (isset($this->settings['defer']) && $this->settings['defer'] == 'y') ? 'true' : 'false';

		$this->helper->insert_js('new Wygwam("'.$id.'", "'.$this->settings['config'].'", '.$defer.');');

		// convert file tags to URLs
		$data = $this->_replace_file_tags($data);

		return '<div class="wygwam"><textarea id="'.$id.'" name="'.$this->field_name.'">'.$data.'</textarea></div>';
	}

	/**
	 * Display Cell
	 */
	function display_cell($data)
	{
		$this->_field_includes();
		$this->_config_json();

		if (! isset($this->cache['displayed_cols']))
		{
			$this->helper->include_theme_js('scripts/matrix2.js');
			$this->cache['displayed_cols'] = array();
		}

		if (! isset($this->cache['displayed_cols'][$this->col_id]))
		{
			$defer = (isset($this->settings['defer']) && $this->settings['defer'] == 'y') ? 'true' : 'false';

			$this->helper->insert_js('Wygwam.matrixColConfigs.col_id_'.$this->col_id.' = ["'.$this->settings['config'].'", '.$defer.'];');

			$this->cache['displayed_cols'][$this->col_id] = TRUE;
		}

		// convert file tags to URLs
		$data = $this->_replace_file_tags($data);

		return '<textarea name="'.$this->cell_name.'">'.$data.'</textarea>';
	}

	/**
	 * Display Variable Field
	 */
	function display_var_field($data)
	{
		return $this->display_field($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field
	 */
	function save($data)
	{
		// Clear out if just whitespace
		if (! $data || preg_match('/^\s*(<\w+>\s*(&nbsp;)*\s*<\/\w+>|<br \/>)?\s*$/s', $data))
		{
			return '';
		}

		// Entitize curly braces within codeblocks
		$data = preg_replace_callback('/<code>(.*?)<\/code>/s',
			create_function('$matches',
				'return str_replace(array("{","}"), array("&#123;","&#125;"), $matches[0]);'
			),
			$data
		);

		// Remove Firebug 1.5.2+ div
		$data = preg_replace('/<div firebugversion=(.|\t|\n|\s)*<\\/div>/', '', $data);

		// Decode double quote entities (&quot;)
		//  - Eventually CKEditor will stop converting these in the first place
		//    http://dev.ckeditor.com/ticket/6645
		$data = str_replace('&quot;', '"', $data);

		// Convert file URLs to tags
		$data = $this->_replace_file_paths($data);

		return $data;
	}

	/**
	 * Save Cell
	 */
	function save_cell($data)
	{
		return $this->save($data);
	}

	/**
	 * Save Variable Field
	 */
	function save_var_field($data)
	{
		return $this->save($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Replace Tag
	 */
	function replace_tag($data)
	{
		return $this->EE->typography->parse_type(
			$this->EE->functions->encode_ee_tags($data),
			array(
				'text_format'   => 'none',
				'html_format'   => 'all',
				'auto_links'    => $this->row['channel_auto_link_urls'],
				'allow_img_url' => $this->row['channel_allow_img_urls']
			)
		);
	}

	/**
	 * Display Variable Tag
	 */
	function display_var_tag($data)
	{
		die($data);
		return $this->replace_tag($data);
	}
}

// END Wygwam_ft class

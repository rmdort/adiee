<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


if (! defined('WYGWAM_NAME'))
{
	// get the values from config.php
	require PATH_THIRD.'wygwam/config.php';

	define('WYGWAM_NAME', $config['name']);
	define('WYGWAM_VER',  $config['version']);
}


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
		// -------------------------------------------
		//  EE1 Conversion
		// -------------------------------------------

		if (! class_exists('FF2EE2')) require 'lib/ff2ee2/ff2ee2.php';

		$converter = new FF2EE2('wygwam');
		return $converter->global_settings;
	}

	// --------------------------------------------------------------------

	private function _theme_url()
	{
		if (! isset($this->cache['theme_url']))
		{
			$theme_folder_url = $this->EE->config->item('theme_folder_url');
			if (substr($theme_folder_url, -1) != '/') $theme_folder_url .= '/';
			$this->cache['theme_url'] = $theme_folder_url.'third_party/wygwam/';
		}

		return $this->cache['theme_url'];
	}

	private function _include_theme_css($file)
	{
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->_theme_url().$file.'" />');
	}

	private function _include_theme_js($file)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$this->_theme_url().$file.'"></script>');
	}

	// --------------------------------------------------------------------

	/**
	 * Insert JS
	 */
	private function _insert_js($js)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript">'.$js.'</script>');
	}

	// --------------------------------------------------------------------

	/**
	 * Display Global Settings
	 */
	function display_global_settings()
	{
		// $this->settings is just the global settings here
		$global_settings = array_merge($this->helper->default_global_settings(), $this->settings);

		// load the language file
		$this->EE->lang->loadfile('wygwam');

		$vars = array(
			'tb_groups' => $this->helper->tb_groups(),
			'tb_selects' => $this->helper->tb_selects(),
			'tb_class_overrides' => $this->helper->tb_class_overrides(),
			'tb_label_overrides' => $this->helper->tb_label_overrides(),
			'toolbars' => array(),
			'theme_url' => $this->_theme_url(),
			'license_key' => $global_settings['license_key'],
			'lang' => array(
				'wygwam_tbconf_label' => $this->EE->lang->line('wygwam_tbconf_label'),
				'wygwam_tbconf_desc' => $this->EE->lang->line('wygwam_tbconf_desc'),
				'wygwam_license_label' => $this->EE->lang->line('wygwam_license_label')
			)
		);

		$toolbars = array_merge($global_settings['toolbars'], array('untitled' => array()));

		foreach($toolbars as $name => $toolbar)
		{
			$vars['toolbars'][$name] = array(
				'flat' => $toolbar,
				'groups' => $this->helper->custom_toolbar($toolbar, TRUE)
			);
		}

		// css and js
		$this->_include_theme_css('lib/ckeditor/skins/wygwam2/editor.css');
		$this->_include_theme_css('styles/global_settings.css');
		$this->_include_theme_js('scripts/global_settings.js');
		$this->EE->cp->add_js_script(array('ui' => 'draggable'));

		// load the view
		$r = $this->EE->load->view('global_settings', array('vars' => $vars), TRUE);

		return '<div id="ft_wygwam_settings">' . $r . '</div>';
	}

	/**
	 * Save Global Settings
	 */
	function save_global_settings()
	{
		$new_global_settings = $this->helper->default_global_settings();
		if (isset($_POST['license_key'])) $new_global_settings['license_key'] = $_POST['license_key'];
		if (isset($_POST['toolbars'])) $new_global_settings['toolbars'] = $_POST['toolbars'];
		if (isset($_POST['ftype'])) $new_global_settings['toolbars'] = array_merge($new_global_settings['toolbars'], $_POST['ftype']['wygwam']['toolbars']);

		return $new_global_settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Field Settings
	 */
	private function _field_settings($settings, $advanced_settings = TRUE)
	{
		// load the language file
		$this->EE->lang->loadfile('wygwam');

		$global_settings = array_merge($this->helper->default_global_settings(), $this->settings);

		$r = array();

		// -------------------------------------------
		//  Toolbar Configuration
		// -------------------------------------------

		$toolbars = array();
		foreach(array_keys($global_settings['toolbars']) as $tb_name)
		{
			$toolbars[$tb_name] = str_replace('_', ' ', $tb_name);
		}

		$toolbar = isset($settings['toolbar']) ? $settings['toolbar'] : '';

		$r[] = array(
			lang('wygwam_toolbar', 'wygwam_toolbar'),
			form_dropdown('wygwam[toolbar]', $toolbars, $toolbar, 'id="wygwam_toolbar"')
		);

		// -------------------------------------------
		//  Upload Directory
		// -------------------------------------------

		$site_id = $this->EE->config->item('site_id');
		$dir_query = $this->EE->db->query('SELECT id, name FROM exp_upload_prefs WHERE site_id = '.$site_id.' ORDER BY name');
		if ($dir_query->num_rows)
		{
			$upload_dirs = array('' => '--');
			foreach($dir_query->result_array() as $row)
			{
				$upload_dirs[$row['id']] = $row['name'];
			}

			$upload_dir = isset($settings['upload_dir']) ? $settings['upload_dir'] : '';

			$uploads = form_dropdown('wygwam[upload_dir]', $upload_dirs, $upload_dir, 'id="wygwam_upload_dir"');
		}
		else
		{
			$uploads = lang('no_upload_prefs');
		}

		$r[] = array(lang('wygwam_upload_dir', 'wygwam_upload_dir'), $uploads);

		// -------------------------------------------
		//  Import CSS and JS
		// -------------------------------------------

		if (! isset($this->cache['displayed_settings']))
		{
			$this->_include_theme_css('styles/field_settings.css');
			$this->_include_theme_js('scripts/field_settings.js');
			$this->EE->cp->add_to_foot('<script type="text/javascript">wygwam_settingPrefix = "wygwam";</script>');
			$this->cache['displayed_settings'] = TRUE;
		}

		// -------------------------------------------
		//  Advanced Settings
		// -------------------------------------------

		if ($advanced_settings && $settings['config'])
		{
			// include jsonwrapper for < PHP 5.1
			include_once('lib/jsonwrapper/jsonwrapper.php');

			// get settings that should be treated as lists
			$config_lists = $this->helper->config_lists();

			// sort settings by key
			ksort($settings['config']);

			$js = '';

			foreach($settings['config'] as $config => $value)
			{
				// format_tags?
				if ($config == 'format_tags')
				{
					$value = explode(';', $value);
				}

				// list?
				if (in_array($config, $config_lists))
				{
					$value = implode("\n", $value);
				}

				$js .= ';new wygwam_addSettingRow("'.$config.'", '.json_encode($value).'); ';
			}

			$this->EE->cp->add_to_foot('<script type="text/javascript">jQuery(document).ready(function(){'.$js.'});</script>');
		}

		return $r;
	}

	/**
	 * Display Field Settings
	 */
	function display_settings($field_settings)
	{
		$field_settings = array_merge($this->helper->default_field_settings(), $field_settings);

		$rows = $this->_field_settings($field_settings);

		// -------------------------------------------
		//  Field Conversion
		// -------------------------------------------

		// was this previously a different fieldtype?
		if ($field_settings['field_id'] && $field_settings['field_type'] != 'wygwam')
		{
			array_unshift($rows, array(
				lang('wygwam_convert_label', 'wygwam_convert').'<br />'.$this->EE->lang->line('wygwam_convert_desc'),
				form_dropdown('wygwam[convert]',
					array(
						''        => '--',
						'auto'    => 'Auto &lt;br /&gt; or XHTML',
						'textile' => 'Textile'
					),
					(in_array($field_settings['field_fmt'], array('br', 'xhtml')) ? 'auto' : ''),
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

		$settings = array_merge($this->helper->default_cell_settings(), $settings);

		return $this->_field_settings($settings, FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field Settings
	 */
	function save_settings($field_settings)
	{
		$field_settings = array_merge($this->helper->default_field_settings(), $this->EE->input->post('wygwam'));

		// -------------------------------------------
		//  Advanced Settings
		// -------------------------------------------

		$config_lists = $this->helper->config_lists();

		// convert any advanced settings to their proper formats
		if (isset($field_settings['config']) && is_array($field_settings['config']))
		{
			foreach ($field_settings['config'] as $config => $value)
			{
				// format_tags?
				if ($config == 'format_tags')
				{
					$field_settings['config'][$config] = implode(';', $value);
				}

				// list?
				if (in_array($config, $config_lists))
				{
					$field_settings['config'][$config] = array_filter(preg_split('/[\r\n]+/', $value));
				}

				// and unset the post var if it exists
				if (isset($_POST["wygwam_config_{$config}"]))
				{
					unset($_POST["wygwam_config_{$config}"]);
				}
			}
		}

		// unset wygwam settings from post vars
		unset($_POST['wygwam']);

		foreach (array_keys($field_settings) as $setting)
		{
			if (isset($_POST["wygwam_{$setting}"]))
			{
				unset($_POST["wygwam_{$setting}"]);
			}
		}

		// cross the T's
		$field_settings['field_fmt'] = 'none';
		$field_settings['field_show_fmt'] = 'n';
		$field_settings['field_type'] = 'wygwam';

		// -------------------------------------------
		//  Field Conversion
		// -------------------------------------------

		if (isset($field_settings['convert']))
		{
			if (isset($_POST['field_id']) && $_POST['field_id'] && $field_settings['convert'])
			{
				$field_id = $_POST['field_id'];
				$query = $this->EE->db->query('SELECT entry_id, field_id_'.$field_id.' data, field_ft_'.$field_id.' format
				                               FROM exp_channel_data
				                               WHERE field_id_'.$field_id.' != ""');

				if ($query->num_rows())
				{
					// prepare Typography
					$this->EE->load->library('typography');
					$this->EE->typography->initialize();

					// prepare Textile
					if ($field_settings['convert'] == 'textile')
					{
						if (! class_exists('Textile'))
						{
							require PATH_THIRD.'wygwam/lib/textile/textile.php';
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
						if ($field_settings['convert'] == 'textile')
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

			unset($field_settings['convert']);
		}

		return $field_settings;
	}

	/**
	 * Save Cell Settings
	 */
	function save_cell_settings($settings)
	{
		$settings = $settings['wygwam'];

		return $settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Config JSON
	 */
	private function _config_json($all_settings)
	{
		// -------------------------------------------
		//  Main Config
		// -------------------------------------------

		$config = array_merge($this->helper->base_config(), $all_settings['config']);

		// language
		if (! isset($config['language']))
		{
			$lang_map = $this->helper->lang_map();
			$language = $this->EE->session->userdata('language');
			$config['language'] = isset($lang_map[$language]) ? $lang_map[$language] : 'en';
		}

		// toolbar
		if (! isset($config['toolbar']))
		{
			$config['toolbar'] = $this->helper->custom_toolbar($all_settings['toolbars'][$all_settings['toolbar']]);
		}

		// -------------------------------------------
		//  CKFinder Config
		// -------------------------------------------

		if ($all_settings['upload_dir'])
		{
			$upload_query = $this->EE->db->query('SELECT server_path, url, allowed_types, max_size, max_height, max_width FROM exp_upload_prefs WHERE id = '.$all_settings['upload_dir']);
			if ($upload_query->num_rows())
			{
				$row = $upload_query->row_array();

				if (! isset($_SESSION)) @session_start();
				if (! isset($_SESSION['wygwam_'.$all_settings['upload_dir']])) $_SESSION['wygwam_'.$all_settings['upload_dir']] = array();
				$sess =& $_SESSION['wygwam_'.$all_settings['upload_dir']];

				$sess['p'] = (substr($row['server_path'], 0, 1) == '/' ? '' : FCPATH) . $row['server_path'];
				$sess['u'] = $row['url'];
				$sess['t'] = $row['allowed_types'];
				$sess['s'] = $row['max_size'];
				$sess['w'] = $row['max_width'];
				$sess['h'] = $row['max_height'];

				$config['filebrowserImageBrowseUrl'] = $this->_theme_url().'lib/ckfinder/ckfinder.html?Type=Images&id='.$all_settings['upload_dir'];
				$config['filebrowserImageUploadUrl'] = $this->_theme_url().'lib/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&id='.$all_settings['upload_dir'];

				if ($row['allowed_types'] == 'all')
				{
					$config['filebrowserBrowseUrl'] = $this->_theme_url().'lib/ckfinder/ckfinder.html?id='.$all_settings['upload_dir'];
					$config['filebrowserUploadUrl'] = $this->_theme_url().'lib/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&id='.$all_settings['upload_dir'];
					$config['filebrowserFlashBrowseUrl'] = $this->_theme_url().'lib/ckfinder/ckfinder.html?Type=Flash&id='.$all_settings['upload_dir'];
					$config['filebrowserFlashUploadUrl'] = $this->_theme_url().'lib/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash&id='.$all_settings['upload_dir'];
				}
			}
		}

		// -------------------------------------------
		//  'wygwam_config' hook
		//   - Override any of the config settings
		// 
			if ($this->EE->extensions->active_hook('wygwam_config'))
			{
				$config = $this->EE->extensions->call('wygwam_config', $config, $all_settings);
			}
		// 
		// -------------------------------------------

		// -------------------------------------------
		//  JSONify Config and Return
		// -------------------------------------------

		$config_literals = $this->helper->config_literals();
		$config_booleans = $this->helper->config_booleans();

		// include jsonwrapper for < PHP 5.1
		include_once('lib/jsonwrapper/jsonwrapper.php');

		$js = '';

		foreach ($config as $setting => $value)
		{
			if (! in_array($setting, $config_literals))
			{
				if (in_array($setting, $config_booleans))
				{
					$value = ($value == 'y' ? TRUE : FALSE);
				}

				$value = json_encode($value);
			}

			$js .= ($js ? ',' : '')
			     . '"'.$setting.'":' . $value;
		}

		return '{'.$js.'}';
	}

	/**
	 * Display Field
	 */
	function display_field($data)
	{
		$all_settings = array_merge($this->helper->default_global_settings(), $this->helper->default_field_settings(), $this->settings);

		if (! isset($this->cache['displayed']))
		{
			$this->_include_theme_js('lib/ckeditor/ckeditor.js');
			$this->cache['displayed'] = TRUE;
		}

		$this->_insert_js('CKEDITOR.replace("'.$all_settings['field_name'].'", '.$this->_config_json($all_settings).');');

		return '<textarea id="'.$this->field_name.'" name="'.$this->field_name.'">'.$data.'</textarea>';
	}

	/**
	 * Display Cell
	 */
	function display_cell($data)
	{
		if (! isset($this->cache['displayed']))
		{
			$this->_include_theme_js('lib/ckeditor/ckeditor.js');
			$this->cache['displayed'] = TRUE;
		}

		if (! isset($this->cache['displayed_cols']))
		{
			$this->_include_theme_js('scripts/matrix2.js');
			$this->cache['displayed_cols'] = array();
		}

		if (! isset($this->cache['displayed_cols'][$this->col_id]))
		{
			$all_settings = array_merge($this->helper->default_global_settings(), $this->helper->default_cell_settings(), $this->settings);
			$json = $this->_config_json($all_settings);
			$this->_insert_js('WygwamConfigs.col_id_'.$this->col_id.' = '.$json.';');

			$this->cache['displayed_cols'][$this->col_id] = TRUE;
		}

		return '<textarea name="'.$this->cell_name.'">'.$data.'</textarea>';
	}

	/**
	 * Save Field
	 */
	function save($data)
	{
		// -------------------------------------------
		//  Clear out if just whitespace
		// -------------------------------------------

		if (! $data || preg_match('/^\s*(<\w+>\s*(&nbsp;)*\s*<\/\w+>|<br \/>)?\s*$/s', $data))
		{
			return '';
		}

		// -------------------------------------------
		//  Entitize curly braces within codeblocks
		// -------------------------------------------

		$data = preg_replace_callback('/<code>(.*?)<\/code>/s',
			create_function('$matches',
				'return str_replace(array("{","}"), array("&#123;","&#125;"), $matches[0]);'
			),
			$data
		);

		// -------------------------------------------
		//  Remove Firebug 1.5.2+ div
		// -------------------------------------------

		$data = preg_replace('/<div firebugversion="[\d\.]+" id="_firebugConsole" style="display: none;">\s*..<\/div>\s*(<br \/>)?/s', '', $data);

		return $data;
	}

	/**
	 * Save Cell
	 */
	function save_cell($data)
	{
		return $this->save($data);
	}
}

// END Wygwam_ft class

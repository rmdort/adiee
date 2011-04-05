<?php if (! defined('EXT')) exit('Invalid file request');


if (! defined('WYGWAM_NAME'))
{
	// get the values from config.php
	require_once FT_PATH.'wygwam/config.php';

	define('WYGWAM_NAME', $config['name']);
	define('WYGWAM_VER',  $config['version']);
	define('WYGWAM_DESC', $config['description']);
	define('WYGWAM_DOCS', $config['docs_url']);
}


/**
 * Wygwam Fieldtype Class
 *
 * @package   Wygwam
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Wygwam extends Fieldframe_Fieldtype {

	var $info = array(
		'name'             => WYGWAM_NAME,
		'version'          => WYGWAM_VER,
		'desc'             => WYGWAM_DESC,
		'docs_url'         => WYGWAM_DOCS,
		'versions_xml_url' => 'http://pixelandtonic.com/ee/versions.xml'
	);

	var $requires = array(
		'ff' => '1.3.1',
		'cp_jquery' => '1.1'
	);

	/**
	 * Fieldtype Constructor
	 */
	function Wygwam()
	{
		$this->helper = new Wygwam_Helper();

		$this->default_site_settings = $this->helper->default_global_settings();
		$this->default_field_settings = $this->helper->default_field_settings();
		$this->default_cell_settings = $this->helper->default_cell_settings();
	}

	// --------------------------------------------------------------------

	private function _theme_url()
	{
		if (! isset($this->_theme_url))
		{
			global $PREFS;
			$theme_folder_url = $PREFS->ini('theme_folder_url', 1);
			$this->_theme_url = $theme_folder_url.'third_party/wygwam/';
		}

		return $this->_theme_url;
	}

	private function _include_theme_css($file)
	{
		$this->insert('head', '<link rel="stylesheet" type="text/css" href="'.$this->_theme_url().$file.'" />');
	}

	private function _include_theme_js($file)
	{
		$this->insert('body', '<script type="text/javascript" src="'.$this->_theme_url().$file.'"></script>');
	}

	// --------------------------------------------------------------------

	/**
	 * Display Site Settings
	 */
	function display_site_settings()
	{
		global $LANG;

		$vars = array(
			'tb_groups' => $this->helper->tb_groups(),
			'tb_selects' => $this->helper->tb_selects(),
			'tb_class_overrides' => $this->helper->tb_class_overrides(),
			'tb_label_overrides' => $this->helper->tb_label_overrides(),
			'toolbars' => array(),
			'theme_url' => $this->_theme_url(),
			'license_key' => $this->site_settings['license_key'],
			'lang' => array(
				'wygwam_tbconf_label' => $LANG->line('wygwam_tbconf_label'),
				'wygwam_tbconf_desc' => $LANG->line('wygwam_tbconf_desc'),
				'wygwam_license_label' => $LANG->line('wygwam_license_label')
			)
		);

		$toolbars = array_merge($this->site_settings['toolbars'], array('untitled' => array()));

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

		// load the view
		ob_start();
		include('views/global_settings.php');
		$r = ob_get_clean();

		return $r;
	}

	// --------------------------------------------------------------------

	private function field_settings($settings, $advanced_settings = TRUE)
	{
		global $LANG, $PREFS, $DB, $DSP;

		// create a new SettingsDisplay instance
		$SD = new Fieldframe_SettingsDisplay();

		$r = array();

		// -------------------------------------------
		//  Toolbar Configuration
		// -------------------------------------------

		$toolbars = array();
		foreach(array_keys($this->site_settings['toolbars']) as $tb_name)
		{
			$toolbars[$tb_name] = str_replace('_', ' ', $tb_name);
		}

		$r[] = array(
			$SD->label('wygwam_toolbar'),
			$SD->select('toolbar', $settings['toolbar'], $toolbars)
		);

		// -------------------------------------------
		//  Upload Directory
		// -------------------------------------------

		$site_id = $PREFS->ini('site_id');
		$dir_query = $DB->query('SELECT id, name FROM exp_upload_prefs WHERE site_id = '.$site_id.' ORDER BY name');
		if ($dir_query->num_rows)
		{
			$upload_dirs = array('' => '--');
			foreach($dir_query->result as $row)
			{
				$upload_dirs[$row['id']] = $row['name'];
			}
			$uploads = $SD->select('upload_dir', $settings['upload_dir'], $upload_dirs);
		}
		else
		{
			$uploads = $LANG->line('no_upload_prefs');
		}

		$r[] = array($SD->label('wygwam_upload_dir'), $uploads);

		// -------------------------------------------
		//  Import CSS and JS
		// -------------------------------------------

		$this->_include_theme_css('styles/field_settings.css');
		$this->_include_theme_js('scripts/field_settings.js');
		$this->insert_js('wygwam_settingPrefix = "ftype[ftype_id_'.$this->_fieldtype_id.']"');

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

				$js .= 'new wygwam_addSettingRow("'.$config.'", '.json_encode($value).'); ';
			}

			$this->insert_js('jQuery(document).ready(function(){'.$js.'});');
		}

		return $r;
	}

	/**
	 * Display Field Settings
	 */
	function display_field_settings($settings)
	{
		global $FF;

		// create a new SettingsDisplay instance
		$SD = new Fieldframe_SettingsDisplay();

		// open the settings table
		$r = $SD->block();
		$SD->row_count++;

		$rows = $this->field_settings($settings);

		// -------------------------------------------
		//  Field Conversion
		// -------------------------------------------

		// was this previously a different fieldtype?
		if (isset($FF->data) && $FF->data['field_id'] && $FF->data['field_type'] != 'ftype_id_'.$this->_fieldtype_id)
		{
			array_unshift($rows, array(
				$SD->label('wygwam_convert_label', 'wygwam_convert_desc'),
				$SD->select('convert',
					(in_array($FF->data['field_fmt'], array('br', 'xhtml')) ? 'auto' : ''),
					array(
						''        => '--',
						'auto'    => 'Auto &lt;br /&gt; or XHTML',
						'textile' => 'Textile'
					)
				)
			));
		}

		// add the rows
		foreach ($rows as $row)
		{
			$r .= $SD->row($row);
		}

		// close the table
		$r .= $SD->block_c();

		return array('rows' => array(array(
			'<div id="ft_wygwam" style="margin: -7px -11px -7px -7px; font-size: 12px;">'.$r.'</div>'
		)));
	}

	/**
	 * Display Cell Settings
	 */
	function display_cell_settings($settings)
	{
		global $DSP;

		$r = '';

		$rows = $this->field_settings($settings, FALSE);
		foreach ($rows as $count => $row)
		{
			$r .= '<label>'
			    .   $DSP->qdiv('itemWrapper'.($count ? 'Top' : '').' defaultBold', $row[0])
			    .   $row[1]
			    . '</label>';
		}

		return $r;
	}

	/**
	 * Save Field Settings
	 * 
	 * @param  array  $field_settings  The field's settings
	 * @return array  modified $settings
	 */
	function save_field_settings($settings)
	{
		global $DB, $TYPE;

		// merge nested wygwam settings
		if (isset($settings['wygwam']))
		{
			$settings = array_merge($settings, $settings['wygwam']);
			unset($settings['wygwam']);
		}

		// -------------------------------------------
		//  Advanced Settings
		// -------------------------------------------

		$config_lists = $this->helper->config_lists();

		// convert any advanced settings to their proper formats
		if (isset($settings['config']) && is_array($settings['config']))
		{
			foreach ($settings['config'] as $config => $value)
			{
				// format_tags?
				if ($config == 'format_tags')
				{
					$settings['config'][$config] = implode(';', $value);
				}

				// list?
				if (in_array($config, $config_lists))
				{
					$settings['config'][$config] = array_filter(preg_split('/[\r\n]+/', $value));
				}
			}
		}

		// -------------------------------------------
		//  Field Conversion
		// -------------------------------------------

		if (isset($_POST['field_id']) && $_POST['field_id'] && isset($settings['convert']) && $settings['convert'])
		{
			$field_id = $_POST['field_id'];
			$query = $DB->query('SELECT entry_id, field_id_'.$field_id.' data, field_ft_'.$field_id.' format
			                     FROM exp_weblog_data
			                     WHERE field_id_'.$field_id.' != ""');

			if ($query->num_rows)
			{
				// prepare Typography
				if (! $TYPE)
				{
					if (! class_exists('Typography'))
					{
						require PATH_CORE.'core.typography'.EXT;
					}

					$TYPE = new Typography;
				}

				// prepare Textile
				if ($settings['convert'] == 'textile')
				{
					if (! class_exists('Textile'))
					{
						require FT_PATH.'wygwam/lib/textile/textile.php';
					}

					$textile = new Textile();
				}

				foreach ($query->result as $row)
				{
					$data = $row['data'];
					$convert = FALSE;

					// Auto <br /> and XHTML
					switch ($row['format'])
					{
						case 'br':    $convert = TRUE; $data = $TYPE->nl2br_except_pre($data); break;
						case 'xhtml': $convert = TRUE; $data = $TYPE->auto_typography($data); break;
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
						$DB->query($DB->update_string('exp_weblog_data',
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

		return $settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Config JSON
	 */
	private function _config_json($settings)
	{
		global $DB, $SESS, $EXT;

		// -------------------------------------------
		//  Main Config
		// -------------------------------------------

		$config = array_merge($this->helper->base_config(), $settings['config']);

		// language
		if (! isset($config['language']))
		{
			$lang_map = $this->helper->lang_map();
			$language = $SESS->userdata['language'];
			$config['language'] = isset($lang_map[$language]) ? $lang_map[$language] : 'en';
		}

		// toolbar
		if (! isset($config['toolbar']))
		{
			$config['toolbar'] = $this->helper->custom_toolbar(
				isset($this->site_settings['toolbars'][$settings['toolbar']])
					? $this->site_settings['toolbars'][$settings['toolbar']]
					: $this->default_site_settings['toolbars'][($settings['toolbar'] != 'Basic' ? 'Full' : 'Basic')]
			);
		}

		// -------------------------------------------
		//  CKFinder Config
		// -------------------------------------------

		if ($settings['upload_dir'])
		{
			$upload_query = $DB->query('SELECT server_path, url, allowed_types, max_size, max_height, max_width FROM exp_upload_prefs WHERE id = '.$settings['upload_dir']);
			if ($upload_query->num_rows)
			{
				if (! isset($_SESSION)) @session_start();
				if (! isset($_SESSION['wygwam_'.$settings['upload_dir']])) $_SESSION['wygwam_'.$settings['upload_dir']] = array();
				$sess =& $_SESSION['wygwam_'.$settings['upload_dir']];

				$sess['p'] = (substr($upload_query->row['server_path'], 0, 1) == '/' ? '' : PATH) . $upload_query->row['server_path'];
				$sess['u'] = $upload_query->row['url'];
				$sess['t'] = $upload_query->row['allowed_types'];
				$sess['s'] = $upload_query->row['max_size'];
				$sess['w'] = $upload_query->row['max_width'];
				$sess['h'] = $upload_query->row['max_height'];

				$config['filebrowserImageBrowseUrl'] = $this->_theme_url().'lib/ckfinder/ckfinder.html?Type=Images&id='.$settings['upload_dir'];
				$config['filebrowserImageUploadUrl'] = $this->_theme_url().'lib/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&id='.$settings['upload_dir'];

				if ($upload_query->row['allowed_types'] == 'all')
				{
					$config['filebrowserBrowseUrl'] = $this->_theme_url().'lib/ckfinder/ckfinder.html?id='.$settings['upload_dir'];
					$config['filebrowserUploadUrl'] = $this->_theme_url().'lib/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&id='.$settings['upload_dir'];
					$config['filebrowserFlashBrowseUrl'] = $this->_theme_url().'lib/ckfinder/ckfinder.html?Type=Flash&id='.$settings['upload_dir'];
					$config['filebrowserFlashUploadUrl'] = $this->_theme_url().'lib/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash&id='.$settings['upload_dir'];
				}
			}
		}

		// -------------------------------------------
		//  'wygwam_config' hook
		//   - Override any of the config settings
		// 
			if ($EXT->active_hook('wygwam_config'))
			{
				$config = $EXT->call_extension('wygwam_config', $config, $settings);
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
	function display_field($field_name, $data, $settings)
	{
		$this->_include_theme_js('lib/ckeditor/ckeditor.js');

		$id = str_replace(array('[', ']'), array('_', ''), $field_name);

		$this->insert_js('CKEDITOR.replace("'.$id.'", '.$this->_config_json($settings).');');

		return '<div style="margin:0 8px 15px 0;"><textarea id="'.$id.'" name="'.$field_name.'">'.$data.'</textarea></div>';
	}

	/**
	 * Display Cell
	 */
	function display_cell($cell_name, $data, $settings)
	{
		global $FFM;
    
		// include dependencies
		$this->_include_theme_js('lib/ckeditor/ckeditor.js');
		$this->_include_theme_js('scripts/matrix.js');
    
		$id = 'wygwam'.rand(); //'wygwam_'.(isset($FFM->col_id) ? $FFM->col_id : '0');
		$this->insert_js('jQuery.wygwamCellConfs.'.$id.' = '.$this->_config_json($settings).';');
    
		return '<textarea id="'.$id.'" name="'.$cell_name.'">'.$data.'</textarea>';
	}


	/**
	 * Save Field
	 */
	function save_field($data, $settings)
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
	function save_cell($data, $settings)
	{
		return $this->save_field($data, $settings);
	}

	// --------------------------------------------------------------------

	/**
	 * Is empty?
	 */
	function is_empty($params, $tagdata, $data, $settings)
	{
		return $data ? 0 : 1;
	}

	/**
	 * Is populated?
	 */
	function is_populated($params, $tagdata, $data, $settings)
	{
		return $data ? 1 : 0;
	}

}

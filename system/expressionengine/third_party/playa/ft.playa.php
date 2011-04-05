<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


require_once PATH_THIRD.'playa/config.php';


/**
 * Playa Fieldtype Class for ExpressionEngine 2
 *
 * @package   Playa
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2011 Pixel & Tonic, Inc
 */
class Playa_ft extends EE_Fieldtype {

	var $drop_panes_size = 10;                          // number of items to be shown at once in Drop Panes UI
	var $limit_options = array(25,50,100,250,500,1000); // options for the "Limit" setting

	// --------------------------------------------------------------------

	var $info = array(
		'name'    => PLAYA_NAME,
		'version' => PLAYA_VER
	);

	var $has_array_data = TRUE;

	/**
	 * Fieldtype Constructor
	 */
	function __construct()
	{
		parent::EE_Fieldtype();

		// -------------------------------------------
		//  Prepare Cache
		// -------------------------------------------

		if (! isset($this->EE->session->cache['playa']))
		{
			$this->EE->session->cache['playa'] = array();
		}

		$this->cache =& $this->EE->session->cache['playa'];

		// -------------------------------------------
		//  Load the helper
		// -------------------------------------------

		if (! class_exists('Playa_Helper'))
		{
			require_once PATH_THIRD.'playa/helper.php';
		}

		$this->helper = new Playa_Helper();

		// -------------------------------------------
		//  Need to call update()?
		// -------------------------------------------

		if (! $this->EE->db->table_exists('playa_relationships'))
		{
			// was Playa 3 installed?
			$query = $this->EE->db->select('fieldtype_id, version')
			                      ->where('name', 'playa')
			                      ->get('fieldtypes');

			if ($query->num_rows())
			{
				// call update()
				$this->update($query->row('version'));

				// update the version # in exp_fieldtypes
				$this->EE->db->where('fieldtype_id', $query->row('fieldtype_id'))
				             ->update('fieldtypes', array('version' => PLAYA_VER));
			}
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Update Global Settings
	 */
	private function _update_global_settings()
	{
		$this->settings = array_merge(array(
			'license_key'    => '',
			'filter_min'  => 20
		), $this->settings);
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

		$ee1_version = '';
		$global_settings = array();

		// include FF2EE2
		if (! class_exists('FF2EE2')) require_once PATH_THIRD.'playa/includes/ff2ee2/ff2ee2.php';

		// run the conversion script
		$converter = new FF2EE2('playa');

		// was Playa 2 or 3 for EE1 installed?
		if ($converter->version)
		{
			// get the old version number
			$ee1_version = $converter->version;

			// get the old global settings
			$global_settings = $converter->global_settings;
		}
		else
		{
			// was Playa 1 for EE1 installed?
			$query = $this->EE->db->select('field_id, field_list_items')
			                      ->where('field_type', 'playa')
			                      ->where('field_list_items !=', '')
			                      ->get('channel_fields');

			if ($query->num_rows())
			{
				$ee1_version = '1.3.3';

				foreach ($query->result() as $field)
				{
					// assemble the new field settings
					$field_settings = array(
						'channels' => explode(',', $field->field_list_items)
					);

					// update the row in exp_channel_fields
					$data = array(
						'field_settings'        => base64_encode(serialize($field_settings)),
						'field_maxl'            => '',
						'field_ta_rows'         => '',
						'field_list_items'      => '',
						'field_related_orderby' => '',
						'field_related_max'     => ''
					);

					$this->EE->db->where('field_id', $field->field_id)
					             ->update('channel_fields', $data);
				}
			}
		}

		if ($ee1_version)
		{
			// run the update script
			$this->update($ee1_version);
		}
		else
		{
			// just create the new table
			$this->_create_table();
		}

		// return the global settings
		return $global_settings;
	}

	/**
	 * Create exp_relationships table
	 */
	private function _create_table()
	{
		if (! $this->EE->db->table_exists('playa_relationships'))
		{
			$this->EE->load->dbforge();

			$this->EE->dbforge->add_field(array(
				'rel_id'          => array('type' => 'int', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE),
				'parent_entry_id' => array('type' => 'int', 'constraint' => 10, 'unsigned' => TRUE),
				'parent_field_id' => array('type' => 'int', 'constraint' => 6, 'unsigned' => TRUE),
				'parent_col_id'   => array('type' => 'int', 'constraint' => 6, 'unsigned' => TRUE),
				'parent_row_id'   => array('type' => 'int', 'constraint' => 10, 'unsigned' => TRUE),
				'child_entry_id'  => array('type' => 'int', 'constraint' => 10, 'unsigned' => TRUE),
				'rel_order'       => array('type' => 'int', 'constraint' => 4, 'unsigned' => TRUE)
			));

			$this->EE->dbforge->add_key('rel_id', TRUE);
			$this->EE->dbforge->add_key('parent_entry_id');
			$this->EE->dbforge->add_key('parent_field_id');
			$this->EE->dbforge->add_key('parent_col_id');
			$this->EE->dbforge->add_key('parent_row_id');
			$this->EE->dbforge->add_key('child_entry_id');

			$this->EE->dbforge->create_table('playa_relationships');

			// add it to the table names cache
			$this->EE->db->data_cache['table_names'][] = $this->EE->db->dbprefix.'playa_relationships';

			return TRUE;
		}
	}

	/**
	 * Update
	 */
	function update($from)
	{
		if ($from)
		{
			if (version_compare($from, '4.0', '<'))
			{
				if ($this->_create_table())
				{
					$rel_data = array();
					$old_rel_ids = array();

					// -------------------------------------------
					//  Migrate Playa relationships
					// -------------------------------------------

					// get the Playa fields
					$fields = $this->EE->db->select('field_id')
					                       ->where('field_type', 'playa')
					                       ->get('channel_fields');

					foreach ($fields->result() as $field)
					{
						$field_name = 'field_id_'.$field->field_id;

						// get the old Playa data
						$entries = $this->EE->db->select('entry_id, '.$field_name)
						                        ->where($field_name.' !=', '')
						                        ->get('channel_data');

						foreach ($entries->result() as $entry)
						{
							// get this field's rel_id's
							$rel_ids = $this->_parse_legacy_rel_ids($entry->$field_name);

							if ($rel_ids)
							{
								// get the old relationships
								$rels = $this->EE->db->select('rel_id, rel_child_id')
								                     ->where_in('rel_id', $rel_ids)
								                     ->get('relationships');

								foreach ($rels->result() as $rel)
								{
									$rel_order = array_search($rel->rel_id, $rel_ids);

									$rel_data[] = array(
										'parent_entry_id' => $entry->entry_id,
										'parent_field_id' => $field->field_id,
										'child_entry_id'  => $rel->rel_child_id,
										'rel_order'       => $rel_order
									);
								}

								// mark the old rel IDs for deletion
								$old_rel_ids = array_merge($old_rel_ids, $rel_ids);
							}
						}
					}

					// -------------------------------------------
					//  Migrate Matrix-Playa relationships
					// -------------------------------------------

					if ($this->EE->db->table_exists('matrix_cols'))
					{
						$cols = $this->EE->db->select('col_id')
						                     ->get_where('matrix_cols', array('col_type' => 'playa'));

						foreach ($cols->result() as $col)
						{
							$col_id = 'col_id_'.$col->col_id;

							$rows = $this->EE->db->select('row_id, entry_id, field_id, '.$col_id)
							                     ->get_where('matrix_data', array($col_id.' !=' => ''));

							foreach ($rows->result() as $row)
							{
								$rel_ids = $this->_parse_legacy_rel_ids($row->$col_id);

								if ($rel_ids)
								{
									// get the old relationships
									$rels = $this->EE->db->select('rel_id, rel_child_id')
									                     ->where_in('rel_id', $rel_ids)
									                     ->get('relationships');

									foreach ($rels->result() as $rel)
									{
										$rel_order = array_search($rel->rel_id, $rel_ids);

										$rel_data[] = array(
											'parent_entry_id' => $row->entry_id,
											'parent_field_id' => $row->field_id,
											'parent_col_id'   => $col->col_id,
											'parent_row_id'   => $row->row_id,
											'child_entry_id'  => $rel->rel_child_id,
											'rel_order'       => $rel_order
										);
									}

									// mark the old rel IDs for deletion
									$old_rel_ids = array_merge($old_rel_ids, $rel_ids);
								}
							}
						}
					}

					// -------------------------------------------
					//  Create the new relationships
					// -------------------------------------------

					foreach ($rel_data as $data)
					{
						$this->EE->db->insert('playa_relationships', $data);
					}

					// -------------------------------------------
					//  Delete the old relationships
					// -------------------------------------------

					if ($old_rel_ids)
					{
						$this->EE->db->where_in('rel_id', $old_rel_ids)
						             ->delete('relationships');
					}
				}
			}

			// update the module version number
			$this->EE->db->where('module_name', 'Playa')
			             ->update('modules', array('module_version' => PLAYA_VER));

			// update the extension version number
			$this->EE->db->where('class', 'Playa_ext')
			             ->update('extensions', array('version' => PLAYA_VER));
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Theme URL
	 */
	private function _theme_url()
	{
		if (! isset($this->cache['theme_url']))
		{
			$theme_folder_url = $this->EE->config->item('theme_folder_url');
			if (substr($theme_folder_url, -1) != '/') $theme_folder_url .= '/';
			$this->cache['theme_url'] = $theme_folder_url.'third_party/playa/';
		}

		return $this->cache['theme_url'];
	}

	/**
	 * Include Theme CSS
	 */
	private function _include_theme_css($file)
	{
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->_theme_url().$file.'" />');
	}

	/**
	 * Include Theme JS
	 */
	private function _include_theme_js($file)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$this->_theme_url().$file.'"></script>');
	}

	// --------------------------------------------------------------------

	/**
	 * Insert CSS
	 */
	private function _insert_css($css)
	{
		$this->EE->cp->add_to_head('<style type="text/css">'.$css.'</style>');
	}

	/**
	 * Insert JS
	 */
	private function _insert_js($js)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript">'.$js.'</script>');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Relationship IDs
	 */
	private function _parse_legacy_rel_ids($data = '', $ignore_closed = FALSE)
	{
		$rel_ids = array();

		$lines = array_filter(preg_split("/[\r\n]+/", $data));
		if (count($lines))
		{
			foreach ($lines as $line)
			{
				if (preg_match('/\[(\!)?(\d+)\]/', $line, $matches))
				{
					if (! $ignore_closed OR ! $matches[1])
					{
						$rel_id = $matches[2];
					}
					else
					{
						continue;
					}
				}
				else
				{
					$rel_id = $line;
				}

				if (is_numeric($rel_id))
				{
					$rel_ids[] = $rel_id;
				}
			}
		}

		return $rel_ids;
	}

	// --------------------------------------------------------------------

	/**
	 * Prepare Params
	 */
	private function _prep_params(&$params)
	{
		// defaults
		$params = array_merge(array(
			'author_id'           => '',
			'backspace'           => '0',
			'category'            => '',
			'category_group'      => '',
			'delimiter'           => '|',
			'dynamic_parameters'  => '',
			'entry_id'            => '',
			'fixed_order'         => '',
			'group_id'            => '',
			'limit'               => '100',
			'offset'              => '0',
			'orderby'             => '',
			'show_expired'        => 'no',
			'show_future_entries' => 'no',
			'sort'                => '',
			'status'              => 'not closed',
			'start_on'            => '',
			'stop_before'         => '',
			'url_title'           => '',
			'weblog'              => ''
		), $params);

		// dynamic params
		if ($params['dynamic_parameters'])
		{
			$dynamic_parameters = explode('|', $params['dynamic_parameters']);
			foreach ($dynamic_parameters as $param)
			{
				if (($val = $this->EE->input->post($param)) !== FALSE)
				{
					$params[$param] = $val;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Select Options
	 */
	private function _select_options($value, $options)
	{
		$r = '';
		foreach ($options as $option_value => $option_line)
		{
			if (is_array($option_line))
			{
				$r .= '<optgroup label="'.$option_value.'">'."\n"
				    .   $this->_select_options($value, $option_line)
				    . '</optgroup>'."\n";
			}
			else
			{
				$selected = is_array($value) ? in_array($option_value, $value) : ($option_value == $value);
				$r .= '<option value="'.$option_value.'"'.($selected ? ' selected="selected"' : '').'>'.$option_line.'</option>';
			}
		}
		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Display Global Settings
	 */
	function display_global_settings()
	{
		$this->_update_global_settings();

		// load the language file
		$this->EE->lang->loadfile('playa');

		// load the table lib
		$this->EE->load->library('table');

		// load the CSS
		$this->_include_theme_css('styles/global_settings.css');

		// use the default template known as
		// $cp_pad_table_template in the views
		$this->EE->table->set_template(array(
			'table_open'    => '<table class="mainTable padTable" border="0" cellspacing="0" cellpadding="0">',
			'row_start'     => '<tr class="even">',
			'row_alt_start' => '<tr class="odd">'
		));

		// "Preference" and "Setting" table headings
		$this->EE->table->set_heading(array('data' => lang('preference'), 'style' => 'width: 50%'), lang('setting'));

		// -------------------------------------------
		//  License Key
		// -------------------------------------------

		$this->EE->table->add_row(
			lang('license_key', 'license_key'),
			form_input('license_key', $this->settings['license_key'], 'id="license_key" size="40"')
		);

		// -------------------------------------------
		//  Filter Min
		// -------------------------------------------

		$this->EE->table->add_row(
			lang('filter_min', 'filter_min').'<br/>'.lang('filter_min_desc'),
			form_input('filter_min', $this->settings['filter_min'], 'id="filter_min" size="3" style="width: 3em;"')
		);

		// -------------------------------------------
		//  Relationship field conversion
		// -------------------------------------------

		$query = $this->EE->db->query('SELECT cf.field_id, cf.field_label, fg.group_name
		                               FROM exp_channel_fields AS cf
		                               INNER JOIN exp_field_groups AS fg ON cf.group_id = fg.group_id
		                               WHERE cf.field_type = "rel"');

		if ($query->num_rows())
		{
			// load the Admin Content lang file
			$this->EE->lang->loadfile('admin_content');

			// initialize a new Table object
			$table = new CI_Table();

			// set the template
			$table->set_template(array(
				'table_open' => '<table class="playaConvertRel" border="0" cellspacing="0" cellpadding="0"'
			));

			// "Field Group", "Field Name", and "Convert?" table headings
			$table->set_heading(lang('field_group'), lang('field_name'), lang('convert'));

			// add each of the Rel fields
			foreach ($query->result() as $field)
			{
				$table->add_row(
					$field->group_name,
					$field->field_label,
					form_radio('convert_rel_field['.$field->field_id.']', 'y', FALSE, 'id="convert_rel_field_'.$field->field_id.'_y"') . NL
						. lang('yes', 'convert_rel_field_'.$field->field_id.'_y') . NBS.NBS.NBS.NBS.NBS . NL
						. form_radio('convert_rel_field['.$field->field_id.']', 'n', TRUE, 'id="convert_rel_field_'.$field->field_id.'_n"') . NL
						. lang('no', 'convert_rel_field_'.$field->field_id.'_n')
				);
			}

			// add the row to the main table
			$this->EE->table->add_row(
				lang('convert_rel_fields', 'convert_rel_fields').'<br/>'
					. lang('convert_rel_fields_info'),
				$table->generate()
			);
		}

		// -------------------------------------------
		//  Related Entries conversion
		// -------------------------------------------

		if ($this->EE->db->table_exists('related_entries'))
		{
			// Convert Related Entries?
			$this->EE->table->add_row(
				lang('convert_related_entries', 'convert_related_entries_y').'<br/>'
					. lang('convert_related_entries_info'),
				form_radio('convert_related_entries', 'y', FALSE, 'id="convert_related_entries_y"') . NL
					. lang('yes', 'convert_related_entries_y') . NBS.NBS.NBS.NBS.NBS . NL
					. form_radio('convert_related_entries', 'n', TRUE, 'id="convert_related_entries_n"') . NL
					. lang('no', 'convert_related_entries_n')
			);
		}

		return $this->EE->table->generate();
	}

	/**
	 * Save Global Settings
	 */
	function save_global_settings()
	{
		$rel_data = array();
		$old_rel_ids = array();

		// -------------------------------------------
		//  Relationship field conversion
		// -------------------------------------------

		if ($fields = $this->EE->input->post('convert_rel_field'))
		{
			$field_ids = array();

			foreach ($fields as $field_id => $convert)
			{
				if ($convert == 'y') $field_ids[] = $field_id;
			}

			if ($field_ids)
			{
				// get the rel fields marked for conversion
				$fields = $this->EE->db->select('field_id, site_id, field_related_id, field_related_orderby, field_related_sort, field_related_max')
				                       ->where_in('field_id', $field_ids)
				                       ->get('channel_fields');

				foreach ($fields->result() as $field)
				{
					// -------------------------------------------
					//  Update the field settings
					// -------------------------------------------

					$field_settings = array(
						'multi'    => 'n',
						'channels' => array($field->field_related_id),
						'limit'    => $field->field_related_max,
						'orderby'  => $field->field_related_orderby,
						'sort'     => strtoupper($field->field_related_sort)
					);

					$data = array(
						'field_type' => 'playa',
						'field_settings' => base64_encode(serialize($field_settings))
					);

					$this->EE->db->where('field_id', $field->field_id)
					             ->update('channel_fields', $data);

					// -------------------------------------------
					//  Convert the relationships
					// -------------------------------------------

					$field_name = 'field_id_'.$field->field_id;

					// get the old relationships
					$rels = $this->EE->db->query('SELECT cd.entry_id, r.rel_id, r.rel_parent_id, r.rel_child_id
					                              FROM exp_channel_data AS cd
					                              INNER JOIN exp_relationships AS r ON cd.'.$field_name.' = r.rel_id');

					foreach ($rels->result() as $rel)
					{
						$rel_data[] = array(
							'parent_entry_id' => $rel->entry_id,
							'parent_field_id' => $field->field_id,
							'child_entry_id'  => $rel->rel_child_id
						);

						// mark the rel_id for deletion
						$old_rel_ids[] = $rel->rel_id;
					}
				}
			}
		}

		// -------------------------------------------
		//  Related Entries Conversion
		// -------------------------------------------

		if ($this->EE->db->table_exists('related_entries') && $this->EE->input->post('convert_related_entries') == 'y')
		{
			// get the field groups
			$query = $this->EE->db->query('SELECT fg.group_id, fg.site_id, fg.group_name,
			                                 (SELECT MAX(cf.field_order) FROM exp_channel_fields AS cf WHERE group_id = fg.group_id) AS field_order
			                               FROM exp_field_groups AS fg');

			if ($query->num_rows())
			{
				$groups = array();

				foreach ($query->result_array() as $group)
				{
					$groups[$group['group_id']] = $group;
				}

				// get all of the related entries
				$query = $this->EE->db->query('SELECT re.entry_id, re.related_entry_id, re.main, c.field_group
				                               FROM exp_related_entries AS re
				                               INNER JOIN exp_channel_titles AS ct ON re.entry_id = ct.entry_id
				                               INNER JOIN exp_channels AS c ON ct.channel_id = c.channel_id');

				foreach ($query->result() as $rel)
				{
					$group =& $groups[$rel->field_group];

					// -------------------------------------------
					//  Create the Playa field
					// -------------------------------------------

					if (! isset($group['playa_field_id']))
					{
						// come up with a unique field name
						$base_field_name = trim(preg_replace('/([^\w]|_)+/', '_', strtolower($group['group_name'])), '_') . '_related_entries';

						for ($i = 0; $i < 50; $i++)
						{
							$field_name = $base_field_name . ($i ? $i : '');

							$count = $this->EE->db->select('COUNT(*) AS count')
							                      ->where('field_name', $field_name)
							                      ->where('site_id', $group['site_id'])
							                      ->get('channel_fields')
							                      ->row('count');

							if (! $count) break;
						}

						// add the field
						$data = array(
							'site_id'     => $group['site_id'],
							'group_id'    => $rel->field_group,
							'field_name'  => $field_name,
							'field_label' => trim($group['group_name']) . ' Related Entries',
							'field_type'  => 'playa',
							'field_order' => ($group['field_order'] + 1)
						);

						$this->EE->db->insert('channel_fields', $data);

						// get the new field_id
						$field_id = $this->EE->db->insert_id();

						// add the field formatting
						$this->EE->db->insert('field_formatting', array(
							'field_id'  => $field_id,
							'field_fmt' => 'none'
						));

						// add the columns
						$this->EE->load->dbforge();
						$fields = $this->settings_modify_column(array_merge($data, array(
							'field_id' => $field_id,
							'ee_action' => 'add'
						)));
						$this->EE->dbforge->add_column('channel_data', $fields);

						// save the field id
						$group['playa_field_id'] = $field_id;
					}

					// -------------------------------------------
					//  Add this relationship
					// -------------------------------------------

				 	$rel_data[] = array(
						'parent_entry_id' => $rel->entry_id,
						'parent_field_id' => $group['playa_field_id'],
						'child_entry_id'  => $rel->related_entry_id,
						'rel_order'       => ($rel->main ? 0 : 1)
					);
				}
			}
		}

		// -------------------------------------------
		//  Create the new relationships
		// -------------------------------------------

		foreach ($rel_data as $data)
		{
			$this->EE->db->insert('playa_relationships', $data);
		}

		// -------------------------------------------
		//  Delete the old relationships
		// -------------------------------------------

		if ($old_rel_ids)
		{
			$this->EE->db->where_in('rel_id', $old_rel_ids)
			             ->delete('relationships');
		}

		// -------------------------------------------
		//  Return the actual global settings
		// -------------------------------------------

		return array(
			'license_key'   => $this->EE->input->post('license_key'),
			'filter_min' => $this->EE->input->post('filter_min')
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Update settings
	 */
	private function _update_settings(&$settings)
	{
		if (isset($settings['show_filters']))
		{
			unset($settings['show_filters']);
		}

		if (isset($settings['blogs']))
		{
			$settings['channels'] = $settings['blogs'];
			unset($settings['blogs']);
		}

		if (isset($settings['ui_mode']))
		{
			$settings['multi'] = ($settings['ui_mode'] != 'select') ? 'y' : 'n';
			unset($settings['ui_mode']);
		}

		// merge in the default settings
		$settings = array_merge(array(
			'multi'    => 'y',
			'channels' => array(),
			'cats'     => array(),
			'authors'  => array(),
			'statuses' => array(),
			'limit'    => '0',
			'limitby'  => '',
			'orderby'  => 'title',
			'sort'     => 'ASC'
		), $settings);
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field Settings
	 */
	function display_settings($data)
	{
		$rows = $this->_field_settings($data, FALSE);

		foreach ($rows as $row)
		{
			$this->EE->table->add_row($row[0], $row[1]);
		}
	}

	/**
	 * Display Cell Settings
	 */
	function display_cell_settings($data)
	{
		return $this->_field_settings($data, TRUE);
	}

	/**
	 * Field Settings
	 */
	private function _field_settings($data, $cell)
	{
		// update the settings
		$this->_update_settings($data);

		// load the language file
		$this->EE->lang->loadfile('playa');

		// get the limit options
		$limit_options['0'] = lang('all');
		foreach ($this->limit_options as $limit)
		{
			$limit_options[(string)$limit] = $limit;
		}

		return array(
			// UI Mode
			array(
				lang('multi', 'playa_multi') . ($cell ? '' : '<br/>'.lang('multi_info')),
				form_radio('playa[multi]', 'y', ($data['multi'] == 'y'), 'id="playa_multi_y"') . NL
					. lang('yes', 'playa_multi_y') . NBS.NBS.NBS.NBS.NBS . NL
					. form_radio('playa[multi]', 'n', ($data['multi'] == 'n'), 'id="playa_multi_n"') . NL
					. lang('no', 'playa_multi_n')
			),

			// Channels
			array(
				lang('channels', 'playa_channels'),
				$this->_channels_select($data['channels'])
			),

			// Categories
			array(
				lang('cats', 'playa_cats'),
				$this->_cats_select($data['cats'])
			),

			// Authors
			array(
				lang('authors', 'playa_authors'),
				$this->_authors_select($data['authors'])
			),

			// Statuses
			array(
				lang('statuses', 'playa_statuses'),
				$this->_statuses_select($data['statuses'])
			),

			// Limit
			array(
				lang('limit_entries_to', 'playa_limit'),
				'<select name="playa[limit]" onchange="this.nextSibling.style.visibility=this.value==\'0\'?\'hidden\':\'visible\';">'
				.   $this->_select_options($data['limit'], $limit_options)
				. '</select>'
				. '<select name="playa[limitby]" style="margin-left: 4px;'.($data['limitby'] ? '' : ' visibility:hidden;').'">'
				.   $this->_select_options($data['limitby'], array('newest'=>lang('newest_entries'), 'oldest'=>lang('oldest_entries')))
				. '</select>'
			),

			// Order
			array(
				lang('order_entries_by', 'playa_order'),
				'<select name="playa[orderby]">'
				.   $this->_select_options($data['orderby'], array('title'=>lang('entry_title'), 'entry_date'=>lang('entry_date')))
				. '</select>'
				. ' in '
				. '<select name="playa[sort]">'
				.   $this->_select_options($data['sort'], array('ASC'=>lang('asc_order'), 'DESC'=>lang('desc_order')))
				. '</select>'
			)
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Channels Multi-select
	 */
	private function _channels_select($selected_channels)
	{
		$msm = ($this->EE->config->item('multiple_sites_enabled') == 'y');
		$site_id = $this->EE->config->item('site_id');

		$channels = $this->EE->db->query('SELECT c.channel_id AS `id`, c.channel_title AS `title`, s.site_label AS `group`
		                                  FROM exp_channels c, exp_sites s
		                                  WHERE s.site_id = c.site_id
		                                        '.($msm ? '' : 'AND s.site_id = "'.$site_id.'"').'
		                                  ORDER BY s.site_label, c.channel_title ASC')
		                         ->result_array();

		// add Current option
		array_unshift($channels, array('id' => 'current', 'title' => '&mdash; '.lang('current').' &mdash;'));

		return $this->_field_settings_select('channels', $channels, $selected_channels, TRUE, $msm);
	}

	/**
	 * Categories Select
	 */
	private function _cats_select($selected_cats)
	{
		$msm = ($this->EE->config->item('multiple_sites_enabled') == 'y');
		$site_id = $this->EE->config->item('site_id');

		$cats = $this->EE->db->query('SELECT c.cat_id AS `id`, c.cat_name AS `title`, c.parent_id, cg.group_name AS `group`
		                              FROM exp_categories c, exp_category_groups cg
		                              WHERE c.group_id = cg.group_id
		                                    '.($msm ? '' : 'AND c.site_id = "'.$site_id.'"').'
		                              ORDER BY cg.group_name, c.cat_order');

		if ($cats->num_rows())
		{
			// group cats by parent_id
			$cats_by_parent = $this->_cats_by_parent($cats->result_array());

			// flatten into sorted and indented options
			$this->_cats_select_options($cats_options, $cats_by_parent);

			return $this->_field_settings_select('cats', $cats_options, $selected_cats);
		}

		return lang('no_cats');
	}

		/**
		 * Group categories by parent_id
		 */
		private function _cats_by_parent($cats)
		{
			$cats_by_parent = array();

			foreach ($cats as $cat)
			{
				if (! isset($cats_by_parent[$cat['parent_id']]))
				{
					$cats_by_parent[$cat['parent_id']] = array();
				}

				$cats_by_parent[$cat['parent_id']][] = $cat;
			}

			return $cats_by_parent;
		}

		/**
		 * Category Options
		 */
		private function _cats_select_options(&$cats=array(), &$cats_by_parent, $parent_id='0', $indent='')
		{
			foreach ($cats_by_parent[$parent_id] as $cat)
			{
				$cat['title'] = $indent.$cat['title'];
				$cats[] = $cat;
				if (isset($cats_by_parent[$cat['id']]))
				{
					$this->_cats_select_options($cats, $cats_by_parent, (string)$cat['id'], $indent.NBS.NBS.NBS.NBS);
				}
			}
		}

	/**
	 * Authors Select
	 */
	private function _authors_select($selected_authors)
	{
		$msm = ($this->EE->config->item('multiple_sites_enabled') == 'y');
		$site_id = $this->EE->config->item('site_id');

		$authors = $this->EE->db->query('SELECT m.member_id AS `id`, m.screen_name AS `title`, mg.group_title AS `group`
		                                 FROM exp_members m, exp_member_groups mg
		                                 WHERE m.group_id = mg.group_id
		                                       AND mg.can_access_publish = "y"
		                                       '.($msm ? '' : 'AND mg.site_id = "'.$site_id.'"').'
		                                 GROUP BY m.member_id
		                                 ORDER BY mg.group_title, m.screen_name')
		                        ->result_array();

		// add Current option
		array_unshift($authors, array('id' => 'current', 'title' => '&mdash; '.lang('current').' &mdash;'));

		return $this->_field_settings_select('authors', $authors, $selected_authors);
	}

	/**
	 * Statuses Select
	 */
	private function _statuses_select($selected_statuses)
	{
		$msm = ($this->EE->config->item('multiple_sites_enabled') == 'y');
		$site_id = $this->EE->config->item('site_id');

		$statuses = $this->EE->db->query('SELECT s.status AS `id`, s.status AS `title`, sg.group_name AS `group`
		                                  FROM exp_statuses s, exp_status_groups sg
		                                  WHERE s.group_id = sg.group_id
		                                        AND s.status NOT IN ("open", "closed")
		                                        '.($msm ? '' : 'AND s.site_id = "'.$site_id.'"').'
		                                  ORDER BY sg.group_name, s.status_order');

		$rows = array_merge(array(
			array('id' => 'open', 'title' => 'Open'),
			array('id' => 'closed', 'title' => 'Closed')
		), $statuses->result_array());

		return $this->_field_settings_select('statuses', $rows, $selected_statuses);
	}

	/**
	 * Field Settings Select
	 */
	private function _field_settings_select($name, $rows, $selected_ids, $multi = TRUE, $optgroups = TRUE)
	{
		$options = $this->_field_settings_select_options($rows, $selected_ids, $optgroups, $row_count);

		return '<select name="playa['.$name.'][]" multiple="multiple" class="multiselect" size="'.($row_count < 10 ? $row_count : 10).'" style="width: 230px">'
		       . $options
		       . '</select>';
	}

	/**
	 * Select Options
	 */
	private function _field_settings_select_options($rows, $selected_ids = array(), $optgroups = TRUE, &$row_count = 0)
	{
		if ($optgroups) $optgroup = '';
		$options = '<option value="any"'.($selected_ids ? '' : ' selected="selected"').'>&mdash; '.lang('any').' &mdash;</option>';
		$row_count = 1;

		foreach ($rows as $row)
		{
			if ($optgroups && isset($row['group']) && $row['group'] != $optgroup)
			{
				if ($optgroup) $options .= '</optgroup>';
				$options .= '<optgroup label="'.$row['group'].'">';
				$optgroup = $row['group'];
				$row_count++;
			}

			$selected = in_array($row['id'], $selected_ids) ? 1 : 0;
			$options .= '<option value="'.$row['id'].'"'.($selected ? ' selected="selected"' : '').'>'.$row['title'].'</option>';
			$row_count++;
		}

		if ($optgroups && $optgroup) $options .= '</optgroup>';

		return $options;
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field Settings
	 */
	function save_settings($data)
	{
		$settings = $this->EE->input->post('playa');

		$this->_validate_settings($settings);

		// cross the T's
		$settings['field_fmt'] = 'none';
		$settings['field_show_fmt'] = 'n';
		$settings['field_type'] = 'playa';

		return $settings;
	}

	/**
	 * Save Cell Settings
	 */
	function save_cell_settings($settings)
	{
		$settings = $settings['playa'];
		$this->_validate_settings($settings);

		return $settings;
	}

	/**
	 * Validate Field Settings
	 */
	private function _validate_settings(&$settings)
	{
		// remove any filters that have "Any" selected
		$filters = array('channels', 'cats', 'authors', 'statuses');

		foreach ($filters as $filter)
		{
			if (isset($settings[$filter]) && in_array('any', $settings[$filter]))
			{
				unset($settings[$filter]);
			}
		}

		// remove Limit if set to "All"
		if (isset($settings['limit']) && ! $settings['limit'])
		{
			unset($settings['limit']);
		}

		// remove Limit By if there's no Limit
		if (isset($settings['limitby']) && ! isset($settings['limit']))
		{
			unset($settings['limitby']);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Status CSS Snippet
	 */
	private function _status_css($status, $highlight)
	{
		return '  .playa-entry a span.'.$status.' { color: #'.$highlight.' !important; }' . NL;
	}

	/**
	 * Display Field
	 */
	function display_field($data)
	{
		$this->_update_settings($this->settings);

		// -------------------------------------------
		//  Include Shared Resources
		// -------------------------------------------

		if (! isset($this->cache['included_shared_resources']))
		{
			// CSS
			$css = $this->_status_css('open', '093')
			     . $this->_status_css('closed', '900');

			$statuses = $this->EE->db->query('SELECT status, highlight FROM exp_statuses
			                                  WHERE status NOT IN ("open", "closed")');
			foreach ($statuses->result_array() as $status)
			{
				$css .= $this->_status_css(str_replace(' ', '_', $status['status']), $status['highlight']);
			}

			$this->_insert_css($css);
			$this->_include_theme_css('styles/field.css');

			// Playa Filter Resources
			$query = $this->EE->db->query('SELECT action_id FROM exp_actions WHERE class = "Playa_mcp" AND method = "filter_entries"');
			$this->cache['filter_action_id'] = $query->num_rows() ? $query->row('action_id') : FALSE;

			if ($this->cache['filter_action_id'])
			{
				$this->EE->lang->loadfile('playa');

				if (($site_index = $this->EE->config->item('playa_site_index')) === FALSE) $site_index = $this->EE->functions->fetch_site_index(0, 0);

				$js = 'PlayaFilterResources = {' . NL
				    . '  filterUrl: "'.$site_index.QUERY_MARKER.'ACT='.$this->cache['filter_action_id'].'",' . NL
				    . '  lang: { is: "'.lang('is').'" }' . NL
				    . '};';

				$this->_insert_js($js);
			}

			$this->cache['included_shared_resources'] = TRUE;
		}

		// is this a cell?
		$cell = isset($this->cell_name);

		// have we included the Matrix single select script?
		if ($cell && ! isset($this->cache['included_cell_resources']))
		{
			$this->_include_theme_js('scripts/matrix.js');

			$this->cache['included_cell_resources'] = TRUE;
		}

		// -------------------------------------------
		//  Get the selected entry IDs
		// -------------------------------------------

		// autosave data?
		if (is_array($data) && isset($data['selections']))
		{
			$vars['selected_entry_ids'] = array_merge(array_filter($data['selections']));
		}
		else if (isset($_POST[$this->field_name]) && isset($_POST[$this->field_name]['selections']) && $_POST[$this->field_name]['selections'])
		{
			$vars['selected_entry_ids'] = $_POST[$this->field_name]['selections'];
		}
		else
		{
			$vars['selected_entry_ids'] = array();

			// is this an existing entry?
			$entry_id = $this->EE->input->get('entry_id');

			if ($entry_id && (! isset($this->cell_name) || isset($this->row_id)))
			{
				$where = array(
					'parent_entry_id' => $entry_id,
					'parent_field_id' => $this->field_id
				);

				// Matrix?
				if (isset($this->cell_name))
				{
					$where['parent_col_id'] = $this->col_id;
					$where['parent_row_id'] = $this->row_id;
				}

				$rels = $this->EE->db->select('child_entry_id')
				                     ->where($where)
				                     ->order_by('rel_order')
				                     ->get('playa_relationships');

				foreach ($rels->result() as $rel)
				{
					$vars['selected_entry_ids'][] = $rel->child_entry_id;
				}
			}
		}

		// -------------------------------------------
		//  Selections list
		//   - Since the selections aren't necessarily in the options list,
		//     we need to run an additional query here
		// -------------------------------------------

		$vars['selected_entries'] = array();

		if ($vars['selected_entry_ids']
			&& ($query = $this->EE->db->query('SELECT entry_id, title, status FROM exp_channel_titles
		                                       WHERE entry_id '.$this->helper->param2sql($vars['selected_entry_ids'])))
			&& $query->num_rows()
		)
		{
			foreach ($query->result() as $entry)
			{
				$key = array_search($entry->entry_id, $vars['selected_entry_ids']);
				$vars['selected_entries'][$key] = $entry;
			}

			ksort($vars['selected_entries']);
		}

		// -------------------------------------------
		//  Is this a cloned entry?
		// -------------------------------------------

		if ($this->EE->input->get('clone') == 'y')
		{
			$old_data = '';
		}

		// -------------------------------------------
		//  Current channel?
		// -------------------------------------------

		if (($key = array_search('current', $this->settings['channels'])) !== FALSE)
		{
			array_splice($this->settings['channels'], $key, 1);

			// get the current channel's ID
			$channel_id = $this->EE->input->get('channel_id');

			// add the ID if it's not already there
			if ($channel_id && ! in_array($channel_id, $this->settings['channels']))
			{
				$this->settings['channels'][] = $channel_id;
			}
		}

		// -------------------------------------------
		//  Current author?
		// -------------------------------------------

		if (($key = array_search('current', $this->settings['authors'])) !== FALSE)
		{
			array_splice($this->settings['authors'], $key, 1);

			// get the current member's ID
			$author_id = $this->EE->session->userdata('member_id');

			// add the ID if it's not already there
			if (! in_array($author_id, $this->settings['authors']))
			{
				$this->settings['authors'][] = $author_id;
			}
		}

		// -------------------------------------------
		//  Get Total Possible Entries
		// -------------------------------------------

		// flatten the array settings
		$flat_channels = implode('|', $this->settings['channels']);
		$flat_cats     = implode('|', $this->settings['cats']);
		$flat_authors  = implode('|', $this->settings['authors']);
		$flat_statuses = implode('|', $this->settings['statuses']);

		// cached?
		$cache_key = $flat_channels . ','
		           . $flat_cats     . ','
		           . $flat_authors  . ','
		           . $flat_statuses;

		if (isset($this->cache['total_entries'][$cache_key]))
		{
			$vars['total_entries'] = $this->cache['total_entries'][$cache_key];
		}
		else
		{
			$vars['total_entries'] = $this->helper->entries_query(array(
				'count'      => TRUE,
				'channel_id' => $this->settings['channels'],
				'category'   => $this->settings['cats'],
				'author_id'  => $this->settings['authors'],
				'status'     => $this->settings['statuses']
			));

			// cache it for later
			$this->cache['total_entries'][$cache_key] = $vars['total_entries'];
		}

		// no entries?
		if (! $vars['total_entries'])
		{
			$this->EE->lang->loadfile('content');
			return lang('no_related_entries');
		}

		// -------------------------------------------
		//  Field config stuff
		// -------------------------------------------

		$cache_key .= $this->settings['limit']   . ','
		            . $this->settings['limitby'] . ','
		            . $this->settings['orderby'] . ','
		            . $this->settings['sort'];

		if (isset($this->cache['configs'][$cache_key]))
		{
			$vars['entries'] =& $this->cache['configs'][$cache_key]['entries'];
			$opts['defaults'] =& $this->cache['configs'][$cache_key]['default_opts'];
		}
		else
		{
			// -------------------------------------------
			//  Get the initial set of entries
			// -------------------------------------------

			$params = array(
				'channel_id' => $this->settings['channels'],
				'category'   => $this->settings['cats'],
				'author_id'  => $this->settings['authors'],
				'status'     => $this->settings['statuses']
			);

			if ($this->settings['limit'])
			{
				$params['orderby'] = 'entry_date';
				$params['sort'] = $this->settings['limitby'] == 'newest' ? 'DESC' : 'ASC';
				$params['limit'] = $this->settings['limit'];
			}
			else
			{
				$params['orderby'] = $this->settings['orderby'];
				$params['sort'] = $this->settings['sort'];
			}

			// run the query
			$vars['entries'] = $this->helper->entries_query($params, array('select' => 'ct.entry_date, ct.status'));

			// if we used ORDER BY for initial limiting,
			// manually sort the entries here
			if ($this->settings['limitby'])
			{
				$this->helper->sort_entries($vars['entries'], $this->settings['sort'], $this->settings['orderby']);
			}

			// cache it for later
			$this->cache['configs'][$cache_key]['entries'] =& $vars['entries'];

			// -------------------------------------------
			//  Put together the default JS options
			// -------------------------------------------

			if ($flat_channels) $opts['defaults']['channel']  = $flat_channels;
			if ($flat_cats)     $opts['defaults']['category'] = $flat_cats;
			if ($flat_authors)  $opts['defaults']['author']   = $flat_authors;
			if ($flat_statuses) $opts['defaults']['status']   = $flat_statuses;

			$opts['defaults']['limit']   = $this->settings['limit'];
			$opts['defaults']['limitby'] = $this->settings['limitby'];
			$opts['defaults']['orderby'] = $this->settings['orderby'];
			$opts['defaults']['sort']    = $this->settings['sort'];

			// cache it for later
			$this->cache['configs'][$cache_key]['default_opts'] =& $opts['defaults'];
		}

		// -------------------------------------------
		//  Pass everything off to the UI function
		// -------------------------------------------

		// use the appropriate field name
		$vars['field_name'] = $cell ? $this->cell_name : $this->field_name;
		$vars['field_id']   = str_replace(array('[', ']'), array('_', ''), $vars['field_name']);

		if (! $cell)
		{
			$opts['fieldName'] = $vars['field_name'];
		}

		$vars['margin'] = $cell ? '3px 0' : '11px 0 1px';

		// return the appropriate display function
		$func = '_display_field_' . ($this->settings['multi'] == 'y' ? 'droppanes' : 'singleselect');
		return $this->$func($data, $vars, $opts, $cell);
	}

	/**
	 * Display Cell
	 */
	function display_cell($data)
	{
		return $this->display_field($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field - Drop panes
	 */
	private function _display_field_droppanes($data, $vars, $opts, $cell)
	{
		$this->_update_global_settings();

		// -------------------------------------------
		//  Include Drop Panes Resources
		// -------------------------------------------

		if (! isset($this->cache['included_droppanes_resources']))
		{
			// load the CSS and JS
			$this->_include_theme_css('styles/droppanes.css');
			$this->_include_theme_js('scripts/droppanes.js');

			$this->cache['included_droppanes_resources'] = TRUE;
		}

		// -------------------------------------------
		//  Filter Bar
		// -------------------------------------------

		// should we display the filters?
		if ($vars['total_entries'] >= $this->settings['filter_min'] && $this->cache['filter_action_id'])
		{
			if (! $cell || ! isset($this->cache['initialized_cols'][$this->col_id]))
			{
				// MSM?
				$msm = ($this->EE->config->item('multiple_sites_enabled') == 'y');
				$site_id = $this->EE->config->item('site_id');

				$cat_groups = array();
				$status_groups = array();

				// -------------------------------------------
				//  Channels
				// -------------------------------------------

				$channels = $this->EE->db->query('SELECT c.channel_id AS `id`, c.channel_title AS `title`, c.cat_group, c.status_group, s.site_label AS `group`
				                                  FROM exp_channels c, exp_sites s
				                                  WHERE s.site_id = c.site_id
				                                        '.($msm ? '' : 'AND s.site_id = "'.$site_id.'"').'
				                                        '.($this->settings['channels'] ? 'AND c.channel_id '.$this->helper->param2sql($this->settings['channels']) : '').'
				                                  ORDER BY s.site_label, c.channel_title ASC')->result_array();

				// remember channel's category groups and status group for later
				foreach ($channels as &$channel)
				{
					if ($channel['cat_group'])    $cat_groups    = array_merge($cat_groups,    explode('|', $channel['cat_group']));
					if ($channel['status_group']) $status_groups = array_merge($status_groups, array($channel['status_group']));

					unset($channel['cat_group']);
					unset($channel['status_group']);
				}

				if (count($channels) > 1)
				{
					$opts['filters']['channel'] = array(lang('channel'), $this->_field_settings_select_options($channels));
				}

				// -------------------------------------------
				//  Categories
				// -------------------------------------------

				if ($cat_groups)
				{
					$cats = $this->EE->db->query('SELECT c.cat_id AS `id`, c.cat_name AS `title`, c.parent_id, cg.group_name AS `group`
					                              FROM exp_categories c, exp_category_groups cg
					                              WHERE c.group_id = cg.group_id
					                                    AND cg.group_id '.$this->helper->param2sql($cat_groups).'
					                              ORDER BY cg.group_name, c.cat_order');

					if ($cats->num_rows())
					{
						// group cats by parent_id
						$cats_by_parent = $this->_cats_by_parent($cats->result_array());

						// flatten into sorted and indented options
						$this->_cats_select_options($cats_options, $cats_by_parent);

						$opts['filters']['category'] = array(lang('category'), $this->_field_settings_select_options($cats_options));
					}
				}

				// -------------------------------------------
				//  Authors
				// -------------------------------------------

				$authors = $this->EE->db->query('SELECT m.member_id AS `id`, m.screen_name AS `title`, mg.group_title AS `group`
				                                 FROM exp_members m, exp_member_groups mg
				                                 WHERE m.group_id = mg.group_id
				                                       AND mg.can_access_publish = "y"
				                                       '.($msm ? '' : 'AND mg.site_id = "'.$site_id.'"').'
				                                       '.($this->settings['authors'] ? 'AND m.member_id '.$this->helper->param2sql($this->settings['authors']) : '').'
				                                 GROUP BY m.member_id
				                                 ORDER BY mg.group_title, m.screen_name');

				if ($authors->num_rows())
				{
					$opts['filters']['author'] = array(lang('author'), $this->_field_settings_select_options($authors->result_array()));
				}

				// -------------------------------------------
				//  Statuses
				// -------------------------------------------

				$statuses = $this->EE->db->query('SELECT s.status AS `id`, s.status AS `title`, sg.group_name AS `group`
				                                  FROM exp_statuses s, exp_status_groups sg
				                                  WHERE s.group_id = sg.group_id
				                                        AND s.status NOT IN ("open", "closed")
				                                        AND s.group_id '.$this->helper->param2sql($status_groups).'
				                                        '.($msm ? '' : 'AND s.site_id = "'.$site_id.'"').'
				                                  ORDER BY sg.group_name, s.status_order');

				$statuses = array_merge(array(
					array('id' => 'open', 'title' => 'Open'),
					array('id' => 'closed', 'title' => 'Closed')
				), $statuses->result_array());

				$opts['filters']['status'] = array(lang('status'), $this->_field_settings_select_options($statuses));


				$opts_json = $this->EE->javascript->generate_json($opts, TRUE);

				if ($cell)
				{
					$this->_insert_js('PlayaColOpts.col_id_'.$this->col_id.' = '.$opts_json.';');

					// remember that we've already gone through all of this for this column
					$this->cache['initialized_cols'][$this->col_id] = TRUE;
				}

			}

			$vars['show_filters'] = TRUE;
		}
		else
		{
			$opts_json = '';
			$vars['show_filters'] = FALSE;
		}

		// -------------------------------------------
		//  Insert the JS
		// -------------------------------------------

		if (! $cell)
		{
			$this->_insert_js('new PlayaDropPanes(jQuery("#'.$vars['field_id'].'")'.($opts_json ? ', '.$opts_json : '').');');
		}

		// -------------------------------------------
		//  Prepare HTML
		// -------------------------------------------

		$vars['options_height'] = $vars['selections_height'] = (($vars['total_entries'] > $this->drop_panes_size) ? $this->drop_panes_size : $vars['total_entries']) * 19 + 9;
		if ($vars['show_filters']) $vars['selections_height'] += 34;

		$r = $this->EE->load->view('droppanes', $vars, TRUE);
		return $this->helper->strip_whitespace($r);
	}

	/**
	 * Category Filter Snippet
	 */
	private function _cats_f(&$cats=array(), &$cats_by_parent, $parent_id='0', $indent='')
	{
		foreach ($cats_by_parent[$parent_id] as $cat_id => $cat)
		{
			$cats[$cat_id] = $indent.$cat['cat_name'];
			if (isset($cats_by_parent[$cat_id]))
			{
				$this->_cats_f($cats, $cats_by_parent, "$cat_id", $indent.'    ');
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field - Select
	 */
	private function _display_field_singleselect($data, $vars, $opts, $cell)
	{
		// -------------------------------------------
		//  Include Single Select Resources
		// -------------------------------------------

		if (! isset($this->cache['included_singleselect_resources']))
		{
			// load the CSS and JS
			$this->_include_theme_css('styles/singleselect.css');
			$this->_include_theme_js('scripts/singleselect.js');

			$this->cache['included_singleselect_resources'] = TRUE;
		}

		// -------------------------------------------
		//  Insert the JS
		// -------------------------------------------

		$opts_json = $this->EE->javascript->generate_json($opts, TRUE);

		if (! $cell)
		{
			$this->_insert_js('new PlayaSingleSelect(jQuery("#'.$vars['field_id'].'")'.($opts_json ? ', '.$opts_json : '').');');
		}
		else if (! isset($this->cache['initialized_cols'][$this->col_id]))
		{
			$this->_insert_js('PlayaColOpts.col_id_'.$this->col_id.' = '.$opts_json.';');

			// remember that we've already gone through all of this for this column
			$this->cache['initialized_cols'][$this->col_id] = TRUE;
		}

		// -------------------------------------------
		//  Prepare HTML
		// -------------------------------------------

		$vars['theme_url'] = $this->_theme_url();
		$vars['selected_entry'] = isset($vars['selected_entries'][0]) ? $vars['selected_entries'][0] : NULL;

		$r = $this->EE->load->view('singleselect', $vars, TRUE);
		return $this->helper->strip_whitespace($r);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field
	 */
	function save($data)
	{
		// ignore everything but the selections
		$selections = isset($data['selections']) ? array_merge(array_filter($data['selections'])) : array();

		// save the post data for later
		$this->cache['selections'][$this->settings['field_id']] = $selections;

		// just return 'y' if there are any selections
		// for the sake of Required field validation
		return $selections ? 'y' : '';
	}

	/**
	 * Save Cell
	 */
	function save_cell($data)
	{
		// ignore everything but the selections
		$selections = isset($data['selections']) ? array_merge(array_filter($data['selections'])) : array();

		// save the post data for later
		if (! isset($this->cache['selections'][$this->settings['field_id']])) $this->cache['selections'][$this->settings['field_id']] = array();
		if (! isset($this->cache['selections'][$this->settings['field_id']][$this->settings['col_id']])) $this->cache['selections'][$this->settings['field_id']][$this->settings['col_id']] = array();
		$this->cache['selections'][$this->settings['field_id']][$this->settings['col_id']][$this->settings['row_name']] = $selections;

		// just return 'y' if there are any selections
		// for the sake of Required field validation
		return $selections ? 'y' : '';
	}

	/**
	 * Post Save
	 */
	function post_save($data)
	{
		// make sure this should have been called in the first place
		if (! isset($this->cache['selections'][$this->settings['field_id']])) return;

		// get the selections from the cache
		$selections = $this->cache['selections'][$this->settings['field_id']];

		$data = array(
			'parent_entry_id' => $this->settings['entry_id'],
			'parent_field_id' => $this->settings['field_id']
		);

		// save the changes
		$keywords = $this->_save_rels($selections, $data);

		// save the keywords in exp_channel_data
		$this->EE->db->where('entry_id', $this->settings['entry_id'])
		             ->update('channel_data', array($this->settings['field_name'] => $keywords));
	}

	/**
	 * Post Save Cell
	 */
	function post_save_cell($data)
	{
		// get the selections from the cache
		$selections = $this->cache['selections'][$this->settings['field_id']][$this->settings['col_id']][$this->settings['row_name']];

		$data = array(
			'parent_entry_id' => $this->settings['entry_id'],
			'parent_field_id' => $this->settings['field_id'],
			'parent_col_id'   => $this->settings['col_id'],
			'parent_row_id'   => $this->settings['row_id']
		);

		// save the changes
		$keywords = $this->_save_rels($selections, $data);

		// save the keywords in exp_matrix_data
		$this->EE->db->where('row_id', $this->settings['row_id'])
		             ->update('matrix_data', array($this->settings['col_name'] => $keywords));
	}

	/**
	 * Save Relationships
	 */
	private function _save_rels($selections, $data)
	{
		$r = '';

		// Delete existing relationships
		$this->EE->db->where($data)
		             ->delete('playa_relationships');


		if ($selections)
		{
			// -------------------------------------------
			//  Get child titles
			// -------------------------------------------

			$child_titles = array();

			$query = $this->EE->db->select('entry_id, title, url_title')
			                      ->where_in('entry_id', $selections)
			                      ->get('channel_titles')
			                      ->result();

			foreach ($query as $row)
			{
				$child_titles[$row->entry_id] = array($row->title, $row->url_title);
			}

			// -------------------------------------------
			//  Build new Playa data
			// -------------------------------------------

			foreach ($selections as $rel_order => $child_entry_id)
			{
				$rel_data = array_merge($data, array(
					'child_entry_id' => $child_entry_id,
					'rel_order'      => $rel_order
				));

				$this->EE->db->insert('playa_relationships', $rel_data);

				// add some keywords to $r
				$r .= ($r ? "\r" : '')
				    . '['.$child_entry_id.'] '.str_replace('\'', '', $child_titles[$child_entry_id][0]).' - '.$child_titles[$child_entry_id][1];
			}
		}

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Modify exp_channel_data Column Settings
	 */
	function settings_modify_column($data)
	{
		if ($data['ee_action'] == 'delete')
		{
			// delete any relationships created by this field
			$this->EE->db->where('parent_field_id', $data['field_id'])
			             ->delete('playa_relationships');
		}

		// just return the default column settings
		return parent::settings_modify_column($data);
	}

	/**
	 * Modify exp_matrix_data Column Settings
	 */
	function settings_modify_matrix_column($data)
	{
		if ($data['matrix_action'] == 'delete')
		{
			// delete any relationships created by this column
			$this->EE->db->where('parent_col_id', $data['col_id'])
			             ->delete('playa_relationships');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Delete Entries
	 */
	function delete($entry_ids)
	{
		$this->EE->db->where_in('parent_entry_id', $entry_ids)
		             ->or_where_in('child_entry_id', $entry_ids)
		             ->delete('playa_relationships');
	}

	/**
	 * Delete Rows
	 */
	function delete_rows($row_ids)
	{
		$this->EE->db->where_in('parent_row_id', $row_ids)
		             ->delete('playa_relationships');
	}

	// --------------------------------------------------------------------

	/**
	 * Module Tag Alias
	 */
	private function _mod_tag_alias($params, $tagdata, $func)
	{
		// cache the row data
		if (! isset($this->cache['entry_rows'][$this->row['entry_id']]))
		{
			$this->cache['entry_rows'][$this->row['entry_id']] =& $this->row;
		}

		$params['entry_id'] = $this->row['entry_id'];
		$params['field_id'] = $this->field_id;

		if (isset($this->row_id))
		{
			$params['col_id'] = $this->col_id;
			$params['row_id'] = $this->row_id;
		}

		return $this->helper->mod_tag_alias($params, $tagdata, $func);
	}

	// --------------------------------------------------------------------

	/**
	 * Replace Tag
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		return $this->_mod_tag_alias($params, $tagdata, 'children');
	}

	/**
	 * Unordered List
	 */
	function replace_ul($data, $params = array())
	{
		return "<ul>\n"
		     .   $this->_mod_tag_alias($params, "  <li>{title}</li>\n", 'children')
		     . '</ul>';
	}

	/**
	 * Ordered List
	 */
	function replace_ol($data, $params = array())
	{
		return "<ol>\n"
		     .   $this->_mod_tag_alias($params, "  <li>{title}</li>\n", 'children')
		     . '</ol>';
	}

	/**
	 * Total Children
	 */
	function replace_total_children($data, $params = array(), $tagdata = FALSE)
	{
		return $this->_mod_tag_alias($params, $tagdata, 'total_children');
	}

	/**
	 * Child IDs
	 */
	function replace_child_ids($data, $params = array(), $tagdata = FALSE)
	{
		return $this->_mod_tag_alias($params, $tagdata, 'child_ids');
	}

	// --------------------------------------------------------------------

	/**
	 * Replace Total Related Entries (deprecated)
	 */
	function replace_total_related_entries($data, $params = array(), $tagdata = FALSE)
	{
		return $this->replace_total_children($data, $params, $tagdata);
	}

	/**
	 * Replace Entry IDs (deprecated)
	 */
	function replace_entry_ids($data, $params = array(), $tagdata = FALSE)
	{
		return $this->replace_child_ids($data, $params, $tagdata);
	}

}

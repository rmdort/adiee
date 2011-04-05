<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Text Celltype Class for EE2
 * 
 * @package   Matrix
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2011 Pixel & Tonic, Inc
 */
class Matrix_text_ft {

	var $info = array(
		'name' => 'Text'
	);

	var $default_settings = array(
		'maxl' => '',
		'multiline' => 'n',
		'fmt' => 'none',
		'dir' => 'ltr',
		'content' => 'any'
	);

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->EE =& get_instance();

		// -------------------------------------------
		//  Prepare Cache
		// -------------------------------------------

		if (! isset($this->EE->session->cache['matrix']['celltypes']['text']))
		{
			$this->EE->session->cache['matrix']['celltypes']['text'] = array();
		}
		$this->cache =& $this->EE->session->cache['matrix']['celltypes']['text'];
	}

	/**
	 * Prep Settings
	 */
	private function _prep_settings(&$settings)
	{
		$settings = array_merge($this->default_settings, $settings);
	}

	// --------------------------------------------------------------------

	/**
	 * Display Cell Settings
	 */
	function display_cell_settings($data)
	{
		$this->_prep_settings($data);

		return array(
			array(lang('maxl'), form_input('maxl', $data['maxl'], 'class="matrix-textarea"')),
			array(lang('multiline'), form_checkbox('multiline', 'y', ($data['multiline'] == 'y'))),
			array(lang('formatting'), form_dropdown('fmt', $data['field_fmt_options'], $data['fmt'])),
			//array(lang('direction'), form_dropdown('dir', array('ltr'=>lang('ltr'), 'rtl'=>lang('rtl')), $data['dir'])),
			array(lang('content'), form_dropdown('content', $data['field_content_options_text'], $data['content']))
		);
	}

	/**
	 * Modify exp_matrix_data Column Settings
	 */
	function settings_modify_matrix_column($data)
	{
		// decode the field settings
		$settings = unserialize(base64_decode($data['col_settings']));

		if (isset($settings['content']))
		{
			switch ($settings['content'])
			{
				case 'integer':
					return array('col_id_'.$data['col_id'] => array('type' => 'int', 'default' => 0));

				case 'numeric':
					return array('col_id_'.$data['col_id'] => array('type' => 'float', 'default' => 0));
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Display Cell
	 */
	function display_cell($data)
	{
		$this->_prep_settings($this->settings);

		if (! isset($this->cache['displayed']))
		{
			// include matrix_text.js
			$theme_url = $this->EE->session->cache['matrix']['theme_url'];
			$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$theme_url.'scripts/matrix_text.js"></script>');

			$this->cache['displayed'] = TRUE;
		}

		$r['class'] = 'matrix-text';
		$r['data'] = '<textarea class="matrix-textarea" name="'.$this->cell_name.'" rows="1" dir="'.$this->settings['dir'].'">'.$data.'</textarea>';

		if ($this->settings['maxl'])
		{
			$r['data'] .= '<div><div></div></div>';
		}

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Pre-process
	 */
	function pre_process($data)
	{
		$this->_prep_settings($this->settings);

		return $this->EE->typography->parse_type(
			$this->EE->functions->encode_ee_tags($data),
			array(
				'text_format'	=> $this->settings['fmt'],
				'html_format'	=> $this->row['channel_html_formatting'],
				'auto_links'	=> $this->row['channel_auto_link_urls'],
				'allow_img_url' => $this->row['channel_allow_img_urls']
			)
		);
	}

}

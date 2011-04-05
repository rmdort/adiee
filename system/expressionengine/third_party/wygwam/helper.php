<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Wygwam Helper Class for EE2
*/
class Wygwam_Helper {

	/**
	 * Constructor
	 */
	function Wygwam_Helper()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Toolbar button groupings, based on CKEditor's default "Full" toolbar
	 */
	function tb_groups()
	{
		return array(
			array('Source'),
			array('Save', 'NewPage', 'Preview'),
			array('Templates'),
			array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'),
			array('Print', 'SpellChecker', 'Scayt'),
			array('Undo', 'Redo'),
			array('Find', 'Replace'),
			array('SelectAll', 'RemoveFormat'),
			array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'),
			array('Bold', 'Italic', 'Underline', 'Strike'),
			array('Subscript', 'Superscript'),
			array('NumberedList', 'BulletedList'),
			array('Outdent', 'Indent', 'Blockquote', 'CreateDiv'),
			array('JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'),
			array('Link', 'Unlink', 'Anchor'),
			array('Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'),
			array('Styles'),
			array('Format'),
			array('Font'),
			array('FontSize'),
			array('TextColor', 'BGColor'),
			array('Maximize', 'ShowBlocks'),
			array('About')
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Record of which toolbar items are selects
	 */
	function tb_selects()
	{
		return array('Styles', 'Format', 'Font', 'FontSize');
	}

	/**
	 * Record of which toolbar buttons' class names differ from their input value
	 */
	function tb_class_overrides()
	{
		return array(
			'SpellChecker'   => 'checkspell',
			'SelectAll'      => 'selectAll',
			'RemoveFormat'   => 'removeFormat'
		);
	}

	/**
	 * The real toolbar button names
	 */
	function tb_label_overrides()
	{
		return array(
			'NewPage'        => 'New Page',
			'PasteText'      => 'Paste As Plain Text',
			'PasteFromWord'  => 'Paste from Word',
			'SpellChecker'   => 'Check Spelling',
			'Scayt'          => 'Spell Check As You Type',
			'SelectAll'      => 'Select All',
			'RemoveFormat'   => 'Remove Format',
			'Radio'          => 'Radio Button',
			'TextField'      => 'Text Field',
			'Select'         => 'Selection Field',
			'ImageButton'    => 'Image Button',
			'HiddenField'    => 'Hidden Field',
			'Strike'         => 'Strike Through',
			'NumberedList'   => 'Insert/Remove Numbered List',
			'BulletedList'   => 'Insert/Remove Bulleted List',
			'Outdent'        => 'Decrease Indent',
			'Indent'         => 'Increase Indent',
			'CreateDiv'      => 'Create Div Container',
			'JustifyLeft'    => 'Left Justify',
			'JustifyRight'   => 'Right Justify',
			'JustifyCenter'  => 'Center Justify',
			'JustifyBlock'   => 'Block Justify',
			'HorizontalRule' => 'Insert Horizontal Line',
			'SpecialChar'    => 'Insert Special Character',
			'PageBreak'      => 'Insert Page Break for Printing',
			'Format'         => 'Format',
			'Font'           => 'Font',
			'FontSize'       => 'Size',
			'TextColor'      => 'Text Color',
			'BGColor'        => 'Background Color',
			'ShowBlocks'     => 'Show Blocks',
			'About'          => 'About CKEditor'
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Mapping of EE languages to CKEditor language codes
	 */
	function lang_map()
	{
		return array(
			'arabic'              => 'ar',
			'arabic-utf8'         => 'ar',
			'arabic-windows-1256' => 'ar',
			'czech'               => 'cs',
			'cesky'               => 'cs',
			'danish'              => 'da',
			'german'              => 'de',
			'deutsch'             => 'de',
			'english'             => 'en',
			'spanish'             => 'es',
			'spanish_ee201pb'     => 'es',
			'finnish'             => 'fi',
			'french'              => 'fr',
			'hungarian'           => 'hu',
			'croatian'            => 'hr',
			'italian'             => 'it',
			'japanese'            => 'ja',
			'korean'              => 'ko',
			'dutch'               => 'nl',
			'norwegian'           => 'no',
			'polish'              => 'pl',
			'brazilian'           => 'pt',
			'portuguese'          => 'pt',
			'brasileiro'          => 'pt',
			'brasileiro_160'      => 'pt',
			'russian'             => 'ru',
			'russian_utf8'        => 'ru',
			'russian_win1251'     => 'ru',
			'slovak'              => 'sk',
			'swedish'             => 'sv',
			'swedish_ee20pb'      => 'sv',
			'turkish'             => 'tr',
			'ukrainian'           => 'uk',
			'chinese'             => 'zh',
			'chinese_traditional' => 'zh',
			'chinese_simplified'  => 'zh'
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Default Config
	 */
	function default_config_settings()
	{
		$toolbars = $this->default_toolbars();

		return array(
			'toolbar' => $toolbars['Basic'],
			'height' => 200,
			'resize_enabled' => 'y',
			'contentsCss' => array(),
			'upload_dir' => ''
		);
	}

	/**
	 * Default Configs
	 */
	function default_toolbars()
	{
		return array(
			'Basic' => array('Bold','Italic','Underline','Strike','NumberedList','BulletedList','Link','Unlink','Anchor','About'),
			'Full'  => array('Source','Save','NewPage','Preview','Templates','Cut','Copy','Paste','PasteText','PasteFromWord','Print','SpellChecker','Scayt','Undo','Redo','Find','Replace','SelectAll','RemoveFormat','Form','Checkbox','Radio','TextField','Textarea','Select','Button','ImageButton','HiddenField','/',
			                 'Bold','Italic','Underline','Strike','Subscript','Superscript','NumberedList','BulletedList','Outdent','Indent','Blockquote','CreateDiv','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','Link','Unlink','Anchor','Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','/',
			                 'Styles','Format','Font','FontSize','TextColor','BGColor','Maximize','ShowBlocks','About')
		);
	}

	/**
	 * Default Global Settings
	 */
	function default_global_settings()
	{
		return array(
			'license_key' => ''
		);
	}

	/**
	 * Default Field Settings
	 */
	function default_settings()
	{
		return array(
			'upload_dir' => ''
		);
	}

	function base_config()
	{
		return array_merge(array(
			'skin'               => 'wygwam2',
			'toolbarCanCollapse' => 'n',
			'dialog_backgroundCoverColor' => '#262626',
			'dialog_backgroundCoverOpacity' => 0.85,
			'entities_processNumerical' => 'y'
		), $this->default_config_settings());
	}

	// --------------------------------------------------------------------

	/**
	 * Wygwam Custom Toolbar
	 * 
	 * Converts flat array of buttons into multi-dimentional
	 * array of toolgroups and their buttons
	 */
	function custom_toolbar($buttons, $include_missing = FALSE)
	{
		$toolbar = array();

		//foreach ($buttons as $button)
		//{
		//	$toolbar[] = ($button == '/' ? '/' : array($button));
		//}
		//return $toolbar;

		// group buttons by toolgroup
		$tb_groups = $this->tb_groups();
		foreach($tb_groups as $group_index => &$group)
		{
			$group_selection_index = NULL;
			$missing = array();
			foreach($group as $button_index => &$button)
			{
				// selected?
				if (($button_selection_index = array_search($button, $buttons)) !== FALSE)
				{
					if ($group_selection_index === NULL) $group_selection_index = $button_selection_index;
					if ( ! isset($toolbar[$group_selection_index])) $toolbar[$group_selection_index] = array();
					$toolbar[$group_selection_index]['b'.$button_index] = $button;
				}
				else if ($include_missing)
				{
					$missing['b'.$button_index] = '!'.$button;
				}
			}
			if ($group_selection_index !== NULL)
			{
				if ($include_missing) $toolbar[$group_selection_index] = array_merge($missing, $toolbar[$group_selection_index]);
				ksort($toolbar[$group_selection_index]);
				$toolbar[$group_selection_index] = array_values($toolbar[$group_selection_index]);
			}
		}

		// add newlines
		foreach(array_keys($buttons, '/') as $key) $toolbar[$key] = '/';

		// sort by keys and remove them
		ksort($toolbar);
		$r = array();
		foreach($toolbar as $toolgroup) array_push($r, $toolgroup);
		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Config Booleans
	 */
	function config_booleans()
	{
		return array(
			'colorButton_enableMore',
			'disableNativeSpellChecker',
			'disableObjectResizing',
			'editingBlock',
			'entities',
			'entities_greek',
			'entities_latin',
			'entities_processNumerical',
			'forceEnterMode',
			'forcePasteAsPlainText',
			'forceSimpleAmpersand',
			'fullPage',
			'htmlEncodeOutput',
			'ignoreEmptyParagraph',
			'image_removeLinkByEmptyURL',
			'pasteFromWordNumberedHeadingToList',
			'pasteFromWordPromptCleanup',
			'pasteFromWordRemoveFontStyles',
			'pasteFromWordRemoveStyles',
			'resize_enabled',
			'startupFocus',
			'startupOutlineBlocks',
			'templates_replaceContent',
			'toolbarCanCollapse',
			'toolbarStartupExpanded'
		);
	}

	/**
	 * Config Lists
	 */
	function config_lists()
	{
		return array(
			'contentsCss',
			'templates_files'
		);
	}

	/**
	 * Config Literals
	 */
	function config_literals()
	{
		return array(
			'enterMode'
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Theme URL
	 */
	function theme_url()
	{
		if (! isset($this->cache['theme_url']))
		{
			$theme_folder_url = $this->EE->config->item('theme_folder_url');
			if (substr($theme_folder_url, -1) != '/') $theme_folder_url .= '/';
			$this->cache['theme_url'] = $theme_folder_url.'third_party/wygwam/';
		}

		return $this->cache['theme_url'];
	}

	/**
	 * Include Theme CSS
	 */
	function include_theme_css($file)
	{
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->theme_url().$file.'" />');
	}

	/**
	 * Include Theme JS
	 */
	function include_theme_js($file)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$this->theme_url().$file.'"></script>');
	}

	/**
	 * Insert JS
	 */
	function insert_js($js)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript">'.$js.'</script>');
	}

}

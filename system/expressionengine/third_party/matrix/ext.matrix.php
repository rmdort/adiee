<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


require_once PATH_THIRD.'matrix/config.php';


/**
 * Matrix Extension Class for ExpressionEngine 2
 *
 * @package   Matrix
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2011 Pixel & Tonic, Inc
 */
class Matrix_ext {

	var $name = MATRIX_NAME;
	var $version = MATRIX_VER;
	var $settings_exist = 'n';
	var $docs_url = 'http://pixelandtonic.com/matrix';

	/**
	 * Extension Constructor
	 */
	function __construct()
	{
		$this->EE =& get_instance();

		// -------------------------------------------
		//  Prepare Cache
		// -------------------------------------------

		if (! isset($this->EE->session->cache['matrix']))
		{
			$this->EE->session->cache['matrix'] = array();
		}
		$this->cache =& $this->EE->session->cache['matrix'];
	}

	// --------------------------------------------------------------------

	/**
	 * Activate Extension
	 */
	function activate_extension()
	{
		$this->EE->db->insert('extensions', array(
			'class'    => 'Matrix_ext',
			'method'   => 'channel_entries_tagdata',
			'hook'     => 'channel_entries_tagdata',
			'settings' => '',
			'priority' => 10,
			'version'  => $this->version,
			'enabled'  => 'y'
		));
	}

	/**
	 * Update Extension
	 */
	function update_extension($current = FALSE)
	{
		if (! $current || $current == $this->version)
		{
			return FALSE;
		}

		$this->EE->db->where('class', 'Matrix_ext');
		$this->EE->db->update('extensions', array('version' => $this->version));
	}

	/**
	 * Disable Extension
	 */
	function disable_extension()
	{
		$this->EE->db->query('DELETE FROM exp_extensions WHERE class = "Matrix_ext"');
	}

	// --------------------------------------------------------------------

	/**
	 * channel_entries_tagdata hook
	 */
	function channel_entries_tagdata($tagdata, $row, &$Channel)
	{
		// has this hook already been called?
		if ($this->EE->extensions->last_call)
		{
			$tagdata = $this->EE->extensions->last_call;
		}

		return $tagdata;
	}
}

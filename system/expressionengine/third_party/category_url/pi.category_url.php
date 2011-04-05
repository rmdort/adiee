<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' 		=> 'Category URL',
  'pi_version' 		=> '1.0',
  'pi_author'	 	=> 'Johan Strömqvist',
  'pi_author_url' 	=> 'http://www.naboovalley.com/',
  'pi_description' 	=> 'Create your category URL structure with on single tag',
  'pi_usage' 		=> Category_url::usage()
  );

/**
 *  Category URL Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			Johan Strömqvist
 * @copyright		Copyright (c) 2011, Johan Strömqvist
 * @link			http://www.naboovalley.com
 */

class Category_url
{

	var $return_data = "";

	// --------------------------------------------------------------------

	/**
	 * Category URL
	 *
	 * Returns url for category
	 *
	 * @access	public
	 * @return	string
	 */

	function Category_url()
	{
		$this->EE 				=& get_instance(); 
		
		$site_index 			= $this->EE->functions->fetch_site_index();
		
		$parent_id				= $this->EE->TMPL->fetch_param('parent_id');
		$category_id			= $this->EE->TMPL->fetch_param('category_id');
		$category_url_title		= $this->EE->TMPL->fetch_param('category_url_title');
		$pathinfo		= $this->EE->TMPL->fetch_param('pathinfo');
		
		// If parent_id = 0
		 if(!isset($this->EE->session->cache['fetch_url']['url'][$category_id]) && $parent_id == 0)
		 {
			$this->EE->session->cache['fetch_url']['url'][$category_id] = $category_url_title;
		 } 
		 else 
		 {
			// If parent_id > 0 (is subcategory)
			if(!isset($this->EE->session->cache['fetch_url']['url'][$category_id]))
			{
				$this->EE->session->cache['fetch_url']['url'][$category_id] = $this->EE->session->cache['fetch_url']['url'][$parent_id] ."/". $category_url_title;
			}
		 }
		 
		 $this->return_data = $site_index .$pathinfo. $this->EE->session->cache['fetch_url']['url'][$category_id];
	}

	// --------------------------------------------------------------------

	/**
	 * Usage
	 *
	 * This function describes how the plugin is used.
	 *
	 * @access	public
	 * @return	string
	 */
	function usage()
	{
	ob_start(); 
	?>
This plugin returns the full URL for one specific category based on it's parent's category_url_title's.

HOW TO USE
==========================================================

{exp:channel:categories channel="foo" style="nested"}

<a href="{exp:category_url parent_id="{parent_id}" category_url_title="{category_url_title}" category_id="{category_id}"} ">{category_name}</a>
	
{/exp:channel:categories}

==========================================================
	<?php
	$buffer = ob_get_contents();
	
	ob_end_clean(); 

	return $buffer;
	}
	
	// END

}
/* End of file */
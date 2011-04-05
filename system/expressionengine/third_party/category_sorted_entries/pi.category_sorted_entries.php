<?php

/*
=====================================================

RogEE "Category Sorted Entries"
a plug-in for ExpressionEngine 2
by Michael Rog
v1.0.0

Please e-mail me with questions, feedback, suggestions, bugs, etc.
>> michael@michaelrog.com
>> http://michaelrog.com/ee

This plugin is compatible with NSM Addon Updater:
>> http://github.com/newism/nsm.addon_updater.ee_addon

Changelog:
0.0.1 - alpha: filtering by entry_id and display by group_id
0.0.2 - beta: improved filtering by entry_id, added filtering by category, more variables
1.0.0 - release: cleaned up the file, added BitBucket details

=====================================================

*/

if (! defined('BASEPATH') && ! defined('EXT')) exit('No direct script access allowed');

$plugin_info = array(
						'pi_name'			=> 'RogEE Category Sorted Entries',
						'pi_version'		=> '1.0.0',
						'pi_author'			=> 'Michael Rog',
						'pi_author_url'		=> 'http://michaelrog.com/ee',
						'pi_description'	=> 'Like the built-in EE Category Archive, but way mo\' better (with additional variables and parameters for added control.)',
						'pi_usage'			=> Category_sorted_entries::usage()
					);

/** ------------------------------------------------------------------------
/**  Category_sorted_entries
/** ------------------------------------------------------------------------*/

class Category_sorted_entries {


	var $limit	= '100';	// Default maximum query results if not specified.

	// These variable are all set dynamically

	var $query;
	var $return_data			= '';	 	// Final data
	var $catfields				= array();
	var $reserved_cat_segment 	= '';
	var $use_category_names		= FALSE;
	var $enable					= array();	// modified by various tags with disable= parameter

	// These are used with the nested category trees

	var $category_list  		= array();
	var $cat_full_array			= array();
	var $cat_array				= array();
	var $temp_array				= array();
	var $category_count			= 0;

	// These are used in filtering entries
	
	var $entries_list	= array();
	var $filter_by_entries	= FALSE;
	var $entries_not_list	= array();
	var $filter_by_entries_not	= FALSE;	

	// SQL Caching

	var $sql_cache_dir			= 'sql_cache/';

	// Misc. - Class variable usable by extensions
	var $misc					= FALSE;
	

	/** ------------------------------------------------------------------------
	/**  Constructor
	/** ------------------------------------------------------------------------*/

	function Category_sorted_entries($str='')
	{

		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();

		$this->p_limit = $this->limit;

		$this->query_string = ($this->EE->uri->page_query_string != '') ? $this->EE->uri->page_query_string : $this->EE->uri->query_string;

		if ($this->EE->config->item("use_category_name") == 'y' && $this->EE->config->item("reserved_category_word") != '')
		{
			$this->use_category_names	= $this->EE->config->item("use_category_name");
			$this->reserved_cat_segment	= $this->EE->config->item("reserved_category_word");
		}

		// a number tags utilize the disable= parameter, set it here
		if (isset($this->EE->TMPL) && is_object($this->EE->TMPL))
		{
			$this->_fetch_disable_param();
		}

		$this->return_data = $this->category_archive();

	} // END Category_sorted_entries() constructor


	/** ------------------------------------------------------------------------
	/**  Category archives
	/** ------------------------------------------------------------------------*/

	function category_archive()
	{
		$sql = "SELECT DISTINCT cat_group, channel_id FROM exp_channels WHERE site_id IN ('".implode("','", $this->EE->TMPL->site_ids)."') ";

		if ($channel = $this->EE->TMPL->fetch_param('channel'))
		{
			$sql .= $this->EE->functions->sql_andor_string($this->EE->TMPL->fetch_param('channel'), 'channel_name');
		}

		$query = $this->EE->db->query($sql);

		if ($query->num_rows() != 1)
		{
			return '';
		}

		$group_id = $query->row('cat_group') ;
		$channel_id = $query->row('channel_id') ;

		/* */
		// GROUP_ID param
		// If there is a "group_id" specified, remove entires from $cat_group which are not in "group_id"
		// (This is to cause entries to be displayed by the specified category groups.)
		// ------------
		
		if ($this->EE->TMPL->fetch_param('group_id') !== FALSE)
		{
			$group_ids = explode('|', trim($group_id));
			
			if (strncmp($this->EE->TMPL->fetch_param('group_id'), 'not ', 4) == 0)
			{
				$not_these_groups = explode('|', trim(substr($this->EE->TMPL->fetch_param('group_id'), 3)));
				$group_ids = array_diff($group_ids, $not_these_groups);
			}
			else
			{
				$these_groups = explode('|', trim($this->EE->TMPL->fetch_param('group_id')));
				$group_ids = array_intersect($group_ids, $these_groups);
			}
			
			$group_id = implode("|", $group_ids);
			unset($group_ids, $not_these_groups, $these_groups);
		}
		
		if ($group_id == "")
		{
			return '';
		}
		
		// ------------
		// end GROUP_ID param
		/* */
		

		/* */
		// ENTRY_ID param
		// If there is an "entry_id" specified, establish entries list and turn the switch on.
		// (This is Step 1 in filtering the entries by entry_id.)
		// ------------
		
		if ($this->EE->TMPL->fetch_param('entry_id') !== FALSE)
		{
			if (strncmp($this->EE->TMPL->fetch_param('entry_id'), 'not ', 4) == 0)
			{
				$this->entries_not_list = explode('|', trim(substr($this->EE->TMPL->fetch_param('entry_id'), 3)));
				$this->filter_by_entries_not = TRUE;
			}
			else
			{
				$this->entries_list = explode('|', trim($this->EE->TMPL->fetch_param('entry_id')));
				$this->filter_by_entries = TRUE;
			}
		}
		
		// ------------
		// end ENTRY_ID param
		/* */


		/* */
		// CATEGORY param
		// If there is a "category" specified, amend/activate the filter lists accordingly.
		// ------------
		
		if ($this->EE->TMPL->fetch_param('category') !== FALSE)
		{
			
			// First, get a list of all the entires that DO match the set of categories in the param.
			
			$category_sql = "SELECT DISTINCT entry_id FROM exp_category_posts WHERE cat_id IN ";
			
			if (strncmp($this->EE->TMPL->fetch_param('category'), 'not ', 4) == 0)
			{
				$not_these_categories = explode('|', trim(substr($this->EE->TMPL->fetch_param('category'), 3)));
				$category_sql .= "('".implode("','", $not_these_categories)."')";
			}
			else
			{
				$these_categories = explode('|', trim($this->EE->TMPL->fetch_param('category')));
				$category_sql .= "('".implode("','", $these_categories)."')";
			}
			
			$category_query = $this->EE->db->query($category_sql);
			
			$matched_entries = array();
			
			if ($category_query->num_rows() > 0)
			{
  				foreach ($category_query->result() as $row)
   				{
	     			$matched_entries[] = $row->entry_id;
     			}
     		}
     		
     		// If the param includes a NOT, add these matching entries to the NOT list.
			
			if (strncmp($this->EE->TMPL->fetch_param('category'), 'not ', 4) == 0)
			{
				$this->entries_not_list = array_merge($matched_entries, $this->entries_not_list);
				$this->filter_by_entries_not = TRUE;
			}
			
			// Else, the param doesn't include a NOT; these matches act as filters.
			
			else
			{
				if ($this->filter_by_entries)
				{
					$this->entries_list = array_intersect($matched_entries, $this->entries_list);
				}
				else
				{
					$this->entries_list = array_merge($matched_entries, $this->entries_list);
				}
				
				$this->filter_by_entries = TRUE;
			}
			
			unset($category_sql, $category_query, $matched_entries, $these_categories, $not_these_categories);
			
		}
		
		// ------------
		// end CATEGORY param
		/* */
		

		$sql = "SELECT exp_category_posts.cat_id, exp_channel_titles.entry_id, exp_channel_titles.title, exp_channel_titles.url_title, exp_channel_titles.entry_date
				FROM exp_channel_titles, exp_category_posts
				WHERE channel_id = '$channel_id'
				AND exp_channel_titles.entry_id = exp_category_posts.entry_id ";


		/* */
		// FILTER BY ENTRY LISTS
		// Tack on a bit of query to include only the entries that are/aren't in the set we have specied.
		// (This block takes effect when style="nested" is used.)
		// ------------
		if ($this->filter_by_entries_not)
		{
			$sql .= "AND exp_channel_titles.entry_id NOT IN ('".implode("','", $this->entries_not_list)."') ";
		}
		if ($this->filter_by_entries)
		{
			$sql .= "AND exp_channel_titles.entry_id IN ('".implode("','", $this->entries_list)."') ";
		}
		// ------------
		// end filter block
		/* */

				
		$timestamp = ($this->EE->TMPL->cache_timestamp != '') ? $this->EE->localize->set_gmt($this->EE->TMPL->cache_timestamp) : $this->EE->localize->now;

		if ($this->EE->TMPL->fetch_param('show_future_entries') != 'yes')
		{
			$sql .= "AND exp_channel_titles.entry_date < ".$timestamp." ";
		}

		if ($this->EE->TMPL->fetch_param('show_expired') != 'yes')
		{
			$sql .= "AND (exp_channel_titles.expiration_date = 0 OR exp_channel_titles.expiration_date > ".$timestamp.") ";
		}

		$sql .= "AND exp_channel_titles.status != 'closed' ";

		if ($status = $this->EE->TMPL->fetch_param('status'))
		{
			$status = str_replace('Open',	'open',	$status);
			$status = str_replace('Closed', 'closed', $status);

			$sql .= $this->EE->functions->sql_andor_string($status, 'exp_channel_titles.status');
		}
		else
		{
			$sql .= "AND exp_channel_titles.status = 'open' ";
		}

		if ($this->EE->TMPL->fetch_param('show') !== FALSE)
		{
			$sql .= $this->EE->functions->sql_andor_string($this->EE->TMPL->fetch_param('show'), 'exp_category_posts.cat_id').' ';
		}


		$orderby  = $this->EE->TMPL->fetch_param('orderby');

		switch ($orderby)
		{
			case 'date'					: $sql .= "ORDER BY exp_channel_titles.entry_date";
				break;
			case 'expiration_date'		: $sql .= "ORDER BY exp_channel_titles.expiration_date";
				break;
			case 'title'				: $sql .= "ORDER BY exp_channel_titles.title";
				break;
			case 'comment_total'		: $sql .= "ORDER BY exp_channel_titles.entry_date";
				break;
			case 'most_recent_comment'	: $sql .= "ORDER BY exp_channel_titles.recent_comment_date desc, exp_channel_titles.entry_date";
				break;
			default						: $sql .= "ORDER BY exp_channel_titles.title";
				break;
		}

		$sort = $this->EE->TMPL->fetch_param('sort');

		switch ($sort)
		{
			case 'asc'	: $sql .= " asc";
				break;
			case 'desc'	: $sql .= " desc";
				break;
			default		: $sql .= " asc";
				break;
		}

		$result = $this->EE->db->query($sql);
		$channel_array = array();

		$parent_only = ($this->EE->TMPL->fetch_param('parent_only') == 'yes') ? TRUE : FALSE;

		$cat_chunk  = (preg_match("/".LD."categories\s*".RD."(.*?)".LD.'\/'."categories\s*".RD."/s", $this->EE->TMPL->tagdata, $match)) ? $match[1] : '';

		$c_path = array();

		if (preg_match_all("#".LD."path(=.+?)".RD."#", $cat_chunk, $matches))
		{
			for ($i = 0; $i < count($matches[0]); $i++)
			{
				$c_path[$matches[0][$i]] = $this->EE->functions->create_url($this->EE->functions->extract_path($matches[1][$i]));
			}
		}

		$tit_chunk = (preg_match("/".LD."entry_titles\s*".RD."(.*?)".LD.'\/'."entry_titles\s*".RD."/s", $this->EE->TMPL->tagdata, $match)) ? $match[1] : '';

		$t_path = array();

		if (preg_match_all("#".LD."path(=.+?)".RD."#", $tit_chunk, $matches))
		{
			for ($i = 0; $i < count($matches[0]); $i++)
			{
				$t_path[$matches[0][$i]] = $this->EE->functions->create_url($this->EE->functions->extract_path($matches[1][$i]));
			}
		}

		$id_path = array();

		if (preg_match_all("#".LD."entry_id_path(=.+?)".RD."#", $tit_chunk, $matches))
		{
			for ($i = 0; $i < count($matches[0]); $i++)
			{
				$id_path[$matches[0][$i]] = $this->EE->functions->create_url($this->EE->functions->extract_path($matches[1][$i]));
			}
		}

		$entry_date = array();

		preg_match_all("/".LD."entry_date\s+format\s*=\s*(\042|\047)([^\\1]*?)\\1".RD."/s", $tit_chunk, $matches);
		{
			$j = count($matches[0]);
			for ($i = 0; $i < $j; $i++)
			{
				$matches[0][$i] = str_replace(array(LD,RD), '', $matches[0][$i]);

				$entry_date[$matches[0][$i]] = $this->EE->localize->fetch_date_params($matches[2][$i]);
			}
		}

		$str = '';

		if ($this->EE->TMPL->fetch_param('style') == '' OR $this->EE->TMPL->fetch_param('style') == 'nested')
		{
			if ($result->num_rows() > 0 && $tit_chunk != '')
			{
					$i = 0;
				foreach($result->result_array() as $row)
				{
					$chunk = "<li>".str_replace(LD.'category_name'.RD, '', $tit_chunk)."</li>";

					foreach($t_path as $tkey => $tval)
					{
						$chunk = str_replace($tkey, $this->EE->functions->remove_double_slashes($tval.'/'.$row['url_title']), $chunk);
					}

					foreach($id_path as $tkey => $tval)
					{
						$chunk = str_replace($tkey, $this->EE->functions->remove_double_slashes($tval.'/'.$row['entry_id']), $chunk);
					}

					foreach($this->EE->TMPL->var_single as $key => $val)
					{
						if (isset($entry_date[$key]))
						{
							$val = str_replace($entry_date[$key], $this->EE->localize->convert_timestamp($entry_date[$key], $row['entry_date'], TRUE), $val);
							$chunk = $this->EE->TMPL->swap_var_single($key, $val, $chunk);
						}

					}

					/* */
					// {entry_id}, {url_title}
					// An extra replace statement to allow display of entry_id and url_title variables.
					// (This block takes effect when style="nested" is used.)
					// ------------
					
					$chunk = str_replace(array(LD.'entry_id'.RD, LD.'url_title'.RD),
										 array($row['entry_id'],$row['url_title']),
										 $chunk);
										 
					// ------------
					// end {entry_id}, {url_title} replacement
					/* */	


					$channel_array[$i.'_'.$row['cat_id']] = str_replace(LD.'title'.RD, $row['title'], $chunk);
					$i++;
				}
			}

			$this->category_tree(
									array(
											'group_id'		=> $group_id,
											'channel_id'		=> $channel_id,
											'path'			=> $c_path,
											'template'		=> $cat_chunk,
											'channel_array' 	=> $channel_array,
											'parent_only'	=> $parent_only,
											'show_empty'	=> $this->EE->TMPL->fetch_param('show_empty'),
											'strict_empty'	=> 'yes'										
										  )
								);

			if (count($this->category_list) > 0)
			{
				$id_name = ($this->EE->TMPL->fetch_param('id') === FALSE) ? 'nav_cat_archive' : $this->EE->TMPL->fetch_param('id');
				$class_name = ($this->EE->TMPL->fetch_param('class') === FALSE) ? 'nav_cat_archive' : $this->EE->TMPL->fetch_param('class');

				$this->category_list[0] = '<ul id="'.$id_name.'" class="'.$class_name.'">'."\n";

				foreach ($this->category_list as $val)
				{
					$str .= $val;
				}
			}
		}
		else
		{
			// fetch category field names and id's

			if ($this->enable['category_fields'] === TRUE)
			{
				$query = $this->EE->db->query("SELECT field_id, field_name FROM exp_category_fields
									WHERE site_id IN ('".implode("','", $this->EE->TMPL->site_ids)."')
									AND group_id IN ('".str_replace('|', "','", $this->EE->db->escape_str($group_id))."')
									AND group_id NOT IN ('WOOHOO-LINE-372')");

				if ($query->num_rows() > 0)
				{
					foreach ($query->result_array() as $row)
					{
						$this->catfields[] = array('field_name' => $row['field_name'], 'field_id' => $row['field_id']);
					}
				}

				$field_sqla = ", cg.field_html_formatting, fd.* ";
				$field_sqlb = " LEFT JOIN exp_category_field_data AS fd ON fd.cat_id = c.cat_id
								LEFT JOIN exp_category_groups AS cg ON cg.group_id = c.group_id ";
			}
			else
			{
				$field_sqla = '';
				$field_sqlb = '';
			}

			$sql = "SELECT DISTINCT (c.cat_id), c.cat_name, c.cat_url_title, c.cat_description, c.cat_image, c.group_id, c.parent_id {$field_sqla}
					FROM (exp_categories AS c";

			if ($this->EE->TMPL->fetch_param('show_empty') != 'no' AND $channel_id != '')
			{
				$sql .= ", exp_category_posts ";
			}

			$sql .= ") {$field_sqlb}";

			if ($this->EE->TMPL->fetch_param('show_empty') == 'no')
			{
				$sql .= " LEFT JOIN exp_category_posts ON c.cat_id = exp_category_posts.cat_id ";

				if ($channel_id != '')
				{
					$sql .= " LEFT JOIN exp_channel_titles ON exp_category_posts.entry_id = exp_channel_titles.entry_id ";
				}
			}
			
			$sql .= " WHERE c.group_id IN ('".str_replace('|', "','", $this->EE->db->escape_str($group_id))."')
						AND c.group_id NOT IN ('WOOHOO-LINE-413') ";
			
			/* */
			// FILTER BY ENTRY LISTS
			// Tack on a bit of query to include only the entries that are/aren't in the set we have specied.
			// (This block takes effect when style="linear" is used.)
			// ------------
			if ($this->filter_by_entries_not)
			{
				$sql .= "AND exp_category_posts.entry_id NOT IN ('".implode("','", $this->entries_not_list)."') ";
			}
			if ($this->filter_by_entries)
			{
				$sql .= "AND exp_category_posts.entry_id IN ('".implode("','", $this->entries_list)."') ";
			}
			// ------------
			// end filter block
			/* */			
			

			if ($this->EE->TMPL->fetch_param('show_empty') == 'no')
			{
				if ($channel_id != '')
				{
					$sql .= "AND exp_channel_titles.channel_id = '".$channel_id."' ";
				}
				else
				{
					$sql .= " AND exp_channel_titles.site_id IN ('".implode("','", $this->EE->TMPL->site_ids)."') ";
				}

				if ($status = $this->EE->TMPL->fetch_param('status'))
				{
					$status = str_replace('Open',	'open',	$status);
					$status = str_replace('Closed', 'closed', $status);

					$sql .= $this->EE->functions->sql_andor_string($status, 'exp_channel_titles.status');
				}
				else
				{
					$sql .= "AND exp_channel_titles.status = 'open' ";
				}

				if ($this->EE->TMPL->fetch_param('show_empty') == 'no')
				{
					$sql .= "AND exp_category_posts.cat_id IS NOT NULL ";
				}
			}

			if ($this->EE->TMPL->fetch_param('show') !== FALSE)
			{
				$sql .= $this->EE->functions->sql_andor_string($this->EE->TMPL->fetch_param('show'), 'c.cat_id').' ';
			}

			if ($parent_only == TRUE)
			{
				$sql .= " AND c.parent_id = 0";
			}

			$sql .= " ORDER BY c.group_id, c.parent_id, c.cat_order";
		 	$query = $this->EE->db->query($sql);

			if ($query->num_rows() > 0)
			{
				$this->EE->load->library('typography');
				$this->EE->typography->initialize();

				$this->EE->typography->convert_curly = FALSE;

				$used = array();

				foreach($query->result_array() as $row)
				{
					if ( ! isset($used[$row['cat_name']]))
					{
						$chunk = $cat_chunk;

						$cat_vars = array('category_name'			=> $row['cat_name'],
										  'category_url_title'		=> $row['cat_url_title'],
										  'category_description'	=> $row['cat_description'],
										  'category_image'			=> $row['cat_image'],
										  'category_id'				=> $row['cat_id'],
										  'parent_id'				=> $row['parent_id']
										);

						foreach ($this->catfields as $v)
						{
							$cat_vars[$v['field_name']] = ( ! isset($row['field_id_'.$v['field_id']])) ? '' : $row['field_id_'.$v['field_id']];
						}

						$chunk = $this->EE->functions->prep_conditionals($chunk, $cat_vars);

						$chunk = str_replace( array(LD.'category_id'.RD,
													LD.'category_name'.RD,
													LD.'category_url_title'.RD,
													LD.'category_image'.RD,
													LD.'category_description'.RD,
													LD.'parent_id'.RD),
											  array($row['cat_id'],
											  		$row['cat_name'],
													$row['cat_url_title'],
											  		$row['cat_image'],
											  		$row['cat_description'],
													$row['parent_id']),
											  $chunk);

						foreach($c_path as $ckey => $cval)
						{
							$cat_seg = ($this->use_category_names == TRUE) ? $this->reserved_cat_segment.'/'.$row['cat_url_title'] : 'C'.$row['cat_id'];
							$chunk = str_replace($ckey, $this->EE->functions->remove_double_slashes($cval.'/'.$cat_seg), $chunk);
						}

						// parse custom fields

						foreach($this->catfields as $cfv)
						{
							if (isset($row['field_id_'.$cfv['field_id']]) AND $row['field_id_'.$cfv['field_id']] != '')
							{
								$field_content = $this->EE->typography->parse_type($row['field_id_'.$cfv['field_id']],
																			array(
																				  'text_format'		=> $row['field_ft_'.$cfv['field_id']],
																				  'html_format'		=> $row['field_html_formatting'],
																				  'auto_links'		=> 'n',
																				  'allow_img_url'	=> 'y'
																				)
																		);
								$chunk = str_replace(LD.$cfv['field_name'].RD, $field_content, $chunk);
							}
							else
							{
								// garbage collection
								$chunk = str_replace(LD.$cfv['field_name'].RD, '', $chunk);
							}
						}

						$str .= $chunk;
						$used[$row['cat_name']] = TRUE;
					}

					foreach($result->result_array() as $trow)
					{
						if ($trow['cat_id'] == $row['cat_id'])
						{
							$chunk = str_replace(array(LD.'title'.RD, LD.'category_name'.RD),
												 array($trow['title'],$row['cat_name']),
												 $tit_chunk);
												 
							/* */
							// {entry_id}, {url_title}
							// An extra replace statement to allow display of entry_id and url_title variables.
							// (This block takes effect when style="linear" is used.)
							// ------------
							
							$chunk = str_replace(array(LD.'entry_id'.RD, LD.'url_title'.RD),
												 array($trow['entry_id'],$trow['url_title']),
												 $chunk);
												 
							// ------------
							// end {entry_id}, {url_title} replacement
							/* */	

							foreach($t_path as $tkey => $tval)
							{
								$chunk = str_replace($tkey, $this->EE->functions->remove_double_slashes($tval.'/'.$trow['url_title']), $chunk);
							}

							foreach($id_path as $tkey => $tval)
							{
								$chunk = str_replace($tkey, $this->EE->functions->remove_double_slashes($tval.'/'.$trow['entry_id']), $chunk);
							}

							foreach($this->EE->TMPL->var_single as $key => $val)
							{
								if (isset($entry_date[$key]))
								{
									$val = str_replace($entry_date[$key], $this->EE->localize->convert_timestamp($entry_date[$key], $trow['entry_date'], TRUE), $val);

									$chunk = $this->EE->TMPL->swap_var_single($key, $val, $chunk);
								}

							}

							$str .= $chunk;
						}
					}
				}
			}

			if ($this->EE->TMPL->fetch_param('backspace'))
			{
				$str = substr($str, 0, - $this->EE->TMPL->fetch_param('backspace'));
			}
		}

		return $str;
	}

	// ------------------------------------------------------------------------

	/**
	  *  Category Tree
	  *
	  * This function and the next create a nested, hierarchical category tree
	  */
	function category_tree($cdata = array())
	{
		$default = array('group_id', 'channel_id', 'path', 'template', 'depth', 'channel_array', 'parent_only', 'show_empty', 'strict_empty');

		foreach ($default as $val)
		{
			$$val = ( ! isset($cdata[$val])) ? '' : $cdata[$val];
		}

		if ($group_id == '')
		{
			return FALSE;
		}

		if ($this->enable['category_fields'] === TRUE)
		{
			$query = $this->EE->db->query("SELECT field_id, field_name
								FROM exp_category_fields
								WHERE site_id IN ('".implode("','", $this->EE->TMPL->site_ids)."')
								AND group_id IN ('".str_replace('|', "','", $this->EE->db->escape_str($group_id))."')
								AND group_id NOT IN ('WOOHOO-LINE-605')");

			if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
				{
					$this->catfields[] = array('field_name' => $row['field_name'], 'field_id' => $row['field_id']);
				}
			}

			$field_sqla = ", cg.field_html_formatting, fd.* ";
			$field_sqlb = " LEFT JOIN exp_category_field_data AS fd ON fd.cat_id = c.cat_id
							LEFT JOIN exp_category_groups AS cg ON cg.group_id = c.group_id";
		}
		else
		{
			$field_sqla = '';
			$field_sqlb = '';
		}

		/** -----------------------------------
		/**  Are we showing empty categories
		/** -----------------------------------*/

		// If we are only showing categories that have been assigned to entries
		// we need to run a couple queries and run a recursive function that
		// figures out whether any given category has a parent.
		// If we don't do this we will run into a problem in which parent categories
		// that are not assigned to a channel will be supressed, and therefore, any of its
		// children will be supressed also - even if they are assigned to entries.
		// So... we will first fetch all the category IDs, then only the ones that are assigned
		// to entries, and lastly we'll recursively run up the tree and fetch all parents.
		// Follow that?  No?  Me neither...

		if ($show_empty == 'no')
		{
			// First we'll grab all category ID numbers

			$query = $this->EE->db->query("SELECT cat_id, parent_id FROM exp_categories
								 WHERE group_id IN ('".str_replace('|', "','", $this->EE->db->escape_str($group_id))."')
								 AND group_id NOT IN ('WOOHOO-LINE-635')
								 ORDER BY group_id, parent_id, cat_order");

			$all = array();

			// No categories exist?  Back to the barn for the night..
			if ($query->num_rows() == 0)
			{
				return FALSE;
			}

			foreach($query->result_array() as $row)
			{
				$all[$row['cat_id']] = $row['parent_id'];
			}

			// Next we'l grab only the assigned categories

			$sql = "SELECT DISTINCT(exp_categories.cat_id), parent_id
					FROM exp_categories
					LEFT JOIN exp_category_posts ON exp_categories.cat_id = exp_category_posts.cat_id
					LEFT JOIN exp_channel_titles ON exp_category_posts.entry_id = exp_channel_titles.entry_id ";
			

			$sql .= "WHERE group_id IN ('".str_replace('|', "','", $this->EE->db->escape_str($group_id))."')
						AND group_id NOT IN ('WOOHOO-LINE-669') ";

			$sql .= "AND exp_category_posts.cat_id IS NOT NULL ";
			
			
			/* */
			// FILTER BY ENTRY LISTS
			// Tack on a bit of query to include only the entries that are/aren't in the set we have specied.
			// (This block takes effect when style="nested" is used AND show_empty="no".)
			// ------------
			if ($this->filter_by_entries_not)
			{
				$sql .= "AND exp_channel_titles.entry_id NOT IN ('".implode("','", $this->entries_not_list)."') ";
			}
			if ($this->filter_by_entries)
			{
				$sql .= "AND exp_channel_titles.entry_id IN ('".implode("','", $this->entries_list)."') ";
			}
			// ------------
			// end filter block
			/* */
			
						

			if ($channel_id != '' && $strict_empty == 'yes')
			{
				$sql .= "AND exp_channel_titles.channel_id = '".$channel_id."' ";
			}
			else
			{
				$sql .= "AND exp_channel_titles.site_id IN ('".implode("','", $this->EE->TMPL->site_ids)."') ";
			}

			if (($status = $this->EE->TMPL->fetch_param('status')) !== FALSE)
	        {
				$status = str_replace(array('Open', 'Closed'), array('open', 'closed'), $status);
	            $sql .= $this->EE->functions->sql_andor_string($status, 'exp_channel_titles.status');
	        }
	        else
	        {
	            $sql .= "AND exp_channel_titles.status != 'closed' ";
	        }

			/**------
			/**  We only select entries that have not expired
			/**------*/

			$timestamp = ($this->EE->TMPL->cache_timestamp != '') ? $this->EE->localize->set_gmt($this->EE->TMPL->cache_timestamp) : $this->EE->localize->now;

			if ($this->EE->TMPL->fetch_param('show_future_entries') != 'yes')
			{
				$sql .= " AND exp_channel_titles.entry_date < ".$timestamp." ";
			}

			if ($this->EE->TMPL->fetch_param('show_expired') != 'yes')
			{
				$sql .= " AND (exp_channel_titles.expiration_date = 0 OR exp_channel_titles.expiration_date > ".$timestamp.") ";
			}

			if ($parent_only === TRUE)
			{
				$sql .= " AND parent_id = 0";
			}

			$sql .= " ORDER BY group_id, parent_id, cat_order";

			$query = $this->EE->db->query($sql);

			if ($query->num_rows() == 0)
			{
				return FALSE;
			}

			// All the magic happens here, baby!!

			foreach($query->result_array() as $row)
			{
				if ($row['parent_id'] != 0)
				{
					$this->find_parent($row['parent_id'], $all);
				}

				$this->cat_full_array[] = $row['cat_id'];
			}

			$this->cat_full_array = array_unique($this->cat_full_array);

			$sql = "SELECT c.cat_id, c.parent_id, c.cat_name, c.cat_url_title, c.cat_image, c.cat_description {$field_sqla}
			FROM exp_categories AS c
			{$field_sqlb}
			WHERE c.cat_id IN (";

			foreach ($this->cat_full_array as $val)
			{
				$sql .= $val.',';
			}

			$sql = substr($sql, 0, -1).')';

			$sql .= " ORDER BY c.group_id, c.parent_id, c.cat_order";

			$query = $this->EE->db->query($sql);

			if ($query->num_rows() == 0)
			{
				return FALSE;
			}
		}
		else
		{
			$sql = "SELECT DISTINCT(c.cat_id), c.parent_id, c.cat_name, c.cat_url_title, c.cat_image, c.cat_description {$field_sqla}
					FROM exp_categories AS c
					{$field_sqlb}
					WHERE c.group_id IN ('".str_replace('|', "','", $this->EE->db->escape_str($group_id))."')
					AND c.group_id NOT IN ('WOOHOO-LINE-763') ";

			if ($parent_only === TRUE)
			{
				$sql .= " AND c.parent_id = 0";
			}

			$sql .= " ORDER BY c.group_id, c.parent_id, c.cat_order";

			$query = $this->EE->db->query($sql);

			if ($query->num_rows() == 0)
			{
				return FALSE;
			}
		}

		// Here we check the show parameter to see if we have any
		// categories we should be ignoring or only a certain group of
		// categories that we should be showing.  By doing this here before
		// all of the nested processing we should keep out all but the
		// request categories while also not having a problem with having a
		// child but not a parent.  As we all know, categories are not asexual

		if ($this->EE->TMPL->fetch_param('show') !== FALSE)
		{
			if (strncmp($this->EE->TMPL->fetch_param('show'), 'not ', 4) == 0)
			{
				$not_these = explode('|', trim(substr($this->EE->TMPL->fetch_param('show'), 3)));
			}
			else
			{
				$these = explode('|', trim($this->EE->TMPL->fetch_param('show')));
			}
		}

		foreach($query->result_array() as $row)
		{
			if (isset($not_these) && in_array($row['cat_id'], $not_these))
			{
				continue;
			}
			elseif(isset($these) && ! in_array($row['cat_id'], $these))
			{
				continue;
			}

			$this->cat_array[$row['cat_id']]  = array($row['parent_id'], $row['cat_name'], $row['cat_image'], $row['cat_description'], $row['cat_url_title']);

			foreach ($row as $key => $val)
			{
				if (strpos($key, 'field') !== FALSE)
				{
					$this->cat_array[$row['cat_id']][$key] = $val;
				}
			}
		}

		$this->temp_array = $this->cat_array;

		$open = 0;

		$this->EE->load->library('typography');
		$this->EE->typography->initialize();
		$this->EE->typography->convert_curly = FALSE;

		$this->category_count = 0;
		$total_results = count($this->cat_array);

		foreach($this->cat_array as $key => $val)
		{
			if (0 == $val[0])
			{
				if ($open == 0)
				{
					$open = 1;

					$this->category_list[] = "<ul>\n";
				}

				$chunk = $template;

				$cat_vars = array('category_name'			=> $val[1],
								  'category_url_title'		=> $val[4],
								  'category_description'	=> $val[3],
								  'category_image'			=> $val[2],
								  'category_id'				=> $key,
								  'parent_id'				=> $val[0]
								);

				// add custom fields for conditionals prep

				foreach ($this->catfields as $v)
				{
					$cat_vars[$v['field_name']] = ( ! isset($val['field_id_'.$v['field_id']])) ? '' : $val['field_id_'.$v['field_id']];
				}

				$cat_vars['count'] = ++$this->category_count;
				$cat_vars['total_results'] = $total_results;

				$chunk = $this->EE->functions->prep_conditionals($chunk, $cat_vars);

				$chunk = str_replace( array(LD.'category_id'.RD,
											LD.'category_name'.RD,
											LD.'category_url_title'.RD,
											LD.'category_image'.RD,
											LD.'category_description'.RD,
											LD.'parent_id'.RD),
									  array($key,
									  		$val[1],
											$val[4],
									  		$val[2],
									  		$val[3],
											$val[0]),
									  $chunk);

				foreach($path as $pkey => $pval)
				{
					if ($this->use_category_names == TRUE)
					{
						$chunk = str_replace($pkey, $this->EE->functions->remove_double_slashes($pval.'/'.$this->reserved_cat_segment.'/'.$val[4]), $chunk);
					}
					else
					{
						$chunk = str_replace($pkey, $this->EE->functions->remove_double_slashes($pval.'/C'.$key), $chunk);
					}
				}

				// parse custom fields
				foreach($this->catfields as $cval)
				{
					if (isset($val['field_id_'.$cval['field_id']]) AND $val['field_id_'.$cval['field_id']] != '')
					{
						$field_content = $this->EE->typography->parse_type($val['field_id_'.$cval['field_id']],
																	array(
																		  'text_format'		=> $val['field_ft_'.$cval['field_id']],
																		  'html_format'		=> $val['field_html_formatting'],
																		  'auto_links'		=> 'n',
																		  'allow_img_url'	=> 'y'
																		)
																);
						$chunk = str_replace(LD.$cval['field_name'].RD, $field_content, $chunk);
					}
					else
					{
						// garbage collection
						$chunk = str_replace(LD.$cval['field_name'].RD, '', $chunk);
					}
				}

				/** --------------------------------
				/**  {count}
				/** --------------------------------*/

				if (strpos($chunk, LD.'count'.RD) !== FALSE)
				{
					$chunk = str_replace(LD.'count'.RD, $this->category_count, $chunk);
				}

				/** --------------------------------
				/**  {total_results}
				/** --------------------------------*/

				if (strpos($chunk, LD.'total_results'.RD) !== FALSE)
				{
					$chunk = str_replace(LD.'total_results'.RD, $total_results, $chunk);
				}

				$this->category_list[] = "\t<li>".$chunk;

				if (is_array($channel_array))
				{
					$fillable_entries = 'n';

					foreach($channel_array as $k => $v)
					{
						$k = substr($k, strpos($k, '_') + 1);

						if ($key == $k)
						{
							if ($fillable_entries == 'n')
							{
								$this->category_list[] = "\n\t\t<ul>\n";
								$fillable_entries = 'y';
							}

							$this->category_list[] = "\t\t\t$v\n";
						}
					}
				}

				if (isset($fillable_entries) && $fillable_entries == 'y')
				{
					$this->category_list[] = "\t\t</ul>\n";
				}

				$this->category_subtree(
											array(
													'parent_id'		=> $key,
													'path'			=> $path,
													'template'		=> $template,
													'channel_array' 	=> $channel_array
												  )
									);
				$t = '';

				if (isset($fillable_entries) && $fillable_entries == 'y')
				{
					$t .= "\t";
				}

				$this->category_list[] = $t."</li>\n";

				unset($this->temp_array[$key]);

				$this->close_ul(0);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	  *  Category Sub-tree
	  */
	function category_subtree($cdata = array())
	{
		$default = array('parent_id', 'path', 'template', 'depth', 'channel_array', 'show_empty');

		foreach ($default as $val)
		{
				$$val = ( ! isset($cdata[$val])) ? '' : $cdata[$val];
		}

		$open = 0;

		if ($depth == '')
				$depth = 1;

		$tab = '';
		for ($i = 0; $i <= $depth; $i++)
			$tab .= "\t";

		$total_results = count($this->cat_array);

		foreach($this->cat_array as $key => $val)
		{
			if ($parent_id == $val[0])
			{
				if ($open == 0)
				{
					$open = 1;
					$this->category_list[] = "\n".$tab."<ul>\n";
				}

				$chunk = $template;

				$cat_vars = array('category_name'			=> $val[1],
								  'category_url_title'		=> $val[4],
								  'category_description'	=> $val[3],
								  'category_image'			=> $val[2],
								  'category_id'				=> $key,
								  'parent_id'				=> $val[0]);

				// add custom fields for conditionals prep
				foreach ($this->catfields as $v)
				{
					$cat_vars[$v['field_name']] = ( ! isset($val['field_id_'.$v['field_id']])) ? '' : $val['field_id_'.$v['field_id']];
				}

				$cat_vars['count'] = ++$this->category_count;
				$cat_vars['total_results'] = $total_results;

				$chunk = $this->EE->functions->prep_conditionals($chunk, $cat_vars);

				$chunk = str_replace( array(LD.'category_id'.RD,
											LD.'category_name'.RD,
											LD.'category_url_title'.RD,
											LD.'category_image'.RD,
											LD.'category_description'.RD,
											LD.'parent_id'.RD),
									  array($key,
									  		$val[1],
											$val[4],
									  		$val[2],
									  		$val[3],
											$val[0]),
									  $chunk);

				foreach($path as $pkey => $pval)
				{
					if ($this->use_category_names == TRUE)
					{
						$chunk = str_replace($pkey, $this->EE->functions->remove_double_slashes($pval.'/'.$this->reserved_cat_segment.'/'.$val[4]), $chunk);
					}
					else
					{
						$chunk = str_replace($pkey, $this->EE->functions->remove_double_slashes($pval.'/C'.$key), $chunk);
					}
				}

				// parse custom fields
				foreach($this->catfields as $ccv)
				{
					if (isset($val['field_id_'.$ccv['field_id']]) AND $val['field_id_'.$ccv['field_id']] != '')
					{
						$field_content = $this->EE->typography->parse_type($val['field_id_'.$ccv['field_id']],
																	array(
																		  'text_format'		=> $val['field_ft_'.$ccv['field_id']],
																		  'html_format'		=> $val['field_html_formatting'],
																		  'auto_links'		=> 'n',
																		  'allow_img_url'	=> 'y'
																		)
																);
						$chunk = str_replace(LD.$ccv['field_name'].RD, $field_content, $chunk);
					}
					else
					{
						// garbage collection
						$chunk = str_replace(LD.$ccv['field_name'].RD, '', $chunk);
					}
				}


				/** --------------------------------
				/**  {count}
				/** --------------------------------*/

				if (strpos($chunk, LD.'count'.RD) !== FALSE)
				{
					$chunk = str_replace(LD.'count'.RD, $this->category_count, $chunk);
				}

				/** --------------------------------
				/**  {total_results}
				/** --------------------------------*/

				if (strpos($chunk, LD.'total_results'.RD) !== FALSE)
				{
					$chunk = str_replace(LD.'total_results'.RD, $total_results, $chunk);
				}

				$this->category_list[] = $tab."\t<li>".$chunk;

				if (is_array($channel_array))
				{
					$fillable_entries = 'n';

					foreach($channel_array as $k => $v)
					{
						$k = substr($k, strpos($k, '_') + 1);

						if ($key == $k)
						{
							if ( ! isset($fillable_entries) OR $fillable_entries == 'n')
							{
								$this->category_list[] = "\n{$tab}\t\t<ul>\n";
								$fillable_entries = 'y';
							}

							$this->category_list[] = "{$tab}\t\t\t$v";
						}
					}
				}

				if (isset($fillable_entries) && $fillable_entries == 'y')
				{
					$this->category_list[] = "{$tab}\t\t</ul>\n";
				}

				$t = '';

				if ($this->category_subtree(
											array(
													'parent_id'		=> $key,
													'path'			=> $path,
													'template'		=> $template,
													'depth' 			=> $depth + 2,
													'channel_array' 	=> $channel_array
												  )
									) != 0 );

			if (isset($fillable_entries) && $fillable_entries == 'y')
			{
				$t .= "$tab\t";
			}

				$this->category_list[] = $t."</li>\n";

				unset($this->temp_array[$key]);

				$this->close_ul($parent_id, $depth + 1);
			}
		}
		return $open;
	}

	// ------------------------------------------------------------------------

	/**
	  *  Close </ul> tags
	  *
	  * This is a helper function to the above
	  */
	function close_ul($parent_id, $depth = 0)
	{
		$count = 0;

		$tab = "";
		for ($i = 0; $i < $depth; $i++)
		{
			$tab .= "\t";
		}

		foreach ($this->temp_array as $val)
		{
		 	if ($parent_id == $val[0])

		 	$count++;
		}

		if ($count == 0)
			$this->category_list[] = $tab."</ul>\n";
	}

	// ------------------------------------------------------------------------


	/**
	  *  Fetch Disable Parameter
	  */
	function _fetch_disable_param()
	{
		$this->enable = array(
							'categories' 		=> TRUE,
							'category_fields'	=> TRUE,
							'custom_fields'		=> TRUE,
							'member_data'		=> TRUE,
							'pagination' 		=> TRUE,
							);

		if ($disable = $this->EE->TMPL->fetch_param('disable'))
		{
			if (strpos($disable, '|') !== FALSE)
			{
				foreach (explode("|", $disable) as $val)
				{
					if (isset($this->enable[$val]))
					{
						$this->enable[$val] = FALSE;
					}
				}
			}
			elseif (isset($this->enable[$disable]))
			{
				$this->enable[$disable] = FALSE;
			}
		}
	}

	// ------------------------------------------------------------------------

	/** --------------------------------
	/**  Locate category parent
	/** --------------------------------*/
	// This little recursive gem will travel up the
	// category tree until it finds the category ID
	// number of any parents.  It's used by the function
	// below
	function find_parent($parent, $all)
	{
		foreach ($all as $cat_id => $parent_id)
		{
			if ($parent == $cat_id)
			{
				$this->cat_full_array[] = $cat_id;

				if ($parent_id != 0)
					$this->find_parent($parent_id, $all);
			}
		}
	}

	// ------------------------------------------------------------------------
		
					
	/** ----------------------------------------
	/**  Plugin Usage
	/** ----------------------------------------*/

	function usage()
	{
	
		ob_start(); 
		?>
	
		This plugin behaves like the standard Category Archive Tag:
		
		http://expressionengine.com/user_guide/modules/channel/category_archive.html
		
		---------------
		
		BUT, you also have some additional parameters to control the output:
		
		group_id: Only categories in these groups are displayed.
		entry_id: Only entries matching these IDs are returned.
		category: Only entries assigned to these categories are returned.
		
		{entry_id} - The entry ID of each entry (in the {entry_titles} section)
		{url_title} - The URL title of each entry (in the {entry_titles} section)
		
		See http://michaelrog.com/ee/category-sorted-entries for detailed documentation.
		
		---------------
		
		This plugin is compatible with NSM Addon Updater:
		
		http://github.com/newism/nsm.addon_updater.ee_addon
	
		<?php
		$buffer = ob_get_contents();
		
		ob_end_clean(); 
	
		return $buffer;
		
	} // END usage()

} // END Category_sorted_entries class

/* End of file pi.category_sorted_entries.php */ 
/* Location: ./system/expressionengine/third_party/category_sorted_entries/pi.category_sorted_entries.php */
<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 	Copyright (c) 2008-2010 Aidann Bowley
=============================================================
	File:					pi.bu_search_pagination.php
-------------------------------------------------------------
	Compatibility (tested):	ExpressionEngine 1.6.4, 2.1.1 
-------------------------------------------------------------
	Purpose:				Additional pagination tags for the Search module
=============================================================
	Version History:
	
	2.1.0 - 17 November 2010 - applied fix to deal with EllisLab's changed search pagination format now bugs settled down. EE 2.1.0 bad, EE 2.1.1 good
	2.0.0 - 17 September 2010 - converted to EE2 format retaining legacy form for templates
	1.2.0 - 17 November 2008 - added parameters to pagination_pages for format and limit. 
		Setting format to block provides access to single variables for breaking paginated pages into blocks of limit (default 5) pages. 
		Single variables then available are prev_block_path, prev_block_pages and next_block_path, next_block_pages, and total_block_pages.
		total_block_pages reflects limit. 
		prev_block_... are not provided if on pages within first block. 
		next_block_... are not provided if on pages within last block
			15/16 Novemeber 2008 - added pair variable pagination_pages with limit param for results and page_number, page_path and count vars available
	1.1.1 - 14 November 2008 - added variables for page_results_first and page_results_last
	1.1.0 - 03 November 2008 - overhauled to work as tag pair with variables rather than using parameter
							 - fixed bug with last_page_path 
	1.0.1 - 18 October 2008	- converted to follow EE dev. guidelines
	1.0.0 - 29 September 2008 - added first and last page options
	0.9.0 - 20 September 2008 - plugin first built				
=============================================================
*/

/**
* Plugin information used by ExpressionEngine
* @global array $plugin_info
*/
$plugin_info = array(
	'pi_name' => 'BU Search pagination',
	'pi_version' => '2.1.0',
	'pi_author' => 'Aidann Bowley',
	'pi_author_url' => 'http://www.bridgingunit.com/labs/expressionengine/bu-search-pagination/',
	'pi_description' => 'Returns alternative pagination tags for the Search module',
	'pi_usage' => Bu_search_pagination::usage()
);

/**
* Additional pagination tags for the Search module
* 
* @package BU Search Pagination
* @version 2.1.0
* @author Aidann Bowley <http://www.bridgingunit.com> 
* @see http://www.bridgingunit.com/labs/expressionengine/bu-search-pagination/
* @copyright Copyright (c) 2008 Aidann Bowley
* @license {@link http://creativecommons.org/licenses/by-sa/2.0/uk/ Attribution-Share Alike 2.0 UK: England & Wales} All source code commenting and attribution must not be removed. This is a condition of the attribution clause of the license.
* @todo add EE standard pagination
* @todo make LG Addon Update compatible ?
*/  

class Bu_search_pagination {
	/**
	* Plugin version
	* @var array
	*/
	var $version = "2.1.0";
	
	/**
	* Returned string
	* @var string
	*/
	var $return_data = "";

	function Bu_search_pagination()
	{
		$this->EE =& get_instance(); 
		
		// Get EE version to deal with version specific adjustments
		$ee_version = explode('.',APP_VER);
		if ($ee_version[0] >= 2 && $ee_version[1] >= 1 && $ee_version[2] >= 1){
			$ee_gte_2_1_1 = true;
		}else{
			$ee_gte_2_1_1 = false;
		}
		
		//beginning bit adapted from total_results(), line 1009+ in mod.search.php EE 1.6
		//global $IN, $DB, $TMPL, $FNS;
		    
		/** ----------------------------------------
		/**  Check search ID number
		/** ----------------------------------------*/
		// 32 is the magic number for the hashed query string set in mod.search.php
		
		// If the QSTR variable is less than 32 characters long we
		// don't have a valid search ID number                
        if (strlen($this->EE->uri->query_string) < 32)
        {
			return '';
        }                           
 		$search_segment = $this->EE->uri->query_string;
 		
       	/** ----------------------------------------
        /**  Fetch ID number and page number
        /** ----------------------------------------*/                 
        $search_count = 0;//default
        
        if (strlen($search_segment) == 32)
        {
        	$search_id = $search_segment;
        }
        else
        {
			if(!$ee_gte_2_1_1)
			{
				// The original way of getting the page position in search results, EE 2.0 etc.
				$search_id = substr($search_segment, 0, 32);
				$search_count  = substr($search_segment, 32);
			}
			else
			{			
				// The new way of getting page position in search results, EE 2.1.1 + 
				if (preg_match("#^P(\d+)|/P(\d+)#", $search_segment, $match))
				{
					$search_count = (isset($match[2])) ? $match[2] : $match[1];
					$search_id = trim_slashes(str_replace($match[0], '', $search_segment));
				}
				else
				{
					$search_id = $search_segment;
				}			
			}
        }
		// Be doubly cautious and tidy up the search ID
		$search_id = trim($search_id); 
		$search_id = preg_replace("#/.+#", "", $search_id);
        
        /** ----------------------------------------
        /**  Fetch the cached search query
        /** ----------------------------------------*/			        
		$query = $this->EE->db->query("SELECT total_results, per_page, result_page 
						FROM exp_search 
						WHERE search_id = '".$this->EE->db->escape_str($search_id)."'");

		//get tag data
		$tagdata = $this->EE->TMPL->tagdata;
		
		// array to store variables to be parsed as conditionals
		$conds = array();

		if ($query->num_rows() == 1)
		{
			$search_total_results = $query->row('total_results');
			$search_limit = $query->row('per_page');
			$search_result_page = $query->row('result_page');
			
			// Adjust Page prefix for EE < 2.1.1
			if(!$ee_gte_2_1_1)
			{
				$page_prefix = '';
			}
			else
			{				
				$page_prefix = '/P';				
			}

			
			//$search_segment='{segment_3}';
			//$search_total_results='{exp:search:total_results}';
			//$search_limit=3;	//from original search set up - num results to return per page		

			//----------------------
			//Do the calculations
			//----------------------
			//current page number
			$current_page = $this->return_data = (($search_count+$search_limit) / $search_limit);
			//total page number
			$total_pages = $this->return_data = ceil($search_total_results / $search_limit);//round up to nearest whole number
			
			//find out the previous page
			if($search_count > 0)
			{//there are pages before this one
				$prev_count = $search_count - $search_limit;
				$prev_search_segment = $search_id.$page_prefix.$prev_count;//x
				$prev_page_path = $search_result_page.'/'.$prev_search_segment;
			}
			else
			{//we're on the first page
				$prev_page_path = '';
			}
			
			//find out the next page
			if(($search_count + $search_limit) < $search_total_results)
			{//there are pages after this one
				$next_count = $search_count + $search_limit;
				$next_search_segment = $search_id.$page_prefix.$next_count;//x
				$next_page_path = $search_result_page.'/'.$next_search_segment;					
			}
			else
			{//we're on the last page
				$next_page_path = '';
			}
			
			//first page
			$first_search_segment = $search_id.$page_prefix.'0';//x
			$first_page_path = $search_result_page.'/'.$first_search_segment;

			//last page
			$last_count = (ceil($search_total_results / $search_limit)-1) * $search_limit;
			$last_search_segment = $search_id.$page_prefix.$last_count;//x
			$last_page_path = $search_result_page.'/'.$last_search_segment;


			//ref to result of first on this page
			$page_results_first = $search_count+1;		
			
			//ref to result of last on this page	
			if(($search_count + $search_limit) >= $search_total_results)
			{//we're on the last page
				$page_results_last = $search_total_results;
			}
			else
			{
				$page_results_last = $current_page * $search_limit ;	
			}
						
			//----------------------
			//Deal with our new variables
			//----------------------	
			//check for param used in legacy code		
			$parameter = $this->EE->TMPL->fetch_param('type');
			
			//provide nice clean variables & conditionals
			if($parameter==NULL){
				
				//prepare conditionals first otherwise won't be able to test for them without the variable already havng being declared
				// Set defaults
				$conds['current_page'] = $current_page;
				$conds['total_pages'] = $total_pages;
				$conds['prev_page_path'] = $prev_page_path;
				$conds['next_page_path'] = $next_page_path;
				$conds['first_page_path'] = $first_page_path;
				$conds['last_page_path'] = $last_page_path;
				$conds['page_results_first'] = $page_results_first;
				$conds['page_results_last'] = $page_results_last;
			
				// Prep the output using EE's conditional voodoo
				$tagdata = $this->EE->functions->prep_conditionals($tagdata, $conds);  
				
				foreach ($this->EE->TMPL->var_single as $key => $val)
				{
					//if we've a particular variable
				    //if ($key == "test")
				    switch ($key)
				    {
						case "current_page":
							$tagdata = $this->EE->TMPL->swap_var_single($val, $conds['current_page'], $tagdata);
						break;			        
						case "total_pages":
							$tagdata = $this->EE->TMPL->swap_var_single($val, $conds['total_pages'], $tagdata);
						break;			        
						case "prev_page_path" :
							$tagdata = $this->EE->TMPL->swap_var_single($val, $conds['prev_page_path'], $tagdata);	        
						break;			        
						case "next_page_path":
							$tagdata = $this->EE->TMPL->swap_var_single($val, $conds['next_page_path'], $tagdata);	        
						break;
						case "first_page_path":
							$tagdata = $this->EE->TMPL->swap_var_single($val, $conds['first_page_path'], $tagdata);
						break;
						case "last_page_path":
							$tagdata = $this->EE->TMPL->swap_var_single($val, $conds['last_page_path'], $tagdata);
						break;
						case "page_results_first":
							$tagdata = $this->EE->TMPL->swap_var_single($val, $conds['page_results_first'], $tagdata);
						break;
						case "page_results_last":
							$tagdata = $this->EE->TMPL->swap_var_single($val, $conds['page_results_last'], $tagdata);
						break;
				    }				
				    
				}		
//deal with variable pairs: key = pair, val = array of params
foreach ($this->EE->TMPL->var_pair as $key => $val)
{
	//locate our variable pair
    if (preg_match("/^pagination_pages/", $key))
	{    	
		//extract its contents - working on the same copy of $this->EE->TMPL->tagdata that we have for earlier single vars 
		//else won't be able to use those earlier singles as conditionals within the pair
		//need to use $key in opening part when dealing with parameters in variable pair
		preg_match("/".LD."$key".RD."(.*?)".LD.'\/'.'pagination_pages'.RD."/s", $tagdata, $matches_pair);
		
		$pair_ref = $matches_pair[0];
		$pair_data = $matches_pair[1];
		
		//check if there's a format chosen for pages display
		$pagination_format = (is_array($val) && isset($val['format'])) ? $val['format'] : 'none';
		
		//make arrays of the results we want to produce	    	
    	$arr_numbers = array();
    	$arr_paths = array();
    	
    	//set defaults for extra single vars
    	$prev_block_path = '';
    	$prev_block_pages = '';
    	$next_block_path = '';
    	$next_block_pages = '';
    	$total_block_pages = '';
    	
		//allowed variants for format are 'block', 'ee', 'none'. 
		//COME BACK TO ADD 'ee' // AND $pagination_format !== 'ee')
		//'none' is default
		if($pagination_format !== 'block')
		{
			$pagination_format = 'none';
			
			//show links for ALL pages        	
        	//populate result arrays
	    	for ($i = 0; $i < $total_pages; $i++)
	    	{    		
	    		$arr_numbers[] = $i+1;//not zero-based
	    		$page_path_ref = $search_limit * $i;
	    		$arr_paths[] = $search_result_page.'/'.$search_id.$page_prefix.$page_path_ref;//x
	    	}
		}
		else
		{
			//check if there's a limit on the pages to display by checking variable pair's params
			//limit only comes into play if format is 'block' or 'ee'
			//default for limit is 5, as per standard EE
			$page_limit = (is_array($val) && isset($val['limit'])) ? $val['limit'] : 5;
			
			//ADD LATER
			if($pagination_format === 'ee')
			{//if limit is even, half limit prior to current, add remainder post
				
			}//end ee

			if($pagination_format === 'block')
			{//limit determines number of links in block
				$total_block_pages = $page_limit;
				$which_block = ceil($current_page / $page_limit);
				$last_block = floor($total_pages / $page_limit);
				$block_start = ($which_block - 1) * $page_limit;
				$block_end = $block_start + $page_limit;
				if($block_end > $total_pages)
				{//don't want to show pages that aren't available
					$block_end = $total_pages;
					$total_block_pages = ($total_pages % $page_limit);
				}
				
	        	//populate result arrays
		    	for ($i = $block_start; $i < $block_end; $i++)
		    	{    		
		    		$arr_numbers[] = $i+1;//not zero-based
		    		$page_path_ref = $search_limit * $i;
		    		$arr_paths[] = $search_result_page.'/'.$search_id.$page_prefix.$page_path_ref;//x
		    	}
		    	
		    	//add single variables for prev and next block
		    	//prev block
		    	if($which_block > 1)
		    	{
		    		$prev_block_start = (($which_block - 1) * $page_limit) - $page_limit;
		    		$prev_block_end = $prev_block_start + $page_limit;
		    		$prev_block_path = $search_result_page.'/'.$search_id.$page_prefix.($prev_block_start * $search_limit);//x
		    		$prev_block_pages = ($prev_block_start + 1).' - '.($prev_block_end);//not zero-based   
		    	}//end prev block
		    	
		    	//next block
		    	//$last_block_pages = ($total_pages % $page_limit);
		    	if($which_block <= $last_block)
		    	{
		    		$next_block_start = $which_block * $page_limit;
		    		$next_block_end = $next_block_start + $page_limit;
		    		if($next_block_end > $total_pages)
		    		{
		    			$next_block_end = $next_block_start + ($total_pages % $page_limit);
		    		}
		    		$next_block_path = $search_result_page.'/'.$search_id.$page_prefix.($next_block_start * $search_limit);//x
		    		$next_block_pages = ($next_block_start + 1).' - '.($next_block_end);//not zero-based
		    	}//end next block
				
			}//end block						
			
		}//end preparation of result arrays 
        	
    	//loop through our resultset
    	$temp = '';
    	$return_pair = '';//$tagdata;
    	$arr_count = count($arr_numbers);
		for ($i = 0; $i < $arr_count; $i++) 
		{	
			$temp = $pair_data;
	    	//preprepare them for use as conditionals
	    	$conds['page_number'] = $arr_numbers[$i];
			$conds['page_path'] = $arr_paths[$i];
			$conds['count'] = $i+1;//not zero-based
			
			// Prep the output using EE's conditional voodoo
			$temp = $this->EE->functions->prep_conditionals($temp, $conds);  

			//deal with single variables available within our variable pair (for each of our resultset)
			foreach ($this->EE->TMPL->var_single as $key => $val)
		    {
		        switch ($val)
		        {
		            case "page_number":
		                $temp = $this->EE->TMPL->swap_var_single($val, $conds['page_number'] , $temp);
		                break;
		            case "page_path":
		                $temp = $this->EE->TMPL->swap_var_single($val, $conds['page_path'], $temp);
		                break;
		            case "count":
		                $temp = $this->EE->TMPL->swap_var_single($val, $conds['count'], $temp);
		                break;
		        }
		    }
		    
		    $return_pair .= $temp;
	    
		}//end resultset loop
		
		//----------------------
		//deal with any additional once-only single vars
		//----------------------
		//prep conditionals
		$conds['total_block_pages'] = $total_block_pages;
    	$conds['prev_block_path'] = $prev_block_path;
		$conds['prev_block_pages'] = $prev_block_pages;
		$conds['next_block_path'] = $next_block_path;
		$conds['next_block_pages'] = $next_block_pages;
		
		//get ref to tagdata that's in use
		$singles_once = $return_pair;
		
		// Prep the output using EE's conditional voodoo
		$singles_once = $this->EE->functions->prep_conditionals($singles_once, $conds);

		//make singles available
		foreach ($this->EE->TMPL->var_single as $key => $val)
	    {
	        switch ($val)
	        {
	            case "total_block_pages":
	                $singles_once = $this->EE->TMPL->swap_var_single($val, $conds['total_block_pages'] , $singles_once);
	                break;
	            case "prev_block_path":
	                $singles_once = $this->EE->TMPL->swap_var_single($val, $conds['prev_block_path'] , $singles_once);
	                break;
	            case "prev_block_pages":
	                $singles_once = $this->EE->TMPL->swap_var_single($val, $conds['prev_block_pages'], $singles_once);
	                break;
	            case "next_block_path":
	                $singles_once = $this->EE->TMPL->swap_var_single($val, $conds['next_block_path'] , $singles_once);
	                break;
	            case "next_block_pages":
	                $singles_once = $this->EE->TMPL->swap_var_single($val, $conds['next_block_pages'], $singles_once);
	                break;
	        }
	    }
	    $return_pair = $singles_once;
	    //------------------------

		$tagdata = str_replace($pair_ref,$return_pair,$tagdata);
    		
    	// Replace var-pair with final value
        //$tagdata = $this->EE->TMPL->swap_var_pairs($key, 'pagination_pages', $return_pair);
        
    }//end if
}//end variable pair
						
				// Spit out collated tagdata
				$this->return_data = $tagdata;			
				
			}else{//allow for legacy code
				
				if($search_total_results > $search_limit)
				{//we've got more than one page
					
				    switch ($parameter)
				    {
						case "current_page":
							$this->return_data = $current_page;
						break;
						
						case "total_pages":
							$this->return_data = $total_pages;
						break;
						
						case "prev_page_path" :						
							$this->return_data = $prev_page_path;								        
						break;
						
						case "next_page_path":
							$this->return_data = $next_page_path;		        
						break;
						case "first_page_path":
							$this->return_data = $first_page_path;
						break;
						case "last_page_path":
							$this->return_data = $last_page_path;
						break;
						case "page_results_first":
							$this->return_data = $page_results_first;
						break;
						case "page_results_last":
							$this->return_data = $page_results_last;
						break;
				    }	
				
				}
				else
				{
					//single page: not enough results to warrant pagination, but might want page references anyway
				    switch ($parameter)
				    {
					case "current_page":
						$this->return_data = $current_page;
					break;			        
					case "total_pages":
						$this->return_data = $total_pages;
					break;			        
					case "prev_page_path" :
						$this->return_data = '';	        
					break;			        
					case "next_page_path":
						$this->return_data = '';	        
					break;
					case "first_page_path":
						$this->return_data = '';
					break;
					case "last_page_path":
						$this->return_data = '';
					break;
					case "page_results_first":
						$this->return_data = $page_results_first;
					break;
					case "page_results_last":
						$this->return_data = $page_results_last;
					break;
				    }
				}					
				
			}//end legacy code

		}
		else
		{//no results
			return '';
		}
 
		
	}//end BU_search_pagination
	  
	
	
	/**
	* Plugin usage documentation
	*
	* @return	string Plugin usage instructions
	*/
	function usage()
	{
		ob_start(); 
?>
The BU_search_pagination plugin simply provides various pagination tags that are not available in the Search Module.

Add this into your template when you wish to have greater control over customising the pagination tags on your search results page.

The plugin bases the output of its pagination on the search results (per page limit) and result_page parameters set in your search form tag and the total number of results calculated by the Search Module.

You can use this plugin to access pagination information in 1 of 2 ways: as a standard tag pair with a set of variables (recommended) or via parameters - this latter way is included because it was how the plugin started - so for backwards compatibility and out of curiosity. Note: the pagination_pages pair variable and the variables it contains are NOT available via tag parameters

The tag pair is as follows:
{exp:bu_search_pagination}

{/exp:bu_search_pagination}

Variables available within it are
{current_page}
{total_pages}
{prev_page_path}
{next_page_path}
{first_page_path}
{last_page_path}
{page_results_first}
{page_results_last}

They are largely self-explanatory, but the last two provide reference to the results available on the current page, e.g. on the 2nd page where you have 5 results per page, page_results_first will be 6, page_results_last will be 10, so you can display 'results 6 - 10'.

There is a pair variable called pagination_pages that by default outputs additional data for all search result pages.
{pagination_pages}

{/pagination_pages}

This has 2 parameters available to it - format and limit

pagination_pages has the following single variables available within it
{count}
{page_number}
{page_path}

count tells you what position you are on in the pagination_pages loop.
page_number tells you the page number for the current result page processed.
page_path provides a link path for the current result page processed.

The format parameter allows you to change the pagination so that it is split up into blocks of pages. By default each block contains up to 5 sets of result pages. The limit parameter allows you to change that.

For example, if you had 19 pages of results and a 'limit' of 4, pages 1-4 would be the first block, 5-8 the second and so on. The final block would have 3 pages.

If you've specified a format parameter of 'block' then the following single variables also become available within pagination_pages:
{total_block_pages}
{prev_block_path}
{prev_block_pages}
{next_block_path}
{next_block_pages}

total_block_pages just reflects the number of blocks that the results have been split into. If you don't specify a 'limit' this will be 5 as that's the default limit.

prev_block_path provides the link back to the first page within the previous block.
prev_block_pages provides a reference for the pages contained within the previous block. This follows the format 1 - 4, 5 - 8 etc.
Neither of these two variables will be available on pages within the first block.

next_block_path provides the link to the first page within the next block.
next_block_pages provides a reference for the pages contained within the next block. This follows the format 4 - 8, 9 - 12 etc.
Neither of these two variables will be available on pages within the last block.

Here is an example of usage without using the pagination_pages pair variable:

{exp:bu_search_pagination}
{if prev_page_path}
 <a href="{path={prev_page_path}}">Prev</a>
{/if}

<p>Page {current_page} of {total_pages} </p>

{if next_page_path}
 <a href="{path={next_page_path}}">Next</a>
{/if}
{/exp:bu_search_pagination}

Here is an example using the pagination_pages pair variable:

{exp:bu_search_pagination}
{pagination_pages format='block'}
{if prev_block_path!='' AND count==1}
<a href='{path={prev_block_path}}'>{prev_block_pages}</a>
{/if}

{if page_number==current_page}
{page_number}
{if:else} 
<a href='{path={page_path}}'>{page_number}</a> 
{/if}

{if next_block_path!='' AND count==total_block_pages}
<a href='{path={next_block_path}}'>{next_block_pages}</a>
{/if}
{/pagination_pages}
{/exp:bu_search_pagination}

There is one permitted parameter for the bu_search_pagination tag - 'type'. Note: if you use the type parameter, the standard variables usually available in the tag pair will not be output.

The variants available for the type parameter basically follow the same naming convention used for variables within the tag pair:
{exp:bu_search_pagination type='current_page'}
{exp:bu_search_pagination type='total_pages'}
{exp:bu_search_pagination type='prev_page_path'}
{exp:bu_search_pagination type='next_page_path'}
{exp:bu_search_pagination type='first_page_path'}
{exp:bu_search_pagination type='last_page_path'}
{exp:bu_search_pagination type='page_results_first'}
{exp:bu_search_pagination type='page_results_last'}

The prev_page_path, next_page_path, first_page_path and last_page_path type parameters are only output when they are actually available, i.e. not when there is just a single page of results.

Here is an example of usage employing the type parameter:

{if "{exp:bu_search_pagination type='previous_page_path'}" !=''}
	<a href="{path={exp:bu_search_pagination type='prev_page_path'}}">Prev</a>
{/if}
	
	<p>Page {exp:bu_search_pagination type='current_page'} of {exp:bu_search_pagination type='total_pages'} </p>

{if "{exp:bu_search_pagination type='next_page_path'}" !=''}
	<a href="{path={exp:bu_search_pagination type='next_page_path'}}">Next</a>
{/if}

<?php
		$buffer = ob_get_contents();
	
		ob_end_clean(); 
	
		return $buffer;
	}//end usage	

}// END

/* End of file pi.bu_search_pagination.php */ 
/* Location: ./system/expressionengine/third_party/bu_search_pagination/pi.bu_search_pagination.php */
?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
                        'pi_name'            => 'HTML Stripper UNOFFICIAL EE 2.X CONVERSION',
                        'pi_version'        => '1.0.1',
                        'pi_author'            => 'Paul Burdick',
                        'pi_author_url'        => 'http://expressionengine.com/',
                        'pi_description'    => 'Removes HTML code from text',
                        'pi_usage'            => HTML_strip::usage()
                    );

/**
 * HTML_strip Class
 *
 * @package            ExpressionEngine
 * @category        Plugin
 * @author            UNOFFICIAL EE2.X CONVERSION 
 * @copyright        Copyright (c) 2004 - 2009, EllisLab, Inc.
 * @link                http://expressionengine.com/downloads/details/html_strip/
 */
 
class HTML_strip {

    var $return_data;

    
    // ----------------------------------------
    //  HTML Stripper
    // ----------------------------------------

    function HTML_strip($str = '')
    {

        $this->EE =& get_instance();
        $convert        = ( ! $this->EE->TMPL->fetch_param('convert'))        ? 'n'        :  $this->EE->TMPL->fetch_param('convert');
        $convert_back    = ( ! $this->EE->TMPL->fetch_param('convert_back'))    ? 'original':  $this->EE->TMPL->fetch_param('convert_back');
        $keep            = ( ! $this->EE->TMPL->fetch_param('keep'))            ? ''        :  $this->EE->TMPL->fetch_param('keep');
        $template        = ($str == '') ? $this->EE->TMPL->tagdata : $str;
        
        $template        = str_replace('&#47;', '/',$template);
		
		// -------------------------------
		//  Clean up Keep values
		// -------------------------------
		
		if ($keep != '')
		{
			// Remove white space and replace with comma
        	$keep = preg_replace("/\s*(\S+)\s*/", "\\1,", $keep);
        	
        	// Remove any existing doubles
        	$keep = str_replace(",,", ",", $keep);
        	
        	// Remove any comma at the end
        	if (substr($keep, -1) == ",")
			{
				$keep = substr($keep, 0, -1);
			}
		
			// Break into an array via commas and remove duplicates
			$tags = preg_split('/[,]/', $keep);
			$tags = array_unique($tags);
			
			$approved_tags = array();
			foreach ($tags as $tag)
			{
			     $approved_tags[] = '<'.$tag.'>';
			}
			
			$keep = implode(',',$approved_tags);
		}
		
		// -------------------------------
		//  Convert and Clean - $5! No waiting!
		// -------------------------------
		
		if ($convert == 'y')
		{
			if ($convert_back == 'all' || $convert_back == 'none')
			{
				$template = str_replace('&lt;','<',$template);
				$template = str_replace('&gt;','>',$template);
				
				// Strip out tags
				$template = strip_tags($template,$keep);
				
				if ($convert_back == 'all')
				{
					$template = str_replace('<', '&lt;', $template);
					$template = str_replace('>', '&gt;', $template);					
				}
			}
			else
			{
				// First clean out any existing tags
				$template = strip_tags($template,$keep);
				
				// Now, convert...
				// Replace current brackets with 'modified' entities
				$template = str_replace('<', '&lt2;', $template);
				$template = str_replace('>', '&gt2;', $template);
				
				// Replace bracket entities with brackets
				$template = str_replace('&lt;','<',$template);
				$template = str_replace('&gt;','>',$template);
				
				// Clean out any entitified tags
				$template = strip_tags($template,$keep);
				
				// Convert everything back to what they should be
				$template = str_replace('<', '&lt;', $template);
				$template = str_replace('>', '&gt;', $template);
				$template = str_replace('&lt2;', '<', $template);
				$template = str_replace('&gt2;', '>', $template);				
			}			
    	}
    	else
    	{
    		$template = strip_tags($template,$keep);    	
    	}
    	
    	//$template		= str_replace('/', '&#47;', $template);
    	
 		$this->return_data = $template;
    }
    // END
    
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

function usage()
{
ob_start(); 
?>
Strip out all HTML from a piece of text

{exp:html_strip}

text <strong>you</strong> want processed

{/exp:html_strip}

// Returns 'text you want processed'

----------------------------------------------

The "convert" parameter lets you specify whether to convert &lt; and &gt; back to the < and  > characters before stripping out the HTML.

The "convert_back" parameter lets you specify whether to convert < and > back to the entities.
   - convert_back="all" will convert all < and > characters to entities.
   - convert_back="original" will convert the < and >'s that were originally &lt; and &gt; back to entities
   - convert_back="none" will convert none of the < and >'s back to entities

The "keep" parameter lets you specify what HTML tags to keep.

{exp:html_strip convert="y" convert_back="all" keep="strong,em"}

&lt;p&gt;text &lt;strong&gt;you&lt;/strong&gt; want processed&lt;/p&gt;

{/exp:html_strip}

// Returns 'text &lt;strong&gt;you&lt;/strong&gt; want processed'


<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
// END


}
// END CLASS
?>
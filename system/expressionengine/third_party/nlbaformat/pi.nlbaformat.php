<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
                        'pi_name'           => 'NlbaFormat',
                        'pi_version'        => '1',
                        'pi_author'         => 'Vinay m',
                        'pi_author_url'     => 'http://www.artminister.com/',
                        'pi_description'    => 'Parses ee File directory tag',
                        'pi_usage'          => NlbaFormat::usage()
                    );

/**
 * Xhtml Class
 *
 * @package         ExpressionEngine
 * @category        Plugin
 * @author          ExpressionEngine Dev Team
 * @copyright       Copyright (c) 2005 - 2009, EllisLab, Inc.
 * @link            http://expressionengine.com/downloads/details/xhtml_typography/
 */
class NlbaFormat {

    var $return_data;
    
    

    /**
    * Full xhtml
    *
    * @access   public
    * @param    string
    * @return   string
    */
    function parsefile($str = '')
    {
        $EE =& get_instance();
        
        $str = ($str == '') ? $EE->TMPL->tagdata : $str;
                
        $EE->load->library('typography');
        return $EE->typography->parse_file_paths($str);                       
    }

		function toupper($str=''){
			$EE =& get_instance();
			$str = ($str == '') ? $EE->TMPL->tagdata : $str;
			return strtoupper($str);			
		}
		
		function light($str = '')
    {
        $EE =& get_instance();
                
        if ($str == '')
        {
            $str = $EE->TMPL->tagdata;
        }
      
        $EE->load->library('typography');
        return $EE->typography->light_xhtml_typography($str);                       
    }

		function full($str = '')
    {
        $EE =& get_instance();
        
        $str = ($str == '') ? $EE->TMPL->tagdata : $str;
                
        $EE->load->library('typography');
        return $EE->typography->xhtml_typography($str);                       
    }

		function getcaturl(){
			$this->EE=& get_instance();

			$category_group_id = intval($this->_get_param('category_group_id'));
			$entry_id = intval($this->_get_param('entry_id'));
			$urlt='';
			$catexclude = array(22,23,24,26,27,28);

			$query = $this->EE->db->query("SELECT
			    c.cat_id,
			    c.cat_name,
			    c.cat_url_title,
					c.parent_id
			FROM
			    exp_channel_titles AS t NATURAL JOIN
			    exp_category_posts AS p NATURAL JOIN
			    exp_categories AS c
			WHERE
			    c.group_id=2 AND
			    t.entry_id = $entry_id ORDER BY c.cat_id ASC");

			if ($query->num_rows() > 0)
			{
				foreach($query->result_array() as $row)
				{
					if(!in_array($row['cat_id'],$catexclude)){
						$urlt.=$row['cat_url_title'].'/';
					}					
				}
				$this->return_data= $urlt;
			}


			return $this->return_data;		
		}
      
    // --------------------------------------------------------------------
    
    /**
    * Usage
    *
    * Plugin Usage
    *
    * @access   public
    * @return   string
    */
    function usage()
    {
        ob_start(); 
        ?>
        Parse {filedir_2} => Server path

        {exp:parsefiledir:parse}

        text you want processed

        {/exp:parsefiledir:light}


        <?php
        $buffer = ob_get_contents();
    
        ob_end_clean(); 

        return $buffer;
    }
		
		
		/**
	     * Helper function for getting a parameter
		 */		 
		function _get_param($key, $default_value = '')
		{
			$val = $this->EE->TMPL->fetch_param($key);

			if($val == '') {
				return $default_value;
			}
			return $val;
		}
		
    // --------------------------------------------------------------------

}
// END CLASS

/* End of file pi.xhtml.php */
/* Location: ./system/expressionengine/third_party/xhtml/pi.xhtml.php */


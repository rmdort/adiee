<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
                        'pi_name'           => 'Am Parse File Directory',
                        'pi_version'        => '1',
                        'pi_author'         => 'Vinay m',
                        'pi_author_url'     => 'http://github.com/rmdort/AmParseFileDir',
                        'pi_description'    => 'Parses ee File directory tags',
                        'pi_usage'          => Amparsefiledir::usage()
                    );

class Amparsefiledir {

    var $return_data;

    /**
    * Full xhtml
    *
    * @access   public
    * @param    string
    * @return   string
    */
    public function Amparsefiledir($str = '')
    {
        $EE =& get_instance();
        
        $str = ($str == '') ? $EE->TMPL->tagdata : $str;
                
        $EE->load->library('typography');
        $this->return_data = $EE->typography->parse_file_paths($str);                       
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

        {exp:amparsefiledir}

        {filedir_2}images/image.png

        {/exp:amparsefiledir}

				Will return /uploads/images/image.png


        <?php
        $buffer = ob_get_contents();
    
        ob_end_clean(); 

        return $buffer;
    }
		
				
    // --------------------------------------------------------------------

}
// END CLASS

/* End of file pi.xhtml.php */
/* Location: ./system/expressionengine/third_party/amparsefiledir/pi.amparsefiledir.php */


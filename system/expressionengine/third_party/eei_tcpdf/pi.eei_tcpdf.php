<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

  /**
  * Copyright (c) 2010 Massimiliano Lombardi
  * Plugin licence: http://creativecommons.org/licenses/by-nd/3.0/
  * 
  * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  * THE SOFTWARE. 
  */
  
  $plugin_info = array(
    'pi_name'           => 'EEI_Tcpdf',
    'pi_version'        => '0.0.4',
    'pi_author'         => 'Massimiliano Lombardi &mdash; <a href="mailto:eei.tcpdf@age38dev.com">eei.tcpdf@age38dev.com</a>',
    'pi_author_url'     => 'http://bitbucket.org/age38dev/eei_tcpdf',
    'pi_description'    => 'Create PDF pages using TCPDF library (http://www.tcpdf.org) using simple template and HTML markup',
    'pi_usage'          => EEI_Tcpdf::usage(),
    'Fonts available'   => EEI_Tcpdf::usageFontsList(),
    'System'            => EEI_Tcpdf::usageWarning(),
    'Plugin licence'    => '<a href="http://creativecommons.org/licenses/by-nd/3.0/">Attribution-NoDerivs 3.0 Unported</a>',
  );
  
  /**
   * EEI_Tcpdf Class
   *
   * @package      ExpressionEngine
   * @category     Plugin
   * @author       Massimiliano Lombardi <eei.tcpdf@age38dev.com>
   * @copyright    Copyright (c) 2010, Massimiliano Lombardi
   * @licence      http://creativecommons.org/licenses/by-nd/3.0/
   * @link         http://bitbucket.org/age38dev/eei_tcpdf
   */
  class EEI_Tcpdf 
  {
    
    // --------------------------------------------------------------------
    
    # the full physical path of the tcpdf lib if different from
    # inside this folder
    #
    # set your full tcpdf lib path in expressionengine/config/config.php, eg:
    #
    # $config['eei_tcpdf_lib_path'] = '/var/www/tcpdf' (*nix like) or 
    # $config['eei_tcpdf_lib_path'] = 'C:\www\tcpdf' (windows)
    # NOTE: make this browseable also ... means: AS apache alias or AS IIS virtual folder
    # example: http://yourdomain/tcpdf
    #
    private $tcpdf = NULL;
    # EE instance
    private static $ee = null;
    # set flag for PDF page
    private static $pdf = FALSE;
    # set dinamic config
    private static $var = array();                            
    
    // --------------------------------------------------------------------
    
    # constructor, set the tcpdf lib path from config file
    public function __construct()
    {
      // set ref to EE
      self::$ee =& get_instance();
      
      // init setting for tcpdf lib path
      if(is_null($this->tcpdf))
      {
        if(trim(self::$ee->config->item('eei_tcpdf_lib_path')) != '' && file_exists(self::$ee->config->item('eei_tcpdf_lib_path')))
        {
          $this->tcpdf = self::$ee->config->item('eei_tcpdf_lib_path');
          log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Setting external TCPDF lib path');  
        }
        elseif(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'tcpdf'))
        {
          $this->tcpdf = dirname(__FILE__).DIRECTORY_SEPARATOR.'tcpdf';
          log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Setting TCPDF lib path as in EEI_Tcpdf plugin folder'); 
        }
        else
        {
          log_message('error', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} EEI_Tcpdf lib does not where TCPDF lib. reside ...'); 
        }  
      }  
    } // end funct
    
    // --------------------------------------------------------------------
    
    # set params vars in template context (see usage) 
    public function params()
    {
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Request for parameters setting tag');
      
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Setting EE instance and first_and_only calling');
      $stop = $this->initialize();
      
      // if its not the first call I stop here to prevent to overwrite the first tag params 
      // (this should happen if you put two tags {exp:ee_tcpdf:params} in logic block)
      if($stop == TRUE)
      {
        log_message('error', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} This not seems the first call, I do not change my first setting');
        return ;  
      }
      
      $p = array(
        array('author' => self::$ee->config->item('webmaster_name')), 
        array('title' => self::$ee->config->item('site_name')),
        array('subject' => 'PDF file'), 
        array('keywords' => 'PDF file'), 
        array('orientation' => 'P'), 
        array('unit' => 'mm'), 
        array('format' => 'A4'), 
        array('language' => 'eng'),
        array('font-family' => 'times'), 
        array('font-size' => 12),
        array('logo' => 'tcpdf_logo.jpg'),
        array('dfolder' => FALSE),
        array('save' => 'no'),
        array('cache' => FALSE), 
      );
      
      // check for vars passed on runtime
      foreach($p as $settings)
      {
        $var_name     = key($settings);
        $var_default  = current($settings);
        $var_template = self::$ee->TMPL->fetch_param($var_name);
        
        // set vars from template or the default value
        self::$var[$var_name] = (trim($var_template) != '') ? trim($var_template) : trim($var_default);
        log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Setting "'.$var_name.'" with value: '.self::$var[$var_name]);
      }
      
      // adjust saving feature
      if(!in_array(strtolower(self::$var['save']), array('yes', 'no')))
      {
        self::$var['save'] = 'no';
      }
      self::$var['save'] = (strtolower(self::$var['save']) == 'yes') ? 'D' : 'I'; // D = download; I = output managed by plugin browser client
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Saving feature (means: tcpdf destination) set to: '.self::$var['save']);
      
      // adjust font family
      self::$var['font-family'] = $this->adjustFontFamily(self::$var['font-family']);
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Font family adjust to: '.self::$var['font-family']);
      
      // adjust font size
      self::$var['font-size'] = (!is_integer(self::$var['font-size'] * 1)) ? 12 : self::$var['font-size'];
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Font size adjust to: '.self::$var['font-size']);
      
      // @TODO: check all other params set by tag
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} @TODO: make more checks for tag params');
      
      // trigger to "inform" hook system to output as pdf
      self::$pdf = TRUE;
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} "Comunicate" to class hook (output) to make pdf response'); 
         
    } // end funct
    
    // --------------------------------------------------------------------
    
    # used by hook to load depency files <--- ATTENTION!!! DO NOT USE THIS AS PLUGIN TEMPLATE
    public function output()
    {
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Hook class starting ...');
      
      $EE  =& get_instance(); // leave like this (do not use static)
      if(self::$pdf == TRUE)
      {
        log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Fine, we need to make a pdf');
        
        // set the full path of tcpdf lib installation
        $tcpdf_path = ($this->tcpdf != FALSE) ? $this->tcpdf.DIRECTORY_SEPARATOR : dirname(__FILE__).DIRECTORY_SEPARATOR.'tcpdf'.DIRECTORY_SEPARATOR;
        log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Setting tcpdf path to: '.$tcpdf_path);
        
        // check if tcpdf lib exist and if not return now
        if(!file_exists($tcpdf_path.'tcpdf.php'))
        {
          log_message('error', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} TCPDF lib seems do not exist, I have look on: '.$tcpdf_path);
          return $EE->output->_display($EE->output->final_output);  
        }
        
        define('K_TCPDF_EXTERNAL_CONFIG', TRUE);
        define('K_PATH_MAIN', $tcpdf_path);
        
        // check if we load logo from standard image folder or specific ones
        if(trim(self::$var['dfolder']) != '') // specific ones
        {                                      
          define('K_BLANK_IMAGE', K_PATH_MAIN.'images/_blank.png');
          define('K_PATH_IMAGES', FCPATH.self::$var['dfolder'].'/');
          log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Setting a runtime different folder for logo header ('.K_PATH_IMAGES.')');  
        } 
        else // standard
        {
          define('K_PATH_IMAGES', K_PATH_MAIN.'images/');
          define('K_BLANK_IMAGE', K_PATH_IMAGES.'_blank.png'); 
        }
        
        // define my HTTP_HOST
        $FQDN = trim(self::$ee->config->item('base_url') == '') ? 'http://'.$GLOBALS['_SERVER']['HTTP_HOST'] : self::$ee->config->item('base_url');
        
        define('K_PATH_URL',  rtrim($FQDN, '/').'/tcpdf/');
        define('K_PATH_FONTS', K_PATH_MAIN.'fonts/');
        define('K_PATH_CACHE', K_PATH_MAIN.'cache/'); 
        define('PDF_HEADER_LOGO', self::$var['logo']);
        define('PDF_PAGE_FORMAT', self::$var['format']);
        define('PDF_PAGE_ORIENTATION', self::$var['orientation']);
        define('PDF_CREATOR', self::$var['author']);
        define('PDF_AUTHOR',  self::$var['author']);
        define('PDF_HEADER_TITLE', self::$var['title']);
        define('PDF_HEADER_STRING', "by ".self::$var['author']." - ".self::$ee->config->item('site_name')."\n".parse_url(self::$ee->config->item('base_url'), PHP_URL_HOST));
        define('PDF_HEADER_LOGO_WIDTH', 30);
        define('PDF_UNIT', self::$var['unit']);
        define('PDF_MARGIN_HEADER', 5);
        define('PDF_MARGIN_FOOTER', 10); 
        define('PDF_MARGIN_TOP', 27); 
        define('PDF_MARGIN_BOTTOM', 25);
        define('PDF_MARGIN_LEFT', 15);
        define('PDF_MARGIN_RIGHT', 15);
        define('PDF_FONT_NAME_MAIN', self::$var['font-family']);
        define('PDF_FONT_SIZE_MAIN', 10);
        define('PDF_FONT_NAME_DATA', self::$var['font-family']);
        define('PDF_FONT_SIZE_DATA', 8);
        define('PDF_FONT_MONOSPACED', 'courier');
        define('PDF_IMAGE_SCALE_RATIO', 1);
        define('HEAD_MAGNIFICATION', 1.1);
        define('K_CELL_HEIGHT_RATIO', 1.25);
        define('K_TITLE_MAGNIFICATION', 1.3);
        define('K_SMALL_RATIO', 2/3);
        define('K_THAI_TOPCHARS', true);
        define('K_TCPDF_CALLS_IN_HTML', true);
        
        include_once($tcpdf_path.'config'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.self::$var['language'].'.php');
        include_once($tcpdf_path.'tcpdf.php');
        
        $cache = (self::$var['cache'] != 'y') ? FALSE : TRUE;
        $pdf   = new TCPDF(self::$var['orientation'], self::$var['unit'], self::$var['format'], true, 'UTF-8', $cache);
        log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Created a new TCPDF object');  
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(self::$var['author']);
        $pdf->SetTitle(self::$var['title']);
        if(isset(self::$var['subject']))  $pdf->SetSubject(self::$var['subject']);
        if(isset(self::$var['keywords'])) $pdf->SetKeywords(self::$var['keywords']);
        
        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
  
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  
        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  
        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
  
        //set some language-dependent strings
        $pdf->setLanguageArray($l);
        
        $pdf->SetFont(self::$var['font-family'], null, self::$var['font-size']);
  
        // add a page 
        $pdf->AddPage();
        $pdf->writeHTML($EE->output->final_output, true, 0, true, 0);
        $pdf->Output(str_replace(chr(32), '-', self::$var['title']).'.pdf', self::$var['save']);
        
        log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Sent PDF to client ...');
        log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Memory usage: '.(( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB'));
        log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Elapsed time: '.$GLOBALS['BM']->elapsed_time('total_execution_time_start', 'total_execution_time_end'));  
        
        exit; // simple "return" has problems?
      }
       
      if($this->isFinalHook() == TRUE)
      {
        log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} No pdf request, send simple web page (html, js, css, ecc.) because I am the last hook in the list');  
        
        return $EE->output->_display($EE->output->final_output);  
      }
      
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} No pdf request, do nothing because I am NOT the last hook in the list');  
      
    } // end funct
    
    // --------------------------------------------------------------------
    
    # show usage
    public function usage()
    {
      ob_start(); 
      ?>
        How to use this plugin:
        
        <!-- this simple template file is converting in PDF format -->
        <h1>hello world</h1>
        <p>This is <b>Awesome!</b>(or not?)</p>
        <img src="http://example.com/image.jpg" />
        {exp:eei_tcpdf:params 
          logo="age38dev.jpg" 
          cache="y"
          language="ita" 
          title="This is the title" 
          author="Massimiliano Lombardi" 
          subject="Hi Man" 
          keywords="Some Words"}
        {!-- this tag can be placed every where in template file --}
        {!-- use cache param equal to "y" just when you do not change page content --}
        
        Please *** read *** also the online: http://www.age38dev.com/user_guide/eei_tcpdf.html
        
      <?php
      $buffer = ob_get_contents();
    
      ob_end_clean(); 
  
      return $buffer;
      
    } // end funct
    
    // --------------------------------------------------------------------
    
    # try to guess if I am the last hook in the list
    private function isFinalHook($key = 'display_override')
    {
      // load the hook config file
      $config = str_replace('third_party'.DIRECTORY_SEPARATOR.strtolower(get_class($this)), '', dirname(__FILE__)).'config'.DIRECTORY_SEPARATOR.'hooks.php';
      require $config;                                                                                                                                      
      
      if(isset($hook[$key][0]))
      {
        krsort($hook[$key]);
        $i = key($hook[$key]);
          
        return ($hook[$key][$i]['class'] == get_class($this)) ? TRUE : FALSE;
      }
      
      return TRUE;
      
    } // end funct
    
    // --------------------------------------------------------------------
    
    # constructor, set internal static vars
    private function initialize()
    {
      log_message('debug', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} Simil constructor class start');
      
      if(is_null(self::$ee))
      {
        self::$ee =& get_instance();
      }
      
      // prevent double calling @NOTE: I do not think this is so usefull, anyway check the same
      return (sizeof(self::$var) > 0) ? TRUE : FALSE;
      
    } // end funct
    
    // --------------------------------------------------------------------
    
    # check for available font-family
    private function adjustFontFamily($arg1)
    {
      $arg1 = strtolower($arg1);
      
      // list of available fonts
      $available = self::fontsAvailable($this);
      
      return (in_array($arg1, $available)) ? $arg1 : 'times';
        
    } // end funct
    
    // --------------------------------------------------------------------
    
    # get current fonts list availables on tcpdf/fonts folder 
    private static function fontsAvailable($me = NULL)
    {
      if(is_null($me)) $me = new EEI_Tcpdf;
      
      // load depencies
      self::$ee->load->helper('directory');
      
      $fontdir  = $me->tcpdf.DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR;
      $infolder = directory_map($fontdir, 0);
      $fonts    = array();
      
      log_message('error', '{'.__CLASS__.'::'.__FUNCTION__.'::'.__LINE__.'} No fonts dir available?');
      if(!is_array($infolder)) return array();
      
      foreach($infolder as $ii)
      {
        if(is_string($ii) && pathinfo($fontdir.$ii, PATHINFO_EXTENSION) == 'php' && stristr($ii, 'uni2cid') == FALSE) 
        {
          $fonts[] = strtolower(str_replace('.php', '', $ii));
        }
      }
        
      return $fonts;  
      
    } // end funct
    
    // --------------------------------------------------------------------
    
    # show in CP which fonts are availables
    public static function usageFontsList()
    {
      $fonts = self::fontsAvailable();
      if(sizeof($fonts) > 0)
      {
        return implode(', ', $fonts);
      }
      
      return '<span style="color:red; font-style:italic">
                Attention: I am not able to find which fonts are avalaibles 
                (please double check your current TCPDF lib. installation) ...
              </span>';
      
      
    } // end funct
    
    // --------------------------------------------------------------------
    
    # some checking
    public static function usageWarning()
    {
      $me = new EEI_Tcpdf;
      
      // check if cache folder is writeable
      if(!is_writable($me->tcpdf.DIRECTORY_SEPARATOR.'cache')) 
      {
        $warning[] = '<span style="color:red">The <font style="background-color:yellow;padding:5px">'.
                     $me->tcpdf.DIRECTORY_SEPARATOR.'cache'.
                     '</font> folder seems not writable!</span>';
      }
      
      if(!isset($warning)) return 'All seems fine.';
      
      return implode(' / ', $warning);
      
    } // end funct
    
    // --------------------------------------------------------------------
    
  }
  // END CLASS
  
  /* End of file pi.eei_tcpdf.php */
  /* Location: ./system/expressionengine/third_party/eei_tcpdf/pi.eei_tcpdf.php */
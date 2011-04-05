<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

// if there's still another hook for the same "hook point"
$hook['display_override'] = array(
  'class'    => 'EEI_Tcpdf',
  'function' => 'output',
  'filename' => 'pi.eei_tcpdf.php',
  'filepath' => 'third_party/eei_tcpdf'
);


/* End of file hooks.php */
/* Location: ./system/expressionengine/config/hooks.php */
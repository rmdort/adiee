<?php
$config['name']='Deeploy Helper';
$config['version']='2.0.2';
$config['nsm_addon_updater']['versions_xml']='http://www.hopstudios.com/software/versions/deeploy_helper/';

// Version constant
if (!defined("VERSION")) {
	define('VERSION', $config['version']);
}
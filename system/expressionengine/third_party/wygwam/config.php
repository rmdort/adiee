<?php

if (! defined('WYGWAM_NAME'))
{
	define('WYGWAM_NAME', 'Wygwam');
	define('WYGWAM_VER',  '2.1.7');
	define('WYGWAM_DESC', 'Wysiwyg editor powered by CKEditor 3.4 and CKFinder 2.0.1');
	define('WYGWAM_DOCS', 'http://pixelandtonic.com/wygwam/docs');
}

// NSM Addon Updater
$config['name'] = WYGWAM_NAME;
$config['version'] = WYGWAM_VER;
$config['nsm_addon_updater']['versions_xml'] = 'http://pixelandtonic.com/wygwam/releasenotes.rss';

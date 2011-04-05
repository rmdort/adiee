<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bn_edit_menu_acc
{
	var $name = 'BN Edit Menu';
	var $id = 'bn_edit_menu_acc';
	var $version = '1.0';
	var $description = 'Displays your channels in the Content > Edit menu.';
	var $sections = array();

	function Bn_edit_menu_acc()
	{
		$this->EE =& get_instance();
	}
	
	function set_sections()
	{
		$this->EE->load->library('javascript');
		
		$this->EE->javascript->output('
			$("#navigationTabs").children("li.parent").each(function(){
				if ($(this).children("a:first").html() == "Content") {
					$(this).children("ul:first").children("li").eq(1).addClass("parent").append($(this).children("ul:first").children("li").eq(0).children("ul").clone(true)).find("a").each(function(){
						$(this).attr("href", $(this).attr("href").replace(/content_publish&(amp;)?M=entry_form/, "content_edit"));
					});
				}
			});
		');
		
		$this->EE->javascript->output('$(".bn_edit_menu_acc").parent().hide();');
	}
}
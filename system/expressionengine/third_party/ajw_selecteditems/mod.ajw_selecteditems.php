<?php

class Ajw_selecteditems {

	var $return_data    = ''; 

	function Ajw_selecteditems()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();

		$id = $this->EE->TMPL->fetch_param('id'); 
		$name = $this->EE->TMPL->fetch_param('name'); 

		$sql = "SELECT ajw_selecteditems_cache 
			FROM exp_ajw_selecteditems 
			WHERE ajw_selecteditems_id = \"" . $id . "\" 
			OR ajw_selecteditems_name = \"" . $name . "\" LIMIT 1";
		
		$query = $this->EE->db->query($sql); 

		$tagdata = $this->EE->TMPL->tagdata;

		$count = 0;
		if ($query->num_rows() > 0) {

			$row = $query->row();
			$tagdata = $this->EE->TMPL->swap_var_single("ajw_selecteditems", $row->ajw_selecteditems_cache, $tagdata);
			$this->return_data = $tagdata;

		} else {
			
			$tagdata = $this->EE->TMPL->swap_var_single("ajw_selecteditems", '0', $tagdata);
			$this->return_data = $tagdata;
			
		}

	}

}

/* End of file mod.ajw_selecteditems.php */
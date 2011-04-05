<?php

class Ajw_selecteditems_mcp {
	
	var $version = '2.0';
	
	function Ajw_selecteditems_mcp() {
		$this->EE =& get_instance();
	}
	
	function index() {
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('ajw_selecteditems_module_name')); 

		$this->EE->load->library('table');
		$this->EE->load->helper('form');

		$this->EE->load->library('javascript'); 
		$this->EE->javascript->output($this->EE->jquery->corner('.cp_button a')); 
		$this->EE->javascript->compile(); 

		$vars["items"] = array();
		
		$query = $this->EE->db->get('exp_ajw_selecteditems');
		foreach($query->result_array() as $row) 
		{ 
			$vars["items"][$row["ajw_selecteditems_id"]]['id'] = $row["ajw_selecteditems_id"];
			$vars["items"][$row["ajw_selecteditems_id"]]['name'] = $row["ajw_selecteditems_name"];
			$vars["items"][$row["ajw_selecteditems_id"]]['description'] = $row["ajw_selecteditems_description"];
			$vars["items"][$row["ajw_selecteditems_id"]]['edit_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_selecteditems'.AMP."method=edit".AMP."id=".$row["ajw_selecteditems_id"];
			$vars["items"][$row["ajw_selecteditems_id"]]['delete_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_selecteditems'.AMP."method=delete".AMP."id=".$row["ajw_selecteditems_id"];
		}

		$vars['form_action'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_selecteditems'.AMP.'method=create';

		return $this->EE->load->view('index', $vars, TRUE); 
	}

	function edit() {
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('ajw_selecteditems_module_name') . " - Edit"); 
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_selecteditems', $this->EE->lang->line('ajw_selecteditems_module_name')); 

		$this->EE->load->library('table');
		$this->EE->load->helper('form');
	

		$this->EE->load->library('javascript'); 
		
		$url = str_replace('&amp;', '&', BASE).'&C=addons_modules&M=show_module_cp&module=ajw_selecteditems&method=refresh';
		$url = $url . "&ajax=true";
		
		$this->EE->javascript->output(
			array(
				'$( "#refresh" ).click( function() {
							refresh();
							return false;
						});
						$( "#up" ).click( function() {
							object = document.getElementById("selected");
							var sel = object.selectedIndex;
							if( sel > 0 ) {
								var temp_value = object.options[ sel ].value;
								var temp_text = object.options[ sel ].text;
								object.options[ sel ].value = object.options[ sel-1 ].value;
								object.options[ sel ].text = object.options[ sel-1 ].text;
								object.options[ sel-1 ].value = temp_value;
								object.options[ sel-1 ].text = temp_text;
								object.selectedIndex = sel-1;
								updateList();
							}
							return false;
						});
						$( "#down" ).click( function() {
							object = document.getElementById("selected");
							var sel = object.selectedIndex;
							if( object.options.length > sel+1 ) {
								var temp_value = object.options[ sel ].value;
								var temp_text = object.options[ sel ].text;
								object.options[ sel ].value = object.options[ sel+1 ].value;
								object.options[ sel ].text = object.options[ sel+1 ].text;
								object.options[ sel+1 ].value = temp_value;
								object.options[ sel+1 ].text = temp_text;
								object.selectedIndex = sel+1;
								updateList();
							}
							return false;
						});
						$( "#add" ).click( function() {
							sourceList = document.getElementById("available");
							selectedList = document.getElementById("selected");
							if ( sourceList.selectedIndex >= 0 ) {
								selectedList.selectedIndex = -1;
								for (var i=0; sourceList.options.length > i; i++) {
									if (sourceList.options[i].selected) {
										var newoption = new Option(sourceList.options[i].text, sourceList.options[i].value, true, true);
										newoption.selected = true;
										selectedList.options[selectedList.length] = newoption;
										// selectedList.selectedIndex = -1;
									}
								}
								updateList();
								refresh();
							}
							return false;
						});
						$( "#remove" ).click( function() {
							sourceList = document.getElementById("available");
							selectedList = document.getElementById("selected");
							if ( selectedList.selectedIndex >= 0 ) {
								for (var i=selectedList.options.length-1; i>=0; i--) {
									if (selectedList.options[i].selected) {
										selectedList.options[i] = null;
									}
								}    			
								selectedList.selectedIndex = -1;
								updateList();
								refresh();
							}
							return false;
						});
						function refresh() {
							$.ajax({
									type: "POST",
									url: "'.$url.'",
									data: $("#ajw_selecteditems_form").serialize(),
									success: function(msg){
										sourceList = $("#available");
										sourceList.html(msg);
									}
								});
						}
						function updateList() {
							selectedList = document.getElementById("selected");
							var temp = "";
							for (var i=0; selectedList.options.length > i; i++) {
								if ( temp != "" ) { temp += "|" }
								temp += selectedList.options[i].value;
							}
							$("input[name=ajw_selecteditems_cache]").val( temp );
						}
					'
			)
		); 
		$this->EE->javascript->compile(); 

				
		$id = $this->EE->input->get("id");

		$this->EE->db->select('*');
		$query = $this->EE->db->get_where('exp_ajw_selecteditems', array('ajw_selecteditems_id' => $id));

		$vars = $query->row_array();

		$vars['message'] = "";
		if( $this->EE->input->get("saved") ) {
			$vars['message'] = "Updated";
		}
		
		$vars['form_action'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_selecteditems'.AMP.'method=update';		

		$this->EE->db->where_in('entry_id', explode("|", $vars["ajw_selecteditems_cache"]));
		$query = $this->EE->db->get('exp_channel_titles');

		$options = array();
		foreach($query->result_array() as $row) {
			$options[$row["entry_id"]] = $row["title"];
		}
		
		$selected = array();
		if( $vars["ajw_selecteditems_cache"] != "" ) {
			foreach( explode("|", $vars["ajw_selecteditems_cache"] ) as $value ) {
				if( isset( $options[ $value ] ) ) {
					$selected[ $value ] = $options[ $value ];
				}
			}
		}

		$vars['selected'] = $selected;

		$query = $this->EE->db->get('exp_channels');
		$channels = array();
		$channels[] = "Filter by channel";
		foreach($query->result_array() as $row) {
			$channels[$row["channel_id"]] = $row["channel_title"];
		}
		$vars['channels'] = $channels;

		$vars['available'] = $this->refresh( $vars["ajw_selecteditems_cache"], $vars["ajw_selecteditems_orderby"], $vars["ajw_selecteditems_channels"] );

		return $this->EE->load->view('edit', $vars, TRUE); 
	}
	
	function update() {
				
		$data = array(
			'ajw_selecteditems_name' => $this->EE->input->get_post("ajw_selecteditems_name"),
			'ajw_selecteditems_description' => $this->EE->input->get_post("ajw_selecteditems_description"),
			'ajw_selecteditems_cache' => $this->EE->input->get_post("ajw_selecteditems_cache")
			);
		
		if( $this->EE->input->get_post("add") != "" ) {
			if ( $this->EE->input->get_post("available") != "" ) {
				if ( $data["ajw_selecteditems_cache"] == "" ) {
					$selected = array();
				} else {
					$selected = explode("|", $data["ajw_selecteditems_cache"]);
					
				}
				$selected[] = $this->EE->input->get_post("available");
				$data["ajw_selecteditems_cache"] = implode("|", $selected);
			}
		}

		if( $this->EE->input->get_post("remove") != "" ) {
			if ( $this->EE->input->get_post("remove") != "" ) {
				$selected = explode("|", $data["ajw_selecteditems_cache"]);
				foreach( $selected as $key => $value ) {
					if ( $value == $this->EE->input->get_post("selected") ) {
						unset($selected[$key]);
					}
				}
				$data["ajw_selecteditems_cache"] = implode("|", $selected);
			}
		}

		if( $this->EE->input->get_post("down") != "" ) {
			if ( $this->EE->input->get_post("down") != "" ) {
				$selected = explode("|", $data["ajw_selecteditems_cache"]);
				foreach( $selected as $key => $value ) {
					if ( $value == $this->EE->input->get_post("selected") ) {
						$saved = $value;
						if ( isset( $selected[ $key+1 ] ) ) {
							$selected[ $key ] = $selected[ $key+1 ];
							$selected[ $key+1 ] = $saved;
						}
					}
				}
				$data["ajw_selecteditems_cache"] = implode("|", $selected);
			}
		}

		if( $this->EE->input->get_post("up") != "" ) {
			if ( $this->EE->input->get_post("up") != "" ) {
				$selected = explode("|", $data["ajw_selecteditems_cache"]);
				foreach( $selected as $key => $value ) {
					if ( $value == $this->EE->input->get_post("selected") ) {
						$saved = $value;
						if ( isset( $selected[ $key-1 ] ) ) {
							$selected[ $key ] = $selected[ $key-1 ];
							$selected[ $key-1 ] = $saved;
						}
					}
				}
				$data["ajw_selecteditems_cache"] = implode("|", $selected);
			}
		}

		$data["ajw_selecteditems_orderby"] = $this->EE->input->get_post("orderby");
		$data["ajw_selecteditems_channels"] = $this->EE->input->get_post("channels");

		$this->EE->db->where('ajw_selecteditems_id', $this->EE->input->get_post("id"));
		$this->EE->db->update('exp_ajw_selecteditems', $data);
	
	$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_selecteditems'.AMP."method=edit".AMP."id=".$this->EE->input->get_post("id").AMP."saved=1"); 
	}

	function create() {

		$data = array(
			'ajw_selecteditems_name' => "New selection",
			'ajw_selecteditems_description' => "New description"
		);

		$this->EE->db->insert('exp_ajw_selecteditems', $data);

		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_selecteditems'); 
		
	}

	function delete() {

		$data = array(
					   'ajw_selecteditems_id' => $this->EE->input->get("id")
					);
		$this->EE->db->delete('exp_ajw_selecteditems', $data);

		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=ajw_selecteditems'); 
		
	}

	function refresh( $ignore = "", $orderby = "", $channels = "" ) {

		if( $this->EE->input->server("HTTP_X_REQUESTED_WITH") == "XMLHttpRequest" ) {
			$ignore = $this->EE->input->get_post("ajw_selecteditems_cache");
			if ( $ignore != "" ) {
				$this->EE->db->where_not_in('entry_id', explode("|", $ignore));
			}
			$channels = $this->EE->input->get_post("channels");
			if ( $channels != 0 && $channels != "" ) {
				$this->EE->db->where('channel_id', $channels);
			}
			$orderby = $this->EE->input->get_post("orderby");
			if ( $orderby != "" ) {
				$this->EE->db->order_by($orderby);
			}		
			$query = $this->EE->db->get('exp_channel_titles');
			$options = "";
			foreach($query->result_array() as $row) 
			{ 
				$options .= '<option value="'.$row["entry_id"].'">'.$row["title"]."</option>";
			}
			print $options;
			exit;

		} else {

			if ( $ignore != "" ) {
				$this->EE->db->where_not_in('entry_id', explode("|", $ignore));
			}
			if ( $channels != 0 && $channels != "" ) {
				$this->EE->db->where('channel_id', $channels);
			}
			if ( $orderby != "" ) {
				$this->EE->db->order_by($orderby);
			}		
			$query = $this->EE->db->get('exp_channel_titles');
			$options = array();
			foreach($query->result_array() as $row) {
				$options[$row["entry_id"]] = $row["title"];
			}
			return $options;

		}
	}		
}

/* End of file mcp.ajw_selecteditems.php */
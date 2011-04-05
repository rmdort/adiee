<style type="text/css">
	#available, #selected {
		width: 300px;
	}
	.button {
		width: 64px;
	}
</style>

<?php if ($message != ''):?>
	<p class="notice"><?= $message?></p>
<?php endif;?>

<?= form_open($form_action, array('id' => 'ajw_selecteditems_form'), array( "id" => $ajw_selecteditems_id ))?>

<?php

		$this->table->set_template($cp_table_template);

		$this->table->set_heading("Information", "Value");
		$this->table->add_row(
			form_label("Name"), 
			form_input("ajw_selecteditems_name", $ajw_selecteditems_name)
		);
		$this->table->add_row(
			form_label("Description"), 
			form_input("ajw_selecteditems_description", $ajw_selecteditems_description) .
			form_hidden("ajw_selecteditems_cache", $ajw_selecteditems_cache, ' id="ajw_selecteditems_cache"')
		);
		/*
		$this->table->add_row(
			"Items", 
			form_input("ajw_selecteditems_cache", $ajw_selecteditems_cache, ' id="ajw_selecteditems_cache"')
		);
		*/

?>
<?=$this->table->generate();?>

<?php

		$this->table->clear();
		// $this->table->set_template($cp_table_template);

		$this->table->set_heading("Available entries", "&nbsp;", "Selected entries");
		$this->table->add_row(
			form_dropdown("available", $available, array(), 'id="available" multple="multiple" size="10"'),
			form_submit("add", "Add", ' id="add" class="submit"') . BR . BR . form_submit("remove", "Remove", ' id="remove" class="submit"'),
			form_dropdown("selected", $selected, array(), 'id="selected" multple="multiple" size="10"')
		);
	$this->table->add_row(
		form_label("Channel:") . NBS . form_dropdown("channels", $channels, $ajw_selecteditems_channels, 'id="channels"') . BR . 
		form_label("Order by:") . NBS . form_dropdown("orderby", array("entry_id" => "Entry Id", "entry_date desc" => "Date (newest first)", "entry_date asc" => "Date (oldest first)", "title" => "Title"), $ajw_selecteditems_orderby, 'id="orderby"') . BR . BR .
		form_submit("refresh", "Update", ' id="refresh" class="submit"'),
		'&nbsp;',
		form_submit("up", "Up", ' id="up" class="submit"') . BR . BR . form_submit("down", "Down", ' id="down" class="submit"')
	);

?>
<?=$this->table->generate();?>

<?=form_submit(array('name' => 'submit', 'value' => "Save", 'class' => 'submit'));?>

<?= form_close()?>

<?php
		$this->table->set_template($cp_table_template);
		$this->table->set_heading(
			"Id",
			"Name",
			"Description",
			""
		);

		foreach($items as $item)
		{
			$this->table->add_row(
				$item['id'],
				'<a href="'.$item['edit_url'].'">'.$item['name']."</a>",
				$item['description'],
				'<a class="delete" href="'.$item['delete_url'].'">Delete</a>'
			);
		}

?>
<?=$this->table->generate();?>
<?= form_open($form_action, array('id' => 'ajw_selecteditems_form'))?>
<?= form_submit(array('name' => 'submit', 'value' => "Create new selection", 'class' => 'submit'));?>
<?= form_close()?>

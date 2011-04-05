<?php // $vars members are first-class variables here ?>

<?php if ($message != ''):?>
	<p style="margin-bottom: 20px;" class="notice"><?= $message?></p>
<?php endif;?>

		<script type="text/javascript">
			jQuery(document).ready(function(){
				
				jQuery('form input').change(function(){
					jQuery(this).css('color', '#red');
				});

				jQuery('#quick_replace').click(function(){
					jQuery('form input').each(function(){
						var old = jQuery(this).val();
						jQuery(this).val(jQuery(this).val().replace(jQuery('#find').val(), jQuery('#replace').val()));
						if (jQuery(this).val() != old)
						{
							jQuery(this).change();
						}	
					});
				});
			});
		</script>


<?php
// Search and replace table
$this->table->set_template($cp_table_template);

$this->table->set_heading(lang('quick_replace'),'');

	$this->table->add_row(lang('current_document_root'), $_SERVER["DOCUMENT_ROOT"]);
	$this->table->add_row(lang('find_text'), form_input(array('name' => 'find', 'id' => 'find', 'value' => '', 'maxlength' => '100', 'size' => '75', 'class'=>'fullfield', 'onchange' => "this.style.color='#ff1212';")));
	$this->table->add_row(lang('replace_text'), form_input(array('name' => 'replace', 'id' => 'replace', 'value' => '', 'maxlength' => '100', 'size' => '75', 'class'=>'fullfield', 'onchange' => "this.style.color='#ff1212';")));
	$this->table->add_row('', form_submit(array('name' => 'quick_replace', 'id' => 'quick_replace', 'value' => lang('quick_replace'), 'class' => 'submit')));

$this->table->set_template($cp_table_template);
?>

<?=$this->table->generate();?>

<?=form_open($form_action, '', '')?> 

<?php
// settings table
$this->table->clear();
$this->table->set_template($cp_table_template);

$this->table->set_heading(array_values($table_heading2));

foreach ($table_rows as $row)
{
	// section rows are bold
	if (isset($row['section']))
	{
		$setting_name = '<strong>'.$row['section'].'</strong>';
		$setting_value = '';
		if (stristr($row['section'],'config')) { $setting_value = '<a href="' . BASE.AMP.'D=cp'.AMP.'C=admin_system'.AMP.'M=config_editor' . '">' . lang('edit_config'). '</a>'; }
	}
	// read-only rows don't get an input box
	elseif (isset($row['read_only']))
	{
		$setting_name = $row['label'];
		$setting_value = $row['value'];
	}
	else 
	{
		$setting_name = $row['label'];
		$setting_value = form_input(array('name' => $row['name'], 'id' => $row['name'], 'value' => $row['value'], 'maxlength' => '100', 'size' => '75', 'class'=>'fullfield', 'onchange' => "this.style.color='#ff1212';"));
	}

	$this->table->add_row($setting_name, $setting_value);
}

	$this->table->add_row('', form_submit(array('name' => 'submit', 'id' => 'submit', 'value' => lang('update'), 'class' => 'submit')));
?>

<?=$this->table->generate();?>

<?php if ($message == ''):?>
	<p style="margin-bottom: 20px;"><?= $pitch?></p>
<?php endif;?>


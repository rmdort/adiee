<?php

$this->table->set_template($cp_table_template);
$this->table->set_heading(array('colspan'=>3, 'data'=>lang('wygwam_editor_configs')));

foreach ($configs as $config)
{
	$this->table->add_row(
		'<a href="'.BASE.AMP.$base.AMP.'method=config_edit'.AMP.'config_id='.$config['config_id'].'">'.$config['config_name'].'</a>',
		array('width'=>'15%', 'data'=>'<a href="'.BASE.AMP.$base.AMP.'method=config_edit'.AMP.'config_id='.$config['config_id'].AMP.'clone=y">'.lang('wygwam_clone').'</a>'),
		array('width'=>'15%', 'data'=>'<a href="'.BASE.AMP.$base.AMP.'method=config_delete_confirm'.AMP.'config_id='.$config['config_id'].'">'.lang('delete').'</a>')
	);
}

echo $this->table->generate();

if (! $configs)
{
	echo '<p>'.lang('wygwam_no_configs').'</p>';
}

?>

<p style="margin-bottom: 2em;">
	<a class="submit" href="<?php echo BASE.AMP.$base.AMP ?>method=config_edit"><?php echo lang('wygwam_create_config') ?></a>
</p>


<?php echo form_open($base.AMP.'method=save_license_key') ?>

<h3><?php echo lang('wygwam_license_key') ?></h3>

<p>
	<?php echo form_input('license_key', $license_key, 'style="width: 40em;"') ?>
	<?php echo form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit')) ?>
</p>

<?php echo form_close() ?>

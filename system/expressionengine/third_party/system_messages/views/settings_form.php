<style type="text/css">
#csm_settings tr:first-child .csm_remove_row { display: none; }
.csm_add_row { float: right; font-weight: bold; display: inline-block; padding: 5px 12px }
.csm_remove_row { float: right; }
</style>

<?php echo form_open('C=addons_extensions'.AMP.'M=save_extension_settings', 'id="csm_settings"', $hidden)?>
	
<?php
$this->table->set_template($cp_table_template);
$this->table->set_heading(
	array('data' => lang('preference'), 'style' => 'width:50%;'),
	lang('setting')
);

foreach ($fields as $field)
{
	$this->table->add_row(
		"<strong>{$field['label']}</strong>".((isset($field['detail'])) ? "<div class='subtext'>{$field['detail']}</div>" : ''),
		form_dropdown($field['name'], $field['options'], $field['selected'], 'id="'.$field['name'].'"')
	);
}

echo $this->table->generate();
$this->table->clear();

$this->table->set_template($cp_table_template);
$this->table->set_heading(
    array('data' => lang('custom_actions_header'))
);
$this->table->add_row(
    '<p style="margin-bottom: 12px">'. lang('custom_actions_detail') .'</p>'
);

echo $this->table->generate();
$this->table->clear();

echo '<div class="custom_actions">';
$this->table->set_template($cp_table_template);
$this->table->set_heading(
	array('data' => lang('action'), 'style' => 'width:50%;'),
	lang('template')
);

foreach ($action_fields as $k => $field)
{
	$this->table->add_row(
		form_dropdown($field['name'], $field['options'], $field['selected'], 'id="'.$field['name'].'"'),
		form_dropdown($action_templates[$k]['name'], $action_templates[$k]['options'], $action_templates[$k]['selected']) . '<a href="#" class="csm_remove_row" rel="custom_actions">Remove</a>'
	);
}

echo $this->table->generate();
echo '</div>';

echo '<a href="#" class="csm_add_row" rel="custom_actions">+ Add</a>';
?>

<script type="text/javascript">
jQuery(function($){
    
    $('.csm_add_row').click(function(e){
        var regex = /(\[\d+\])/g; 
        var rel = $(this).attr('rel');
        var table = $('.'+ rel +' .mainTable tbody');
        var tr = table.find('tr:last-child').clone(true);
        var row = tr.html();
        var index = table.find('tr').length;
    
        if(tr.hasClass('even')){
            var cssclass = 'odd';
        } else {
            var cssclass = 'even';
        }
    
        row = row.replace(regex, '[]');
        table.append('<tr id="'+ rel + index +'" class="'+ cssclass +'">'+ row +'</tr>');
    
        /* Remove all selections from the duplicated select */
        $('#'+ rel + index).find('select').val('');
        
        e.preventDefault();
    });
    
    $('.csm_remove_row').live('click', function(e){
        var rel = $(this).attr('rel');
        $(this).closest('tr').remove();
        
        e.preventDefault();
    });
});
</script>

<p class="centerSubmit"><?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'))?></p>

<?=form_close()?>
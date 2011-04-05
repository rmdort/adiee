<?php if($message) : ?>
<div class="mor alert success">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>

<?php if($settings_form) : ?>
<?= form_open(
'C=addons_extensions&M=extension_settings&file=&file=mx_title_control',
'',
array("file" => "mx_title_control")
)
?>

		
<table class="mainTable padTable" id="event_table" border="0" cellpadding="0" cellspacing="0">

<tbody>
<tr class="header">
<th  colspan="3">
</th>
</tr>

<tr>
<td class="default" colspan="3">
<div class="box" style="border-width: 0pt 0pt 1px; margin: 0pt; padding: 10px 5px;"><p><?= lang('extension_settings_info')?></p></div>
</td>
</tr>
</tbody> <?php endif; ?>
<tbody>

		
		<?php
				$out="";
					foreach ($language_packs as $language)
				{
					$i	=	1;
					$out .= '<tr class="header"><th>'. $language.'</th><th>'.lang('title').'</th><th>'.lang('url_title').'</th></tr>';
					foreach ($channel_data as $channel)
					{
						$out .= '<tr class="'.(($i&1) ? "odd" : "even").'">
						<td><strong>'.$channel->channel_title.'</strong></td>
						<td><input dir="ltr" style="width: 100%;" name="'.$input_prefix.'[title_'.strtolower($language).'_'.$channel->channel_id.']" id="" value="'.((isset($settings['title_'.strtolower($language).'_'.$channel->channel_id])) ? $settings['title_'.strtolower($language).'_'.$channel->channel_id] : '').'" size="20" maxlength="120" class="input" type="text"></td>
						<td><input dir="ltr" style="width: 100%;" name="'.$input_prefix.'[url_title_'.strtolower($language).'_'.$channel->channel_id.']" id="" value="'.((isset($settings['url_title_'.strtolower($language).'_'.$channel->channel_id])) ? $settings['url_title_'.strtolower($language).'_'.$channel->channel_id] : '').'" size="20" maxlength="120" class="input" type="text"></td>
						</tr>';
						$i++;
					}		

				}				
			echo $out;

		?>	
		
</tbody>		

<tbody>
<tr class="header" >
<th colspan="3"><?= lang('multilanguage_settings_info')?></th>

</tr>
<tr>

<td><?= lang('multilanguage')?></td>
 <td colspan="2">
<select name="<?= $input_prefix ?>[multilanguage]" id='multilanguage' >
<option value="y" <?= (isset($settings['multilanguage'])) ? (($settings['multilanguage'] == 'y') ? " selected='selected'" : "" ) : "" ?>><?= lang('enable') ?></option>
<option value="n" <?= (isset($settings['multilanguage'])) ? (($settings['multilanguage'] == 'n') ? " selected='selected'" : "") : "" ?>><?= lang('disable') ?></option>
</select>
  

 
</td></tr>
</tbody>
<tbody>
<tr class="header" >
<th colspan="3"><?= lang('titles_length_control')?></th>

</tr>
<tr>
<td><?= lang('title_max_length')?></td>
 <td colspan="2">
	<input dir="ltr" style="width: 100%;" name="<?=$input_prefix?>[max_title]" id="" value="<?=((isset($settings['max_title'])) ? $settings['max_title'] : '100')?>" size="20" maxlength="120" class="input" type="text">
 </td>
 </tr>
 <tr>
<td><?= lang('url_title_max_length')?></td>
 <td colspan="2">	<input dir="ltr" style="width: 100%;" name="<?=$input_prefix?>[max_url_title]" id="" value="<?=((isset($settings['max_url_title'])) ? $settings['max_url_title'] : '75')?>" size="20" maxlength="120" class="input" type="text"></td> 
 </tr>
</tbody>			
</table>
<p class="centerSubmit"><input name="edit_field_group_name" value="<?= lang('save_extension_settings'); ?>" class="submit" type="submit"></p>


<?= form_close(); ?>


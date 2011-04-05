<span id="tb-<?php echo $id ?>" class="cke_skin_wygwam2">
<span class="cke_browser_gecko">
<span class="cke_wrapper cke_ltr">
<table class="cke_editor cke_ltr">
<tr>
<td class="cke_top">
<div class="cke_toolbox">

	<?php foreach($groups as $group):

		$first = $group[0];
		if (substr($first, 0, 1) == '!') $first = substr($first, 1);
		$id = 'tb-option-'.$first;

		if ($group == '/'): ?>
			<span class="cke_clear tb-option tb-duplicate tb-selected">
				<input type="hidden" name="settings[toolbar][]" value="/" <?php if (!$selections_pane) echo 'disabled' ?>>
			</span>
		<?php elseif (in_array($first, $selected_groups)): ?>
			<span id="<?php echo $id ?>-placeholder" class="tb-placeholder"></span>
		<?php else:
			$select = in_array($first, $vars['tb_selects']); ?>
			<span id="<?php echo $id ?>" class="cke_toolbar tb-option <?php if ($selections_pane) echo 'tb-selected' ?>">
				<span class="<?php echo ($select ? 'cke_rcombo' : 'cke_toolgroup') ?>">
				<?php foreach($group as $button):
						if ($disabled = substr($button, 0, 1) == '!') $button = substr($button, 1);
						$class = isset($vars['tb_class_overrides'][$button]) ? $vars['tb_class_overrides'][$button] : strtolower($button);
						$label = isset($vars['tb_label_overrides'][$button]) ? $vars['tb_label_overrides'][$button] : $button;
					?>
					<?php if ( ! $select): ?>
						<span class="cke_button<?php if ($disabled) echo ' disabled' ?>">
							<a class="cke_off cke_button_<?php echo $class ?>" title="<?php echo $label ?>">
								<span class="cke_icon"></span>
								<span class="cke_label"><?php echo $label ?></span>
							</a>
					<?php else: ?>
						<span class="cke_<?php echo $class ?> cke_off">
							<span class="cke_label"><?php echo $label ?></span>
							<a title="<?php echo $label ?>">
								<span>
									<span class="cke_accessibility"><?php echo $label ?></span>
									<span class="cke_text cke_inline_label"><?php echo $label ?></span>
								</span>
								<span class="cke_openbutton"></span>
							</a>
					<?php endif ?>
							<input type="hidden" name="settings[toolbar][]" value="<?php echo $button ?>" <?php if (!$selections_pane || $disabled) echo 'disabled' ?>>
						</span>
				<?php endforeach ?>
				</span>
			</span>
		<?php endif; ?>

	<?php endforeach; ?>

	<?php if (! $selections_pane): ?>
		<span class="cke_clear tb-option tb-duplicate">
			<input type="hidden" name="settings[toolbar][]" value="/" disabled>
		</span>
	<?php endif; ?>

</div>
</td>
</tr>
</table>
</span>
</span>
</span>

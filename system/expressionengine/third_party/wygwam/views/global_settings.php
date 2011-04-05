<?php
	global $LANG;

	function toolbar_html(&$vars, $tb_name, $tb_groups, $selections_pane = FALSE, $selected_groups = array())
	{
		foreach($tb_groups as $group):
			$first = $group[0];
			if (substr($first, 0, 1) == '!') $first = substr($first, 1);
			$id = 'dp-option-'.$tb_name.'-'.$first;

			if ($group == '/'): ?>
				<span class="cke_clear dp-option dp-duplicate dp-selected">
					<input type="hidden" name="toolbars[<?php echo $tb_name ?>][]" value="/" <?php if (!$selections_pane) echo 'disabled' ?>>
				</span>
			<?php elseif (in_array($first, $selected_groups)): ?>
				<span id="<?php echo $id ?>-placeholder" class="dp-placeholder"></span>
			<?php else:
				$select = in_array($first, $vars['tb_selects']); ?>
				<span id="<?php echo $id ?>" class="cke_toolbar dp-option <?php if ($selections_pane) echo 'dp-selected' ?>">
					<span class="<?php echo $select ? 'cke_rcombo' : 'cke_toolgroup' ?>">
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
								<input type="hidden" name="toolbars[<?php echo $tb_name ?>][]" value="<?php echo $button ?>" <?php if (!$selections_pane || $disabled) echo 'disabled' ?>>
							</span>
					<?php endforeach ?>
					</span>
				</span>
			<?php endif;
		endforeach;
	}
?>

<div id="wyg-toolbars">
	<div class="box">
		<h3 class="defaultBold"><?php echo $vars['lang']['wygwam_tbconf_label'] ?></h3>
		<p><?php echo $vars['lang']['wygwam_tbconf_desc'] ?></p>
	</div>
	<div class="wygnav">
		<ul>
			<?php foreach(array_keys($vars['toolbars']) as $tb_name): ?>
				<?php if ($tb_name != 'untitled'): ?>
					<li><a href="#wyg-tb-<?php echo $tb_name ?>"><?php echo str_replace('_', ' ', $tb_name) ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
	<div class="wygbuttons">
		<ul class="clearfix">
			<li><a class="add" title="Add a toolbar"><img src="<?php echo $vars['theme_url'] ?>images/add.gif" width="13" height="14" alt=""></a></li>
			<li><a class="remove" title="Remove this toolbar"><img src="<?php echo $vars['theme_url'] ?>images/remove.gif" width="13" height="14" alt=""></a></li>
		</ul>
	</div>

	<?php foreach($vars['toolbars'] as $tb_name => $toolbar): ?>
	<div id="wyg-tb-<?php echo $tb_name ?>" class="wygsubpane">
		<div class="wyg-toolbar cke_skin_wygwam2"><span class="cke_browser_gecko">
			<div class="dp-selections clearfix">
				<?php echo toolbar_html($vars, $tb_name, $toolbar['groups'], TRUE) ?>
			</div>

			<div class="dp-options clearfix">
				<?php echo toolbar_html($vars, $tb_name, $vars['tb_groups'], FALSE, $toolbar['flat']) ?>
				<div class="wygclear"></div>
				<span class="cke_clear dp-option dp-duplicate">
					<input type="hidden" name="toolbars[<?php echo $tb_name ?>][]" value="/" disabled>
				</span>
			</div>
		</span></div>
	</div>
	<?php endforeach ?>
</div>

<div class="box license">
	<h3 class="defaultBold"><?php echo $vars['lang']['wygwam_license_label'] ?></h3>
	<input type="text" name="license_key" value="<?php echo $vars['license_key'] ?>" size="" maxlength="" class="input" />
</div>

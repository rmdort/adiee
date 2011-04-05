<?php
if ( ! $EE_view_disable)
{
	$this->load->view('_shared/header');
	$this->load->view('_shared/main_menu');
	$this->load->view('_shared/sidebar');
	$this->load->view('_shared/breadcrumbs');
}
?>

<div id="mainContent"<?=$maincontent_state?>>
	<?php $this->load->view('_shared/right_nav')?>
	<div class="contents">

		<div class="heading">
			<h2 class="edit"><?=lang('content_files')?></h2>
		</div>
		
		<div class="pageContents group">
			<div id="file_manager_toolbar">
				<?=form_open('C=content_files'.AMP.'M=edit_image', array('id'=>'image_crop_form'), array('action'=> 'crop', 'upload_dir' => $upload_dir, 'file' => $file))?>
					<h3 class="accordion"><?=lang('crop')?></h3>
					<div id="file_manager_crop">
						<ul>
							<li>
								<?=lang('crop_width', 'crop_width')?>
								<?=form_input('crop_width', $file_info['width'], 'id="crop_width" class="crop_dim"')?>
							</li>
							<li>
								<?=lang('crop_height', 'crop_height')?>
								<?=form_input('crop_height', $file_info['height'], 'id="crop_height" class="crop_dim"')?>
							</li>
							<li>
								<?=lang('crop_x', 'crop_x')?>
								<?=form_input('crop_x', 0, 'id="crop_x" class="crop_dim"')?>
							</li>
							<li>
								<?=lang('crop_y', 'crop_y')?>
								<?=form_input('crop_y', 0, 'id="crop_y"  class="crop_dim"')?>
							</li>
						</ul>
						<p class="submit_button">
							<a href="#" id="toggle_crop" class="submit js_show"><?=lang('crop_mode')?></a>
							<?=form_submit('save_image', lang('save_image'), 'class="submit"')?><br />
							<a href="#" id="cancel_crop" style="display: none"><?=lang('cancel_changes')?></a>
						</p>
					</div>
				<?=form_close()?>
				<?=form_open('C=content_files'.AMP.'M=edit_image', array('id'=>'image_rotate_form'), 
								array('action'=> 'rotate', 'upload_dir' => $upload_dir, 'file' => $file))?>
					<h3 class="accordion"><?=lang('rotate')?></h3>
					<div id="rotate_fieldset">
						<ul>
							<li class="rotate_90">
								<label>
									<?=form_radio('rotate', '90', TRUE)?>
									<?=lang('rotate_90r')?>
								</label>
							</li>
							<li class="rotate_270">
								<label>
									<?=form_radio('rotate', '270', TRUE)?>
									<?=lang('rotate_90l')?>
								</label>
							</li>
							<li class="rotate_vrt">
								<label>
									<?=form_radio('rotate', 'vrt', TRUE)?>
									<?=lang('rotate_flip_vert')?>
								</label>
							</li>
							<li class="rotate_hor">
								<label>
									<?=form_radio('rotate', 'hor', TRUE)?>
									<?=lang('rotate_flip_hor')?>
								</label>
							</li>
						</ul>
						<p class="submit_button">
							<?=form_submit('save_image', lang('save_image'), 'class="submit"')?><br />
							<?=anchor('#', lang('cancel_changes'), 'class="disabled"')?>
						</p>
					</div>
				<?=form_close()?>
				<?=form_open('C=content_files'.AMP.'M=edit_image', array('id'=>'image_resize_form'), array('action'=> 'resize', 'upload_dir' => $upload_dir, 'file' => $file))?>
					<h3 class="accordion"><?=lang('resize')?></h3>
					<div id="resize_fieldset">
						<ul>
							<li>
								<?=lang('resize_width', 'resize_width')?>
								<?=form_input('resize_width', $file_info['width'], 'id="resize_width"')?>
							</li>
							<li>
								<?=lang('resize_height', 'resize_height')?>
								<?=form_input('resize_height', $file_info['height'], 'id="resize_height"')?>
							</li>
						</ul>
						<p class="submit_button">
							<?=form_submit('save_image', lang('save_image'), 'class="submit"')?><br />
							<?=anchor('#', lang('cancel_changes'), 'id="cancel_resize" style="display: none"')?>
						</p>
					</div>
					<div class="clear_left"></div>
				<?=form_close()?>
			</div>
			<div id="file_manager_edit_file">
				<img src="<?=$file_url?>?r=<?=$filemtime?>" <?=$file_info['size_str']?>>
			</div> <!-- #file_manager_edit_file -->
			<div class="clear"></div>
		</div>
	</div>
</div>

<?php
if ( ! $EE_view_disable)
{
	$this->load->view('_shared/accessories');
	$this->load->view('_shared/footer');
}

/* End of file file_browse.php */
/* Location: ./themes/cp_themes/default/tools/file_browse.php */
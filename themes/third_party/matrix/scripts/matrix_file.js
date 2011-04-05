(function($) {


Matrix.bind('file', 'display', function(cell){

	var $thumb = $('> div.matrix-thumb', cell.dom.$td),
		$removeBtn = $('> a', $thumb),
		$thumbImg = $('> img', $thumb),
		$filename = $('> div.matrix-filename', cell.dom.$td),

		$filedirInput = $('> input.filedir', cell.dom.$td),
		$filenameInput = $('> input.filename', cell.dom.$td),
		$fileInput = $('> input.file', cell.dom.$td),
		$addBtn = $('> a.matrix-add', cell.dom.$td);

	var id = cell.field.id+'_'+cell.row.id+'_'+cell.col.id+'_file';
	$fileInput.attr('id', id);

	var removeFile = function(){
		$thumb.remove();
		$filename.remove();
		$filedirInput.val('');
		$filenameInput.val('');
		$addBtn.show();
	};

	$removeBtn.click(removeFile);

	cell.selectFile = function(directory, name, thumb) {
	  
		// validation
		if (! (directory && name)) {
			setTimeout(function(){
				alert(Matrix.lang.select_file_error);
			}, 250);

			return;
		}

		// update the inputs
		$filedirInput.val(directory);
		$filenameInput.val(name);

		$addBtn.hide();

		// add the new dom elements
		$thumb = $('<div class="matrix-thumb" />').prependTo(cell.dom.$td);
		$removeBtn = $('<a title="'+Matrix.lang.remove_file+'" />').appendTo($thumb);
		$thumbImg = $('<img />').appendTo($thumb);
		$filename = $('<div class="matrix-filename">'+name+'</div>').appendTo(cell.dom.$td);

		$removeBtn.click(removeFile);

		// prepare to set the container's width
		$thumbImg.load(function() {
			$thumb.width($thumbImg.attr('width'));
		});

		// load the new thummb
		$thumbImg.attr('src', thumb);

		// restore everything to default state
		$.ee_filebrowser.reset();
	};

	$.ee_filebrowser.add_trigger($addBtn, id, function(file, field){
		cell.selectFile(file.directory, file.name, file.thumb);
	});

});


})(jQuery);
